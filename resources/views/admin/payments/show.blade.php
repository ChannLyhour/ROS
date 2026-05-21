@extends('layouts.app')

@section('title', __('Transaction Details'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('Transaction Details') }}</h2>
            <p class="text-muted small mb-0">
                {{ __('Receipt') }} <code class="text-primary">#TXN-{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</code>
                &middot;
                <span class="status-badge paid"><span class="status-dot"></span>{{ __('Paid') }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                <i data-lucide="printer" style="width:15px;"></i>{{ __('Print Receipt') }}
            </button>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
                <i data-lucide="arrow-left" style="width:15px;"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Order Items -->
        <div class="col-lg-8">
            <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
                <!-- Order Header -->
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center"
                     style="border-color:#dee2e6 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="order-icon">
                            <i data-lucide="shopping-bag" style="width:16px;height:16px;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">{{ __('Order') }} {{ $payment->order->order_no }}</div>
                            <small class="text-muted text-uppercase" style="font-size:0.72rem;">
                                {{ __(ucfirst(str_replace('_', ' ', $payment->order->order_type))) }}
                            </small>
                        </div>
                    </div>
                    <span class="status-badge paid"><span class="status-dot"></span>{{ __('PAID') }}</span>
                </div>

                <!-- Items Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead style="background:#f8f9fa; border-bottom:1px solid #dee2e6;">
                                <tr>
                                    <th class="ps-4 py-3 small text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">{{ __('Item') }}</th>
                                    <th class="py-3 small text-muted fw-semibold text-uppercase text-center" style="font-size:0.72rem;">{{ __('Price') }}</th>
                                    <th class="py-3 small text-muted fw-semibold text-uppercase text-center" style="font-size:0.72rem;">{{ __('Qty') }}</th>
                                    <th class="pe-4 py-3 small text-muted fw-semibold text-uppercase text-end" style="font-size:0.72rem;">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->order->items as $item)
                                <tr style="border-bottom:1px solid #f1f3f5;">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $item->menuItem->display_image }}"
                                                 class="rounded border" style="width:40px;height:40px;object-fit:cover;border-color:#dee2e6 !important;">
                                            <div>
                                                <div class="fw-semibold text-dark small">{{ $item->menuItem->name }}</div>
                                                <small class="text-muted" style="font-size:0.72rem;">{{ $item->menuItem->category->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-semibold small">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="qty-badge">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="pe-4 text-end fw-bold text-dark">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals -->
                <div class="card-footer bg-white border-top px-4 py-3" style="border-color:#dee2e6 !important;">
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">{{ __('Subtotal') }}</span>
                                <span class="fw-semibold small">${{ number_format($payment->order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 mb-2 border-bottom" style="border-color:#f1f3f5 !important;">
                                <span class="text-muted small">{{ __('Tax') }} ({{ $appSettings['tax_percentage'] }}%)</span>
                                <span class="fw-semibold small">${{ number_format($payment->order->tax, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="fw-bold text-dark">{{ __('Total') }}</span>
                                <span class="fw-bold text-primary fs-5">${{ number_format($payment->order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Meta Info -->
        <div class="col-lg-4">
            <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Transaction Info') }}</p>

            <div class="card border mb-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                <div class="card-body p-3">
                    @php
                    $methodLabels = [
                        'cash'  => ['icon' => 'banknote',   'label' => __('Cash Payment')],
                        'card'  => ['icon' => 'credit-card','label' => __('Card Payment')],
                        'qr'    => ['icon' => 'qr-code',    'label' => __('QR Scan')],
                        'khqr'  => ['icon' => 'qr-code',    'label' => __('KHQR Scan')],
                    ];
                    $ml = $methodLabels[$payment->payment_method] ?? ['icon' => 'help-circle', 'label' => __('Unknown')];
                    @endphp

                    <!-- Payment Method -->
                    <div class="d-flex align-items-center gap-3 p-3 bg-light border rounded mb-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                        <div class="pay-method-icon">
                            <i data-lucide="{{ $ml['icon'] }}" style="width:18px;height:18px;"></i>
                        </div>
                        <div>
                            <div class="extra-small text-muted text-uppercase fw-semibold">{{ __('Payment Method') }}</div>
                            <div class="fw-semibold text-dark small">{{ $ml['label'] }}</div>
                        </div>
                    </div>

                    <div class="meta-row d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f3f5 !important;">
                        <span class="text-muted small">{{ __('Cash Received') }}</span>
                        <span class="fw-semibold text-dark small">${{ number_format($payment->paid_amount, 2) }}</span>
                    </div>
                    <div class="meta-row d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f3f5 !important;">
                        <span class="text-muted small">{{ __('Change') }}</span>
                        <span class="fw-semibold text-success small">${{ number_format($payment->change_amount, 2) }}</span>
                    </div>
                    <div class="meta-row d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f3f5 !important;">
                        <span class="text-muted small">{{ __('Date') }}</span>
                        <span class="fw-semibold small text-dark">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y • h:i') : '—' }}</span>
                    </div>

                    <!-- Processed By -->
                    <div class="d-flex align-items-center gap-2 pt-3">
                        <div class="info-icon-sm text-primary"><i data-lucide="user" style="width:13px;"></i></div>
                        <div>
                            <div class="extra-small text-muted text-uppercase">{{ __('Processed By') }}</div>
                            <div class="small fw-semibold text-dark">{{ $payment->order->user->name ?? __('System') }}</div>
                        </div>
                    </div>

                    @if($payment->payer_name || $payment->payer_account)
                    <div class="d-flex align-items-center gap-2 pt-2 mt-2 border-top" style="border-color:#f1f3f5 !important;">
                        <div class="info-icon-sm text-success"><i data-lucide="smartphone" style="width:13px;"></i></div>
                        <div>
                            <div class="extra-small text-muted text-uppercase">{{ __('Paid By') }}</div>
                            <div class="small fw-semibold text-dark">{{ $payment->payer_name ?? __('Mobile') }}</div>
                            @if($payment->payer_account)
                            <small class="text-muted" style="font-size:0.72rem;">{{ $payment->payer_account }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($payment->order->customer_id)
            <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="info-icon-md text-info"><i data-lucide="user-circle" style="width:18px;"></i></div>
                    <div>
                        <div class="extra-small text-muted text-uppercase fw-semibold">{{ __('Customer') }}</div>
                        <div class="fw-semibold text-dark small">{{ $payment->order->customer->name }}</div>
                        <small class="text-muted" style="font-size:0.72rem;">{{ $payment->order->customer->phone }}</small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .extra-small { font-size: 0.72rem; }
    .tracking { letter-spacing: 0.05em; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 600;
    }
    .status-badge .status-dot { width: 6px; height: 6px; border-radius: 50%; }
    .status-badge.paid { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.paid .status-dot { background: #22c55e; }
    .order-icon {
        width: 34px; height: 34px; border-radius: 8px;
        background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe;
        display: flex; align-items: center; justify-content: center;
    }
    .pay-method-icon {
        width: 38px; height: 38px; border-radius: 8px;
        background: #fff; color: #0d6efd; border: 1px solid #dee2e6;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .info-icon-sm {
        width: 28px; height: 28px; border-radius: 6px; flex-shrink: 0;
        background: #f1f5f9; display: flex; align-items: center; justify-content: center;
    }
    .info-icon-md {
        width: 38px; height: 38px; border-radius: 8px; flex-shrink: 0;
        background: #e0f2fe; display: flex; align-items: center; justify-content: center;
    }
    .qty-badge {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 26px; height: 22px; padding: 0 6px;
        background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px;
        font-size: 0.8rem; font-weight: 600; color: #475569;
    }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; color: #fff; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }

    @media print {
        .admin-navbar, .sidebar, .btn, .navbar { display: none !important; }
        body { background: white !important; }
        .card { border: none !important; box-shadow: none !important; }
        .col-lg-8 { width: 100% !important; }
        .col-lg-4 { width: 100% !important; margin-top: 20px !important; }
    }
</style>

@push('js')
<script>
    window.addEventListener('load', function () {
        @if(isset($autoPrint) && $autoPrint)
            window.print();
        @endif
    });
</script>
@endpush
@endsection
