@extends('layouts.app')

@section('content')
<div class="payment-details-page p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="flex-grow-1">
            <h2 class="fw-black mb-0 responsive-h2" style="color: #1e293b;">{{ __('Transaction Details') }}</h2>
            <p class="text-muted small mb-0">{{ __('Receipt') }} #TXN-{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }} • {{ __('Processing Complete') }}</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('payments.index') }}" class="btn btn-white border px-4 py-2 d-flex align-items-center gap-2 rounded-lg shadow-sm">
                <i data-lucide="arrow-left" style="width: 18px;"></i>
                <span>{{ __('Back to List') }}</span>
            </a>
            <button onclick="window.print()" class="btn btn-orange px-4 py-2 d-flex align-items-center gap-2 rounded-lg shadow-sm">
                <i data-lucide="printer" style="width: 18px;"></i>
                <span>{{ __('Print Receipt') }}</span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Receipt Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white p-2 rounded-circle">
                                <i data-lucide="shopping-bag" style="width: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="fw-black mb-0">{{ __('Order') }} {{ $payment->order->order_no }}</h5>
                                <small class="text-muted text-uppercase fw-bold">{{ __(ucfirst(str_replace('_', ' ', $payment->order->order_type))) }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-success text-white px-3 py-2 rounded-pill h5 mb-0">
                                <i data-lucide="check" class="me-1"></i> {{ __('PAID') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <table class="table table-borderless align-middle mb-5">
                        <thead class="bg-light rounded-lg">
                            <tr>
                                <th class="p-3 text-muted extra-small text-uppercase">{{ __('Menu Item') }}</th>
                                <th class="p-3 text-muted extra-small text-uppercase text-center">{{ __('Unit Price') }}</th>
                                <th class="p-3 text-muted extra-small text-uppercase text-center">{{ __('Qty') }}</th>
                                <th class="p-3 text-muted extra-small text-uppercase text-end">{{ __('Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment->order->items as $item)
                            <tr class="border-bottom">
                                <td class="p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $item->menuItem->display_image }}" class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->menuItem->name }}</div>
                                            <small class="text-muted">{{ $item->menuItem->category->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-center fw-bold">${{ number_format($item->price, 2) }}</td>
                                <td class="p-3 text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $item->quantity }}</span>
                                </td>
                                <td class="p-3 text-end fw-black">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="row justify-content-end mt-4">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-bold">{{ __('Subtotal') }}</span>
                                <span class="text-dark fw-bold">${{ number_format($payment->order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-3 border-bottom">
                                <span class="text-muted fw-bold">{{ __('Tax') }} ({{ $appSettings['tax_percentage'] }}%)</span>
                                <span class="text-dark fw-bold">${{ number_format($payment->order->tax, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <span class="h4 fw-black mb-0">{{ __('Total Due') }}</span>
                                <span class="h3 fw-black text-primary mb-0">${{ number_format($payment->order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Sidebar -->
        <div class="col-lg-4">
            <div class="item-info-header mb-3">
                <span class="info-label text-uppercase mb-1 d-block">{{ __('Transaction Info') }}</span>
            </div>
            
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="payment-method-tile d-flex align-items-center gap-3 p-3 bg-light rounded-lg border mb-4">
                        @php
                            $methods = [
                                'cash' => ['icon' => 'banknote', 'label' => __('CASH PAYMENT')],
                                'card' => ['icon' => 'credit-card', 'label' => __('CARD PAYMENT')],
                                'qr' => ['icon' => 'qr-code', 'label' => __('QR SCAN')],
                                'khqr' => ['icon' => 'qr-code', 'label' => __('KHQR SCAN')],
                            ];
                            $method = $methods[$payment->payment_method] ?? ['icon' => 'help-circle', 'label' => __('UNKNOWN')];
                        @endphp
                        <div class="bg-white p-2 rounded shadow-sm">
                            <i data-lucide="{{ $method['icon'] }}" class="text-primary" style="width: 28px; height: 28px;"></i>
                        </div>
                        <div>
                            <div class="extra-small text-muted fw-bold">{{ __('Method') }}</div>
                            <div class="fw-black h6 mb-0">{{ $method['label'] }}</div>
                        </div>
                    </div>

                    <div class="meta-row d-flex justify-content-between py-3 border-bottom">
                        <span class="text-muted small fw-bold">{{ __('Cash Received') }}</span>
                        <span class="text-dark fw-bold h6 mb-0">${{ number_format($payment->paid_amount, 2) }}</span>
                    </div>
                    <div class="meta-row d-flex justify-content-between py-3 border-bottom">
                        <span class="text-muted small fw-bold">{{ __('Change Returned') }}</span>
                        <span class="text-success fw-black h6 mb-0">${{ number_format($payment->change_amount, 2) }}</span>
                    </div>
                    <div class="meta-row d-flex justify-content-between py-3 mb-4">
                        <span class="text-muted small fw-bold">{{ __('Received Date') }}</span>
                        <span class="text-dark small fw-bold">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y • h:i') : __('N/A') }}</span>
                    </div>

                    <div class="p-3 bg-primary-subtle text-primary rounded-lg border border-primary border-opacity-25 d-flex align-items-center gap-3">
                        <i data-lucide="user" style="width: 20px;"></i>
                        <div>
                            <small class="d-block extra-small text-uppercase fw-black">{{ __('Processed By') }}</small>
                            <span class="fw-bold">{{ $payment->order->user->name ?? __('System') }}</span>
                        </div>
                    </div>
                    @if($payment->payer_name || $payment->payer_account)
                    <div class="p-3 mt-3 bg-success-subtle text-success rounded-lg border border-success border-opacity-25 d-flex align-items-center gap-3">
                        <i data-lucide="smartphone" style="width: 20px;"></i>
                        <div>
                            <small class="d-block extra-small text-uppercase fw-black">{{ __('Paid By') }}</small>
                            <span class="fw-bold">{{ $payment->payer_name ?? __('Mobile account') }}</span>
                            @if($payment->payer_account)
                                <small class="d-block text-muted">{{ $payment->payer_account }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($payment->order->customer_id)
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="bg-info-subtle text-info p-3 rounded-circle">
                        <i data-lucide="users" style="width: 24px;"></i>
                    </div>
                    <div>
                        <div class="extra-small text-muted fw-bold text-uppercase">{{ __('Customer Info') }}</div>
                        <div class="fw-black text-dark">{{ $payment->order->customer->name }}</div>
                        <small class="text-muted">{{ $payment->order->customer->phone }}</small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .payment-details-page { font-family: 'Kantumruy Pro', sans-serif; }
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .rounded-lg { border-radius: 12px !important; }
    
    .info-label { font-weight: 800; font-size: 0.7rem; color: #f08913; text-transform: uppercase; letter-spacing: 1px; }
    .btn-orange { background: #f08913; color: white; border: none; }
    .btn-orange:hover { background: #d87b11; color: white; transform: translateY(-2px); }
    .btn-white { background: white; color: #1e293b; }

    @media print {
        .admin-navbar, .sidebar, .btn, .card-header, .navbar, #app > div > .sidebar, .toast-container { 
            display: none !important; 
        }
        body { background: white !important; margin: 0; padding: 0; }
        .payment-details-page { padding: 0 !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .content-wrapper { padding: 0 !important; }
        
        .card { border: none !important; box-shadow: none !important; }
        .col-lg-8 { width: 100% !important; display: block !important; margin: 0 !important; }
        .col-lg-4 { width: 100% !important; display: block !important; margin-top: 20px !important; }
        
        /* Hide images for cleaner print */
        img, .rounded-circle { display: none !important; }
        
        table { width: 100% !important; border-collapse: collapse !important; }
        th { background: #f8fafc !important; -webkit-print-color-adjust: exact; border-bottom: 2px solid #000 !important; }
        td { border-bottom: 1px solid #eee !important; padding: 10px 0 !important; }
        
        .responsive-h2 { font-size: 24pt !important; text-align: center; display: block; width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px !important; }
        .flex-grow-1 { width: 100% !important; }
        .justify-content-between { display: flex !important; justify-content: space-between !important; }
        
        .h3 { font-size: 20pt !important; color: black !important; }
        .text-primary { color: black !important; }
    }
</style>
@push('js')
<script>
    window.addEventListener('load', function() {
        @if(isset($autoPrint) && $autoPrint)
            window.print();
        @endif
    });
</script>
@endpush
@endsection
