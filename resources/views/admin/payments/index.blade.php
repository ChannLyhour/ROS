@extends('layouts.app')

@section('title', __('Payment History'))

@section('content')
<x-master-table
    title="{{ __('Payment History') }}"
    subtitle="{{ __('Track all financial transactions and payment receipts') }}"
    createRoute=""
    createLabel=""
    searchPlaceholder="{{ __('Search order number...') }}"
    :headers="['#', __('Amount & Date'), __('Order Ref'), __('Method'), __('Status'), __('Actions')]"
    :items="$payments">

    @forelse($payments as $payment)
    @php
    $methodMap = [
        'cash'  => ['icon' => 'banknote',     'class' => 'method-cash',  'label' => 'CASH'],
        'card'  => ['icon' => 'credit-card',   'class' => 'method-card',  'label' => 'CARD'],
        'qr'    => ['icon' => 'qr-code',       'class' => 'method-qr',    'label' => 'QR'],
        'khqr'  => ['icon' => 'qr-code',       'class' => 'method-khqr',  'label' => 'KHQR'],
    ];
    $m = $methodMap[$payment->payment_method] ?? ['icon' => 'help-circle', 'class' => 'method-cash', 'label' => strtoupper($payment->payment_method)];
    @endphp
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</span>
        </td>
        <td class="ps-3">
            <div class="d-flex align-items-center gap-3">
                <div class="pay-icon">
                    <i data-lucide="dollar-sign" style="width:15px;height:15px;"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size:0.95rem;">${{ number_format($payment->total_amount, 2) }}</div>
                    <small class="text-muted" style="font-size:0.75rem;">
                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y • h:i A') : '—' }}
                    </small>
                </div>
            </div>
        </td>
        <td class="text-center">
            <code class="text-primary fw-semibold" style="font-size:0.82rem;">{{ $payment->order->order_no }}</code>
        </td>
        <td class="text-center">
            <span class="method-badge {{ $m['class'] }}">
                <i data-lucide="{{ $m['icon'] }}" style="width:12px;height:12px;"></i>
                {{ $m['label'] }}
            </span>
        </td>
        <td class="text-center">
            <span class="status-badge paid">
                <span class="status-dot"></span>{{ __('Paid') }}
            </span>
        </td>
        <td class="text-end pe-4" style="width:100px;">
            <x-table-actions
                :viewRoute="route('payments.show', $payment->id)"
                viewPermission="view-payments"
                :printRoute="route('orders.receipt', $payment->order_id)" 
                printPermission="view-payments" />
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="py-5">
            <div class="text-center">
                <i data-lucide="receipt" style="width:48px;height:48px;color:#ced4da;"></i>
                <p class="text-muted mt-3 fw-semibold mb-1">{{ __('No transactions found') }}</p>
            </div>
        </td>
    </tr>
    @endforelse
</x-master-table>

<style>
    body { background-color: #f8fafc !important; }
    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; background: #f1f3f5; border-radius: 50%;
        font-size: 0.75rem; font-weight: 600; color: #6c757d;
    }
    .pay-icon {
        width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0;
        background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center;
    }
    .method-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 700;
    }
    .method-cash  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .method-card  { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .method-qr    { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .method-khqr  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600;
    }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.paid { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.paid .status-dot { background: #22c55e; }
</style>
@endsection
