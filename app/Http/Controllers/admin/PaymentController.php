<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);
        $query = Payment::with(['order.customer', 'order.user'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('order_no', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        $payments = $query->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        $payment->load(['order.items.menuItem', 'order.customer', 'order.diningTable', 'order.user']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Process a payment for an order.
     */
    public function process(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,qr,khqr',
            'paid_amount' => 'required|numeric|min:' . $order->total_amount,
            'payer_name' => 'nullable|string|max:255',
            'payer_account' => 'nullable|string|max:255',
        ]);

        $change = $request->paid_amount - $order->total_amount;

        $payment = $order->payment()->create([
            'payment_method' => $request->payment_method,
            'total_amount' => $order->total_amount,
            'paid_amount' => $request->paid_amount,
            'change_amount' => $change,
            'status' => 'paid',
            'payer_name' => $request->payer_name,
            'payer_account' => $request->payer_account,
            'paid_at' => now(),
        ]);

        $order->update([
            'status' => in_array($order->status, ['pending', 'preparing']) ? $order->status : 'completed',
            'notes' => $request->notes ?? $order->notes
        ]);
        
        session()->flash('success', 'Payment processed successfully!');

        if ($request->wantsJson()) {
            return response()->json($payment);
        }

        return redirect()->route('payments.show', $payment->id);
    }

    /**
     * Get receipt view with autoprint - new professional receipt.
     */
    public function receipt(Order $order)
    {
        $payment = $order->payment;
        if (!$payment) {
            return redirect()->back()->with('error', 'No payment found for this order.');
        }

        $payment->load(['order.items.menuItem.category', 'order.customer', 'order.diningTable', 'order.user']);
        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return view('admin.receipts.invoice', compact('payment', 'appSettings'));
    }

    /**
     * Get legacy receipt view (dashboard version).
     */
    public function legacyReceipt(Order $order)
    {
        $payment = $order->payment;
        if (!$payment) {
            return redirect()->back()->with('error', 'No payment found for this order.');
        }

        $payment->load(['order.items.menuItem', 'order.customer', 'order.diningTable', 'order.user']);
        return view('admin.payments.show', compact('payment'))->with('autoPrint', true);
    }
}
