<?php

namespace App\Http\Controllers\admin;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function income(Request $request)
    {
        $period = $request->get('period', '');
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if ($period == 'today') {
            $startDate = now()->format('Y-m-d');
            $endDate = now()->format('Y-m-d');
        } elseif ($period == 'weekly') {
            $startDate = now()->startOfWeek()->format('Y-m-d');
            $endDate = now()->format('Y-m-d');
        } elseif ($period == 'monthly') {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->format('Y-m-d');
        }

        // Default if everything is empty
        if (!$startDate) {
            $startDate = now()->startOfMonth()->format('Y-m-d');
        }

        $baseQuery = Payment::with(['order.customer'])
            ->whereBetween('paid_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        if ($request->filled('method')) {
            $baseQuery->where('payment_method', $request->method);
        }

        // Clone query for stats to avoid pagination issues
        $statsQuery = clone $baseQuery;
        $allPaymentsForStats = $statsQuery->get();

        // Statistics for ALL filtered records
        $stats = [
            'total_income' => $allPaymentsForStats->sum('total_amount'),
            'total_transactions' => $allPaymentsForStats->count(),
            'avg_ticket' => $allPaymentsForStats->count() > 0 ? $allPaymentsForStats->sum('total_amount') / $allPaymentsForStats->count() : 0,
            'by_method' => $allPaymentsForStats->groupBy('payment_method')->map(fn($group) => $group->sum('total_amount')),
        ];

        // Paginate only for the table
        $payments = $baseQuery->latest()->paginate(10)->withQueryString();

        // Chart Trend Calculation
        if ($period == 'today') {
            // Get actual sales data by hour
            $salesByHour = Payment::whereDate('paid_at', now()->toDateString())
                ->select(DB::raw("HOUR(paid_at) as hour_key"), DB::raw('SUM(total_amount) as total'))
                ->groupBy('hour_key')
                ->pluck('total', 'hour_key')
                ->toArray();

            // Generate full 24-hour trend array
            $chartTrendData = [];
            for ($h = 0; $h < 24; $h++) {
                $hourLabel = \Carbon\Carbon::createFromTime($h, 0)->format('h A'); // e.g. 09 AM
                $chartTrendData[] = (object)[
                    'label' => $hourLabel,
                    'total' => $salesByHour[$h] ?? 0
                ];
            }
            $chartTrend = collect($chartTrendData);
        } elseif ($period == 'weekly') {
            // Get last 7 days sales data
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();

            $salesByDay = Payment::whereBetween('paid_at', [$startDate, $endDate])
                ->select(DB::raw("DATE(paid_at) as date_key"), DB::raw('SUM(total_amount) as total'))
                ->groupBy('date_key')
                ->pluck('total', 'date_key')
                ->toArray();

            $chartTrendData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateKey = $date->toDateString();
                $chartTrendData[] = (object)[
                    'label' => $date->format('d M'), // e.g. 13 Apr
                    'total' => $salesByDay[$dateKey] ?? 0
                ];
            }
            $chartTrend = collect($chartTrendData);
        } elseif ($period == 'monthly') {
            // Get all days in the current month
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $daysInMonth = now()->daysInMonth;

            $salesByDay = Payment::whereBetween('paid_at', [$startDate, $endDate])
                ->select(DB::raw("DATE(paid_at) as date_key"), DB::raw('SUM(total_amount) as total'))
                ->groupBy('date_key')
                ->pluck('total', 'date_key')
                ->toArray();

            $chartTrendData = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = now()->day($d);
                $dateKey = $date->toDateString();
                $chartTrendData[] = (object)[
                    'label' => $date->format('d M'), // e.g. 01 Apr
                    'total' => $salesByDay[$dateKey] ?? 0
                ];
            }
            $chartTrend = collect($chartTrendData);
        } else {
            // Daily Trend for other periods (Monthly or Custom)
            $chartTrend = Payment::whereBetween('paid_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->select(DB::raw("DATE_FORMAT(paid_at, '%d %b') as label"), DB::raw('SUM(total_amount) as total'))
                ->groupBy('label', DB::raw('DATE(paid_at)'))
                ->orderBy(DB::raw('DATE(paid_at)'))
                ->get();
        }

        // Monthly Trend (Last 12 Months) - Always available for "Yearly/Overview" if needed
        $monthlyTrend = Payment::select(
                DB::raw("DATE_FORMAT(paid_at, '%Y-%c') as month_key"),
                DB::raw("DATE_FORMAT(paid_at, '%b %Y') as label"),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('paid_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month_key', 'label')
            ->orderBy(DB::raw("MIN(paid_at)"))
            ->get();

        // Decide which trend to use for the main chart
        // If no period is selected (Custom range) and range > 60 days, use monthly. 
        // Otherwise use the calculated chartTrend.
        $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $mainTrend = ($daysDiff > 60 && !$period) ? $monthlyTrend : $chartTrend;

        $viewData = [
            'payments' => $payments,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'mainTrend' => $mainTrend,
            'monthlyTrend' => $monthlyTrend,
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.reports.partials.income_content', $viewData)->render(),
                'chart' => [
                    'labels' => $mainTrend->pluck('label'),
                    'data' => $mainTrend->pluck('total')
                ]
            ]);
        }

        return view('admin.reports.income', $viewData);
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $payments = Payment::with(['order.customer'])
            ->whereBetween('paid_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->get();

        return Excel::download(new \App\Exports\IncomeExport($payments), "income_report_{$startDate}_to_{$endDate}.xlsx");
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $payments = Payment::with(['order.customer', 'order.user'])
            ->whereBetween('paid_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->get();

        $total = $payments->sum('total_amount');

        $pdf = Pdf::loadView('admin.reports.pdf.income', compact('payments', 'startDate', 'endDate', 'total'));
        return $pdf->download("income_report_{$startDate}_to_{$endDate}.pdf");
    }
}
