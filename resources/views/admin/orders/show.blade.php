@extends('layouts.app')

@section('content')
<div class="order-show-page p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="flex-grow-1">
            <h2 class="fw-bold mb-0 responsive-h2" style="color: #1e293b;">{{ __('Order') }}: {{ $order->order_no }}</h2>
            <p class="text-muted small mb-0">{{ __('Managed by') }} {{ $order->user->name ?? __('System') }} • {{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('orders.index') }}" class="btn btn-white border px-3 px-sm-4 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" style="width: 16px;"></i>
                <span class="d-none d-sm-inline">{{ __('Back to Orders') }}</span>
                <span class="d-inline d-sm-none">{{ __('Back') }}</span>
            </a>
            @if($order->status == 'pending')
            @can('edit-orders')
            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-orange px-3 px-sm-4 py-2 d-flex align-items-center gap-2 rounded-lg shadow-sm">
                <i data-lucide="edit" style="width: 18px;"></i>
                <span>{{ __('Add Items / Checkout') }}</span>
            </a>
            @endcan
            @endif
            <button type="button" class="btn btn-white border px-3 px-sm-4 py-2 d-flex align-items-center gap-2 shadow-sm" onclick="window.print()">
                <i data-lucide="printer" style="width: 16px;"></i>
                <span class="d-none d-sm-inline">{{ __('Print Receipt') }}</span>
            </button>
        </div>
    </div>

    <div class="row g-4 overflow-hidden">
        <!-- Left Column: Order Items -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
                <div class="card-header bg-white py-4 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">{{ __('Order Contents') }}</h5>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $order->items->count() }} {{ __('Items') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">{{ __('Item') }}</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">{{ __('Price') }}</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">{{ __('Qty') }}</th>
                                    <th class="pe-4 py-3 text-uppercase small fw-bold text-muted border-0 text-end">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->menuItem && $item->menuItem->display_image)
                                            <img src="{{ $item->menuItem->display_image }}" class="rounded-lg shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                                            @else
                                            <div class="bg-light rounded-lg d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i data-lucide="utensils" class="text-muted" style="width: 18px;"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->menuItem->name ?? __('Deleted Item') }}</div>
                                                <small class="text-muted">{{ $item->menuItem->category->name ?? __('N/A') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-muted">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="px-3 py-1 bg-light rounded-pill border fw-bold">x {{ $item->quantity }}</span>
                                    </td>
                                    <td class="pe-4 text-end fw-bold text-dark">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Card Footer: Notes -->
                @if($order->notes)
                <div class="card-footer bg-light p-4 border-0">
                    <div class="info-label mb-2">{{ __('Internal Notes') }}</div>
                    <p class="text-muted small mb-0">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Summary & Actions -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4 h-100">

                <!-- Order Status Card -->
                <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                    <div class="card-body p-4">
                        <div class="info-label mb-3">{{ __('Status') }}</div>

                        <div class="workflow-selector d-flex flex-wrap gap-2">
                            @php
                            $statuses = [
                            'pending' => ['label' => __('Pending'), 'icon' => 'clock', 'class' => 'btn-pending'],
                            'preparing' => ['label' => __('Cooking'), 'icon' => 'flame', 'class' => 'btn-preparing'],
                            'ready' => ['label' => __('Ready'), 'icon' => 'bell', 'class' => 'btn-ready'],
                            'completed' => ['label' => __('Paid'), 'icon' => 'check-circle', 'class' => 'btn-completed'],
                            'cancelled' => ['label' => __('Cancel'), 'icon' => 'x-circle', 'class' => 'btn-cancelled'],
                            ];
                            @endphp

                            @foreach($statuses as $value => $data)
                            @php
                            $isFinalized = in_array($order->status, ['completed', 'cancelled']);
                            $isActive = ($order->status == $value);
                            @endphp

                            @if($value === 'completed' && $order->status !== 'completed')
                            <div class="flex-grow-1">
                                <button type="button" class="btn status-btn btn-outline-light text-muted w-100 d-flex flex-column align-items-center justify-content-center p-2 rounded-lg transition-all shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal" {{ $isFinalized ? 'disabled' : '' }}>
                                    <i data-lucide="{{ $data['icon'] }}" style="width: 20px;" class="mb-1"></i>
                                    <span class="extra-small fw-black text-uppercase">{{ $data['label'] }}</span>
                                </button>
                            </div>
                            @elseif($value === 'completed' && $order->status === 'completed')
                            <div class="flex-grow-1">
                                <button type="button" class="btn status-btn active btn-completed w-100 d-flex flex-column align-items-center justify-content-center p-2 rounded-lg transition-all shadow-sm" style="pointer-events: none;">
                                    <i data-lucide="{{ $data['icon'] }}" style="width: 20px;" class="mb-1"></i>
                                    <span class="extra-small fw-black text-uppercase">{{ $data['label'] }}</span>
                                </button>
                            </div>
                            @else
                            @can('edit-orders')
                            <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $value }}">
                                <button type="submit"
                                    class="btn status-btn {{ $isActive ? 'active ' . $data['class'] : 'btn-outline-light text-muted' }} w-100 d-flex flex-column align-items-center justify-content-center p-2 rounded-lg transition-all shadow-sm"
                                    {{ ($isFinalized && !$isActive) ? 'disabled style=opacity:0.3;' : '' }}
                                    {{ $isActive ? 'style=pointer-events:none;' : '' }}>
                                    <i data-lucide="{{ $data['icon'] }}" style="width: 20px;" class="mb-1"></i>
                                    <span class="extra-small fw-black text-uppercase">{{ $data['label'] }}</span>
                                </button>
                            </form>
                            @endcan
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                    <div class="card-body p-4">
                        <div class="info-label mb-3">{{ __('Customer & Service') }}</div>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="icon-box bg-light text-primary">
                                <i data-lucide="user" style="width: 20px;"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $order->customer->name ?? __('Guest') }}</div>
                                <small class="text-muted">{{ $order->customer->phone ?? __('Walk-in Customer') }}</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-light text-primary">
                                <i data-lucide="map-pin" style="width: 20px;"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ __(ucfirst(str_replace('_', ' ', $order->order_type))) }}</div>
                                @if($order->diningTable)
                                <small class="text-muted">{{ __('Table') }}: {{ $order->diningTable->name }}</small>
                                @else
                                <small class="text-muted">{{ __('No Table Assigned') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card border-0 shadow-sm rounded-lg overflow-hidden flex-grow-1" style="background: linear-gradient(145deg, #ffffff 0%, #f9fafb 100%);">
                    <div class="card-body p-4">
                        <div class="info-label mb-4">{{ __('Financial Summary') }}</div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold">{{ __('Subtotal') }}</span>
                            <span class="text-dark fw-bold">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 border-bottom pb-3">
                            <span class="text-muted fw-bold">{{ __('Tax') }} ({{ $appSettings['tax_percentage'] }}%)</span>
                            <span class="text-dark fw-bold">${{ number_format($order->tax, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-black mb-0 text-dark">{{ __('Total Amount') }}</h5>
                                <small class="text-success fw-bold">{{ __('Tax Included') }}</small>
                            </div>
                            <div class="h3 fw-black text-primary mb-0">${{ number_format($order->total_amount, 2) }}</div>
                        </div>

                        <!-- Payment Method Info -->
                        @if($order->payment)
                        <div class="mt-4 p-3 bg-white rounded-lg border d-flex align-items-center justify-content-between shadow-sm border-success border-opacity-25">
                            @php
                            $methods = [
                            'cash' => ['icon' => 'banknote', 'label' => __('Paid via Cash'), 'color' => 'text-success'],
                            'card' => ['icon' => 'credit-card', 'label' => __('Paid via Card'), 'color' => 'text-primary'],
                            'qr' => ['icon' => 'qr-code', 'label' => __('Paid via QR'), 'color' => 'text-warning'],
                            'khqr' => ['icon' => 'qr-code', 'label' => __('Paid via KHQR'), 'color' => 'text-danger'],
                            ];
                            $method = $methods[$order->payment->payment_method] ?? ['icon' => 'check-circle', 'label' => __('Payment Settled'), 'color' => 'text-success'];
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="{{ $method['icon'] }}" class="{{ $method['color'] }}" style="width: 20px;"></i>
                                <span class="fw-bold small {{ $method['color'] }}">{{ strtoupper($method['label']) }}</span>
                            </div>
                            @if($order->payment->paid_at)
                            <small class="text-muted fw-bold extra-small">{{ $order->payment->paid_at->format('M d, h:i A') }}</small>
                            @endif
                        </div>
                        @if($order->payment->payer_name || $order->payment->payer_account)
                        <div class="mt-2 extra-small fw-bold text-muted">
                            {{ __('Paid by') }} {{ $order->payment->payer_name ?? __('Mobile account') }}
                            @if($order->payment->payer_account)
                                <span class="ms-1">({{ $order->payment->payer_account }})</span>
                            @endif
                        </div>
                        @endif
                        @else
                        <div class="mt-4 p-3 bg-light rounded-lg border border-dashed text-center">
                            <span class="text-muted extra-small fw-black text-uppercase tracking-wider">
                                <i data-lucide="clock" class="me-1" style="width: 14px; transform: translateY(-1px);"></i> {{ __('Pending Payment') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-black mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="shield-check" class="text-success"></i>
                    {{ __('Complete Checkout') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="h1 fw-black text-primary mb-1">{{ $appSettings['currency'] }}{{ number_format($order->total_amount, 2) }}</div>
                <p class="text-muted extra-small fw-bold text-uppercase mb-4">{{ __('Total amount due') }}</p>

                <div class="payment-methods row g-3 mb-4">
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="pay_method" id="pay_cash" value="cash" checked>
                        <label class="btn btn-premium-toggle w-100 py-3" for="pay_cash">
                            <i data-lucide="banknote" class="mb-2"></i>
                            <span>{{ __('Cash') }}</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="pay_method" id="pay_card" value="card">
                        <label class="btn btn-premium-toggle w-100 py-3" for="pay_card">
                            <i data-lucide="credit-card" class="mb-2"></i>
                            <span>{{ __('Card') }}</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="pay_method" id="pay_qr" value="qr">
                        <label class="btn btn-premium-toggle w-100 py-3" for="pay_qr">
                            <i data-lucide="qr-code" class="mb-2"></i>
                            <span>{{ __('QR Pay') }}</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label class="info-label mb-2">{{ __('Internal Order Notes') }}</label>
                    <textarea id="orderNotes" class="form-control premium-field" rows="2" placeholder="{{ __('Special requests, allergies, etc.') }}">{{ $order->notes }}</textarea>
                </div>

                <div id="qrPayerFields" class="p-3 bg-light rounded-lg border mb-4 text-start d-none">
                    <label class="info-label mb-2">{{ __('Mobile Payment Info') }}</label>
                    <input type="text" id="payerName" class="form-control premium-field mb-2" placeholder="{{ __('Customer account name') }}">
                    <input type="text" id="payerAccount" class="form-control premium-field" placeholder="{{ __('Phone number or account id') }}">
                </div>

                <!-- Cash Payment Calculator -->
                <div id="cashCalculator" class="p-3 bg-light rounded-lg border mb-4 animate__animated animate__fadeIn">
                    <div class="row g-2 align-items-center">
                        <div class="col-6">
                            <label class="extra-small fw-black text-muted text-uppercase mb-1 d-block text-start">{{ __('Amount Paid') }}</label>
                            <div class="input-group premium-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 py-1 px-2 text-muted fw-bold">{{ $appSettings['currency'] }}</span>
                                <input type="number" id="cashReceived" class="form-control premium-field border-start-0 py-1 fw-bold" step="0.01" placeholder="0.00" oninput="calculateChange()">
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="extra-small fw-black text-muted text-uppercase mb-1 d-block">{{ __('Change Due') }}</label>
                            <div class="h4 fw-black mb-0 text-success" id="changeAmount">{{ $appSettings['currency'] }}0.00</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-finalize-checkout w-100 py-3 fw-bold rounded-lg shadow-md hover-lift transform-active" onclick="processFinalPayment()">
                    <i data-lucide="check-circle" class="me-2"></i>
                    {{ __('FINALIZE TRANSACTION') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #fdfaf5 !important;
    }

    .order-show-page {
        font-family: 'Kantumruy Pro', sans-serif;
    }

    .info-label {
        font-weight: 800;
        font-size: 0.7rem;
        color: #f08913;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .responsive-h2 {
        font-size: calc(1.3rem + .5vw);
    }

    .fw-black {
        font-weight: 900;
    }

    .icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
    }

    .premium-field {
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 12px 18px !important;
        background-color: #fff !important;
        transition: all 0.3s;
    }

    .status-btn {
        border: 1px solid #f1f5f9;
        background: #fff;
        min-height: 70px;
    }

    .status-btn:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
        transform: translateY(-2px);
    }

    .status-btn.active {
        color: white !important;
        border-color: transparent !important;
    }

    .btn-pending {
        background-color: #94a3b8 !important;
        box-shadow: 0 4px 12px rgba(148, 163, 184, 0.4) !important;
    }

    .btn-preparing {
        background-color: #f59e0b !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4) !important;
    }

    .btn-ready {
        background-color: #10b981 !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4) !important;
    }

    .btn-completed {
        background-color: #3b82f6 !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;
    }

    .btn-cancelled {
        background-color: #ef4444 !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4) !important;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    @media print {

        /* Hide everything not related to the receipt */
        .sidebar,
        .navbar,
        .admin-navbar,
        .btn,
        .main-content>.x-navbar,
        #app>div>.sidebar,
        .toast-container,
        .card-header,
        select,
        .info-label,
        .extra-small,
        .header-info p,
        a,
        button {
            display: none !important;
        }

        body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 12pt;
        }

        .order-show-page {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .content-wrapper {
            padding: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
        }

        .col-lg-8,
        .col-lg-4 {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        .row {
            display: block !important;
            margin: 0 !important;
        }

        /* Receipt Header Styling */
        .responsive-h2 {
            font-size: 24pt !important;
            text-align: center;
            margin-bottom: 20px !important;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        /* Invoice Style Table */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        th {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact;
            border-bottom: 1px solid #000 !important;
        }

        td {
            border-bottom: 1px solid #eee !important;
            padding: 10px 0 !important;
        }

        .image-container,
        img {
            display: none !important;
        }

        /* Hide images for cleaner print */

        .card-body {
            padding: 0 !important;
        }

        /* Total Section */
        .h3 {
            font-size: 20pt !important;
            color: black !important;
        }

        .text-primary {
            color: black !important;
        }

        /* Layout Fixes */
        .flex-grow-1 {
            width: 100% !important;
        }

        .justify-content-between {
            display: flex !important;
            justify-content: space-between !important;
        }
    }

    /* Payment Options */
    .btn-payment-option {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        background: #fff;
        color: #475569;
        font-weight: 700;
        transition: 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .btn-check:checked+.btn-payment-option {
        background: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }
</style>

@push('js')
<script>
    function calculateChange() {
        const total = {{ $order->total_amount }};
        const paid = parseFloat(document.getElementById('cashReceived').value) || 0;
        const change = paid - total;
        const changeDisplay = document.getElementById('changeAmount');
        const currency = "{{ $appSettings['currency'] }}";

        if (change >= 0) {
            changeDisplay.innerText = `${currency}${change.toFixed(2)}`;
            changeDisplay.className = 'h4 fw-black mb-0 text-success';
        } else {
            changeDisplay.innerText = `${currency}${Math.abs(change).toFixed(2)}`;
            changeDisplay.className = 'h4 fw-black mb-0 text-danger';
        }
    }

    // Toggle Calculator based on method
    document.querySelectorAll('input[name="pay_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const calc = document.getElementById('cashCalculator');
            const qrFields = document.getElementById('qrPayerFields');
            if (this.id === 'pay_cash') calc.classList.remove('d-none');
            else calc.classList.add('d-none');

            if (this.id === 'pay_qr') qrFields.classList.remove('d-none');
            else qrFields.classList.add('d-none');
        });
    });

    // Auto-fill payment amount on modal show
    document.getElementById('paymentModal').addEventListener('show.bs.modal', function() {
        document.getElementById('cashReceived').value = "{{ $order->total_amount }}";
        calculateChange();
    });

    async function processFinalPayment() {
        const payMethodChecked = document.querySelector('input[name="pay_method"]:checked');
        const payMethod = payMethodChecked ? payMethodChecked.value : 'cash';

        const data = {
            payment_method: payMethod,
            paid_amount: parseFloat(document.getElementById('cashReceived').value) || {{ $order->total_amount }},
            notes: document.getElementById('orderNotes').value,
            payer_name: payMethod === 'qr' ? document.getElementById('payerName')?.value.trim() : null,
            payer_account: payMethod === 'qr' ? document.getElementById('payerAccount')?.value.trim() : null,
            _token: "{{ csrf_token() }}"
        };

        try {
            const response = await fetch("{{ route('orders.pay', $order->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                if (payMethod === 'qr') {
                    const payer = data.payer_name || 'mobile account';
                    alert(`Payment received by ${payer}.`);
                }
                window.location.reload();
            } else {
                const err = await response.json();
                alert(err.message || 'Error processing payment');
            }
        } catch (e) {
            console.error(e);
            alert('Network error');
        }
    }
</script>
@endpush
@endsection
