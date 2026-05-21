@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="font-size:1.4rem; color:#212529;">{{ __('Dashboard') }}</h1>
            <p class="text-muted small mb-0">
                {{ __('Welcome back') }}, <strong>{{ auth()->user()->name }}</strong>
                &middot; {{ date('M d, Y') }}
            </p>
        </div>
        @if(auth()->user()->role && in_array(auth()->user()->role->slug, ['admin', 'cashier']))
        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
            <i data-lucide="plus-circle" style="width:15px;"></i>{{ __('New Order') }}
        </a>
        @endif
    </div>

    <!-- Stat Tiles Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-tile">
                <div class="stat-icon pending">
                    <i data-lucide="clock" style="width:18px;height:18px;"></i>
                </div>
                <div class="stat-value">{{ $orderStats['pending'] }}</div>
                <div class="stat-label">{{ __('Pending') }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-tile">
                <div class="stat-icon preparing">
                    <i data-lucide="flame" style="width:18px;height:18px;"></i>
                </div>
                <div class="stat-value">{{ $orderStats['preparing'] }}</div>
                <div class="stat-label">{{ __('Preparing') }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-tile">
                <div class="stat-icon ready">
                    <i data-lucide="package-check" style="width:18px;height:18px;"></i>
                </div>
                <div class="stat-value">{{ $orderStats['ready'] }}</div>
                <div class="stat-label">{{ __('Ready') }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-tile">
                <div class="stat-icon completed">
                    <i data-lucide="check-circle-2" style="width:18px;height:18px;"></i>
                </div>
                <div class="stat-value">{{ $orderStats['completed'] }}</div>
                <div class="stat-label">{{ __('Completed') }}</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-8 col-12">
            <div class="income-tile">
                <div>
                    <div class="income-label">{{ __("Today's Income") }}</div>
                    <div class="income-value">${{ number_format($todayIncome, 2) }}</div>
                </div>
                <div class="income-icon">
                    <i data-lucide="trending-up" style="width:26px;height:26px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Table Occupancy -->
        <div class="col-lg-4">
            <div class="card border h-100" style="border-color:#dee2e6 !important; border-radius:6px;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center"
                     style="border-color:#dee2e6 !important;">
                    <div>
                        <div class="fw-semibold text-dark small">{{ __('Dining Status') }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ __('Real-time table occupancy') }}</div>
                    </div>
                </div>
                <div class="card-body p-4 text-center">
                    <!-- Gauge Ring -->
                    <div class="gauge-ring mx-auto mb-4">
                        <div class="gauge-inner">
                            <div class="gauge-num">{{ $activeTables }}</div>
                            <div class="gauge-sub">{{ __('Occupied') }}</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center gap-5">
                        <div>
                            <div class="fw-bold text-dark" style="font-size:1.3rem;">{{ $totalTables - $activeTables }}</div>
                            <div class="text-muted" style="font-size:0.75rem; font-weight:600;">{{ __('Free') }}</div>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:1.3rem;">{{ $totalTables }}</div>
                            <div class="text-muted" style="font-size:0.75rem; font-weight:600;">{{ __('Total') }}</div>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->role && in_array(auth()->user()->role->slug, ['admin', 'cashier']))
                <div class="card-footer bg-white border-top p-3 text-center" style="border-color:#dee2e6 !important;">
                    <a href="{{ route('tables.index') }}"
                       class="btn btn-outline-secondary btn-sm px-4 fw-semibold" style="border-radius:4px; font-size:0.82rem;">
                        {{ __('Manage Tables') }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Live Service Queue -->
        <div class="col-lg-8">
            <div class="card border h-100" style="border-color:#dee2e6 !important; border-radius:6px;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center"
                     style="border-color:#dee2e6 !important;">
                    <div>
                        <div class="fw-semibold text-dark small">{{ __('Live Service Queue') }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ __('Most recent orders across all stations') }}</div>
                    </div>
                    @if(auth()->user()->role && in_array(auth()->user()->role->slug, ['admin', 'cashier']))
                    <a href="{{ route('orders.index') }}"
                       class="btn btn-outline-primary btn-sm px-3 fw-semibold" style="border-radius:4px; font-size:0.8rem;">
                        {{ __('View All') }}
                    </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8f9fa; border-bottom:1px solid #dee2e6;">
                            <tr>
                                <th class="ps-4 py-3 small text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">{{ __('Order') }}</th>
                                <th class="py-3 small text-muted fw-semibold text-uppercase text-center" style="font-size:0.72rem;">{{ __('Type / Table') }}</th>
                                <th class="py-3 small text-muted fw-semibold text-uppercase text-center" style="font-size:0.72rem;">{{ __('Status') }}</th>
                                <th class="pe-4 py-3 small text-muted fw-semibold text-uppercase text-end" style="font-size:0.72rem;">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr style="border-bottom:1px solid #f1f3f5;">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="order-icon-sm">
                                            <i data-lucide="hash" style="width:13px;height:13px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark small">{{ $order->order_no }}</div>
                                            <small class="text-muted" style="font-size:0.72rem;">{{ $order->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center py-3">
                                    @php
                                    $typeIcons = [
                                        'dine_in'  => ['icon' => 'utensils',     'label' => 'Dine In'],
                                        'takeaway' => ['icon' => 'shopping-bag', 'label' => 'Takeaway'],
                                        'delivery' => ['icon' => 'truck',        'label' => 'Delivery'],
                                    ];
                                    $type = $typeIcons[$order->order_type] ?? ['icon' => 'package', 'label' => 'Order'];
                                    @endphp
                                    <span class="type-badge">
                                        <i data-lucide="{{ $type['icon'] }}" style="width:11px;height:11px;"></i>
                                        {{ $order->diningTable->name ?? $type['label'] }}
                                    </span>
                                </td>
                                <td class="text-center py-3">
                                    @php
                                    $statusMap = [
                                        'pending'   => ['cls' => 'status-pending',   'icon' => 'clock'],
                                        'preparing' => ['cls' => 'status-preparing', 'icon' => 'flame'],
                                        'ready'     => ['cls' => 'status-ready',     'icon' => 'bell'],
                                        'completed' => ['cls' => 'status-done',      'icon' => 'check-circle'],
                                        'cancelled' => ['cls' => 'status-cancelled', 'icon' => 'x-circle'],
                                    ];
                                    $sc = $statusMap[$order->status] ?? $statusMap['pending'];
                                    @endphp
                                    <span class="order-status {{ $sc['cls'] }}">
                                        <i data-lucide="{{ $sc['icon'] }}" style="width:11px;height:11px;"></i>
                                        {{ strtoupper($order->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end py-3">
                                    <div class="fw-bold text-dark small">${{ number_format($order->total_amount, 2) }}</div>
                                    <small class="text-muted" style="font-size:0.72rem;">{{ $order->items->count() }} {{ __('items') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-5">
                                    <div class="text-center">
                                        <i data-lucide="inbox" style="width:40px;height:40px;color:#ced4da;"></i>
                                        <p class="text-muted mt-3 small fw-semibold mb-0">{{ __('No live orders') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }

    /* ── Stat Tiles ── */
    .stat-tile {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 20px 16px;
        text-align: center;
        transition: box-shadow 0.15s;
    }
    .stat-tile:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
    .stat-icon {
        width: 42px; height: 42px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px; color: #fff;
    }
    .stat-icon.pending   { background: #f59e0b; }
    .stat-icon.preparing { background: #3b82f6; }
    .stat-icon.ready     { background: #10b981; }
    .stat-icon.completed { background: #6366f1; }
    .stat-value { font-size: 1.8rem; font-weight: 700; color: #212529; line-height: 1; margin-bottom: 4px; }
    .stat-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: #6c757d; letter-spacing: 0.04em; }

    /* ── Income Tile ── */
    .income-tile {
        background: #0d6efd;
        border-radius: 6px;
        padding: 20px 24px;
        height: 100%;
        min-height: 112px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #fff;
    }
    .income-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85; margin-bottom: 6px; }
    .income-value { font-size: 2rem; font-weight: 700; line-height: 1; }
    .income-icon {
        width: 52px; height: 52px; border-radius: 12px;
        background: rgba(255,255,255,0.18);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── Gauge ── */
    .gauge-ring {
        width: 150px; height: 150px; border-radius: 50%;
        border: 12px solid #eff6ff;
        display: flex; align-items: center; justify-content: center;
    }
    .gauge-inner { text-align: center; }
    .gauge-num { font-size: 2.2rem; font-weight: 700; color: #0d6efd; line-height: 1; }
    .gauge-sub { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: #6c757d; margin-top: 2px; }

    /* ── Order Icon ── */
    .order-icon-sm {
        width: 30px; height: 30px; border-radius: 6px; flex-shrink: 0;
        background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe;
        display: flex; align-items: center; justify-content: center;
    }

    /* ── Type Badge ── */
    .type-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 4px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        font-size: 0.72rem; font-weight: 600; color: #475569;
        text-transform: uppercase;
    }

    /* ── Order Status ── */
    .order-status {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 8px; border-radius: 20px;
        font-size: 0.68rem; font-weight: 700;
    }
    .status-pending   { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
    .status-preparing { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .status-ready     { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-done      { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-cancelled { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    /* ── Button overrides ── */
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-primary { border-radius: 4px; }
    .btn-outline-secondary { border-radius: 4px; }
    .table-hover tbody tr:hover td { background-color: #f8f9fb; }
</style>
@endsection
