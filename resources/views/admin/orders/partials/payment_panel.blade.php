{{--
    Partial: payment_panel.blade.php
    Inline (non-modal) checkout panel.
    Left  → Ordered items list
    Right → Payment method selector + notes + cash calculator + action buttons

    Variables expected (passed from parent / JS-driven):
      $appSettings['currency']
      $appSettings['tax_percentage']
      $appSettings['exchange_rate']
--}}

<div class="card shadow-sm border">
    <div class="row g-0 pp-body">

        {{-- ─────────────────────────────────────────── --}}
        {{-- LEFT — Order Items List                      --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="col-md-6 pp-left border-end d-flex flex-column">
            <div class="bg-light px-4 py-2 border-bottom">
                <span class="small fw-bold text-muted text-uppercase">{{ __('List Menu Order') }}</span>
            </div>

            {{-- Items rendered by JS into #ppOrderItems --}}
            <div class="flex-grow-1 overflow-auto px-4 py-3" id="ppOrderItems">
                <div class="text-center py-5 opacity-50 pp-empty-msg">
                    <i data-lucide="shopping-bag" class="mb-3" style="width: 40px; height: 40px;"></i>
                    <p class="fw-bold small">{{ __('No items in this order') }}</p>
                </div>
            </div>

            {{-- Totals strip --}}
            <div class="px-4 py-3 border-top bg-light">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">{{ __('Subtotal') }}</span>
                    <span class="fw-bold small" id="ppSubtotalLabel">{{ $appSettings['currency'] }}0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">{{ __('Tax') }} ({{ $appSettings['tax_percentage'] }}%)</span>
                    <span class="fw-bold small" id="ppTaxLabel">{{ $appSettings['currency'] }}0.00</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold h6 mb-0">{{ __('Total') }}</span>
                    <div id="ppTotalDisplayArea">
                        <span class="h4 fw-bold text-primary mb-0">{{ $appSettings['currency'] }}0.00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────── --}}
        {{-- RIGHT — Payment Panel                        --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="col-md-6 pp-right d-flex flex-column">
            <div class="flex-grow-1 overflow-auto p-4">

                {{-- Payment Method --}}
                <div class="mb-4">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">
                        {{ __('Select Payment Method') }}
                    </label>
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="pp_pay_method" id="pp_pay_cash" value="cash" checked>
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-1" for="pp_pay_cash">
                                <i data-lucide="banknote" style="width: 20px; height: 20px;"></i>
                                <span class="small fw-bold">{{ __('Cash') }}</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="pp_pay_method" id="pp_pay_card" value="card">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-1" for="pp_pay_card">
                                <i data-lucide="credit-card" style="width: 20px; height: 20px;"></i>
                                <span class="small fw-bold">{{ __('Card') }}</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="pp_pay_method" id="pp_pay_qr" value="qr">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-1" for="pp_pay_qr">
                                <i data-lucide="qr-code" style="width: 20px; height: 20px;"></i>
                                <span class="small fw-bold">{{ __('KHQR') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- QR Info Panel --}}
                <div id="ppQrInfo" class="p-3 rounded border bg-light mb-4 d-none">
                    <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">{{ __('QR Pay Instructions') }}</h6>
                    <p class="small text-muted mb-3">
                        {{ __('Scan the QR code with your mobile wallet, then enter the payer name shown on the phone receipt.') }}
                    </p>
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/myqr.jpg') }}" alt="QR Code" class="img-fluid rounded border shadow-sm" style="max-width: 180px;">
                    </div>
                    <div class="text-center mb-3">
                        <a href="{{ asset('images/myqr.jpg') }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3">
                            <i data-lucide="external-link" class="me-1" style="width: 13px;"></i>{{ __('Open in new tab') }}
                        </a>
                    </div>
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="small fw-bold text-muted text-uppercase mb-1 d-block">{{ __('Paid By') }}</label>
                            <input type="text" id="ppPayerName" class="form-control" placeholder="{{ __('Customer account name') }}">
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted text-uppercase mb-1 d-block">{{ __('Account / Phone') }}</label>
                            <input type="text" id="ppPayerAccount" class="form-control" placeholder="{{ __('Phone number or account id') }}">
                        </div>
                    </div>
                </div>

                {{-- Internal Order Notes --}}
                <div class="mb-4">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">{{ __('Internal Order Notes') }}</label>
                    <textarea id="ppOrderNotes" class="form-control" rows="2"
                              placeholder="{{ __('Special requests, allergies, etc.') }}"></textarea>
                </div>

                {{-- Cash Calculator --}}
                <div id="ppCashCalculator" class="p-3 bg-light rounded border mb-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-6">
                            <label class="small fw-bold text-muted text-uppercase mb-1 d-block">{{ __('Amount Paid') }}</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white fw-bold text-muted small">
                                    {{ $appSettings['currency'] }}
                                </span>
                                <input type="number" id="ppCashReceived"
                                       class="form-control py-2"
                                       step="0.01" placeholder="0.00"
                                       oninput="ppCalculateChange()">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted text-uppercase mb-1 d-block">{{ __('Change Due') }}</label>
                            <div class="h5 fw-bold mb-0 text-success" id="ppChangeAmount">
                                {{ $appSettings['currency'] }}0.00
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /overflow-auto --}}

            {{-- Action Buttons --}}
            <div class="p-4 border-top bg-light mt-auto">
                @if(isset($existingOrder) && $existingOrder)
                <div class="mb-2">
                    <a href="{{ route('orders.show', $existingOrder->id) }}"
                       class="btn btn-outline-secondary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                        <span class="small text-uppercase">{{ __('View Order #') }}{{ $existingOrder->order_no }}</span>
                    </a>
                </div>
                @endif
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" id="ppSaveLaterBtn"
                                class="btn btn-warning w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="clock" style="width: 17px; height: 17px;"></i>
                            <span class="small text-uppercase">{{ __('Save (Pay Later)') }}</span>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" id="ppPayNowBtn"
                                class="btn btn-success w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="check" style="width: 17px; height: 17px;"></i>
                            <span class="small text-uppercase">{{ __('Pay Now') }}</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>{{-- /pp-right --}}

    </div>{{-- /row --}}
</div>{{-- /payment-panel-wrap --}}


{{-- ─────────────────────── STYLES ─────────────────────── --}}
<style>
    /* ── Body rows ── */
    .pp-body {
        flex: 1;
        min-height: 0;
    }

    .pp-left,
    .pp-right {
        min-height: 480px;
        max-height: 72vh;
    }

    /* ── Item rows ── */
    #ppOrderItems .pp-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
    }

    .pp-item-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .pp-item-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: #212529;
    }

    .pp-item-price {
        font-size: 0.85rem;
        font-weight: bold;
        color: #0d6efd;
    }

    .pp-item-qty {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* ── Responsive: stack on small screens ── */
    @media (max-width: 767.98px) {
        .pp-left,
        .pp-right {
            max-height: none;
            min-height: 0;
        }

        .pp-left {
            border-end: none !important;
            border-bottom: 1px solid #e2e8f0;
        }
    }
</style>


{{-- ─────────────────────── SCRIPTS ─────────────────────── --}}
<script>
(function () {
    const currency      = @json($appSettings['currency']);
    const taxRate       = parseFloat(@json($appSettings['tax_percentage'])) / 100;
    const exchangeRate  = parseFloat(@json($appSettings['exchange_rate'] ?? 4100)) || 4100;

    /* ── Render ordered items into left panel ── */
    function ppRenderItems() {
        const container = document.getElementById('ppOrderItems');
        if (!container) return;

        // Read from the global cart array (shared with checkout.blade.php)
        const items = (typeof cart !== 'undefined' && Array.isArray(cart)) ? cart : [];

        if (items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 opacity-50 pp-empty-msg">
                    <i data-lucide="shopping-bag" class="mb-3" style="width: 40px; height: 40px;"></i>
                    <p class="fw-bold small">{{ __('No items in this order') }}</p>
                </div>`;
            ppUpdateTotals(0);
            if (window.lucide) lucide.createIcons();
            return;
        }

        let html = '';
        let subtotal = 0;

        items.forEach(function (item) {
            if (!item) return;
            const price     = parseFloat(item.price) || 0;
            const qty       = parseInt(item.qty) || 0;
            const lineTotal = price * qty;
            subtotal       += lineTotal;
            const img       = item.display_image || "{{ asset('images/placeholder.jpg') }}";

            html += `
                <div class="pp-item">
                    <img src="${img}" class="pp-item-img"
                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="pp-item-name">${item.name || '—'}</div>
                        <div class="pp-item-qty">× ${qty}</div>
                    </div>
                    <div class="pp-item-price text-nowrap">${currency}${lineTotal.toFixed(2)}</div>
                </div>`;
        });

        container.innerHTML = html;
        ppUpdateTotals(subtotal);
        if (window.lucide) lucide.createIcons();
    }

    /* ── Update totals strip ── */
    function ppUpdateTotals(subtotal) {
        const tax   = subtotal * taxRate;
        const total = subtotal + tax;
        const riel  = Math.round(total * exchangeRate);

        const sub   = document.getElementById('ppSubtotalLabel');
        const taxEl = document.getElementById('ppTaxLabel');
        const tot   = document.getElementById('ppTotalDisplayArea');

        if (sub)   sub.innerText   = currency + subtotal.toFixed(2);
        if (taxEl) taxEl.innerText = currency + tax.toFixed(2);
        if (tot)   tot.innerHTML   = `
            <span class="h4 fw-bold text-primary mb-0 d-block">${currency}${total.toFixed(2)}</span>
            <span class="badge bg-light text-muted fw-bold border" style="font-size:0.68rem;">
                ៛${riel.toLocaleString()}
            </span>`;

        window.ppCurrentTotalUSD = total;
        ppCalculateChange();
    }

    /* ── Change calculator ── */
    window.ppCalculateChange = function () {
        const total  = window.ppCurrentTotalUSD || 0;
        const paid   = parseFloat(document.getElementById('ppCashReceived')?.value) || 0;
        const change = paid - total;
        const el     = document.getElementById('ppChangeAmount');
        if (!el) return;
        el.innerText  = currency + Math.abs(change).toFixed(2);
        el.className  = 'h5 fw-bold mb-0 ' + (change >= 0 ? 'text-success' : 'text-danger');
    };

    /* ── Payment method toggle ── */
    document.querySelectorAll('input[name="pp_pay_method"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const calc  = document.getElementById('ppCashCalculator');
            const qrBox = document.getElementById('ppQrInfo');
            if (this.id === 'pp_pay_cash') {
                calc  && calc.classList.remove('d-none');
                qrBox && qrBox.classList.add('d-none');
            } else if (this.id === 'pp_pay_qr') {
                calc  && calc.classList.add('d-none');
                qrBox && qrBox.classList.remove('d-none');
            } else {
                calc  && calc.classList.add('d-none');
                qrBox && qrBox.classList.add('d-none');
            }
        });
    });

    /* ── Action buttons — delegate to parent processPayment() if available ── */
    var btnLater = document.getElementById('ppSaveLaterBtn');
    var btnNow   = document.getElementById('ppPayNowBtn');

    if (btnLater) {
        btnLater.addEventListener('click', function () {
            if (typeof processPayment === 'function') {
                processPayment(false);
            }
        });
    }

    if (btnNow) {
        btnNow.addEventListener('click', function () {
            if (typeof processPayment === 'function') {
                processPayment(true);
            }
        });
    }

    /* ── Auto-fill cash received when panel mounts ── */
    var cashInput = document.getElementById('ppCashReceived');
    if (cashInput && window.currentTotalUSD) {
        cashInput.value = window.currentTotalUSD.toFixed(2);
    }

    /* ── Initial render ── */
    ppRenderItems();

    /* ── Re-render whenever the global cart changes (hook into renderCart) ── */
    var _origRenderCart = window.renderCart;
    window.renderCart = function () {
        if (typeof _origRenderCart === 'function') _origRenderCart.apply(this, arguments);
        ppRenderItems();
    };
})();
</script>
