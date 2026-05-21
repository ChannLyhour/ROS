<!-- Summary & Checkout Area -->
<div class="summary-details mb-4">
    <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Subtotal</span>
        <span class="fw-bold" id="subtotalLabel">{{ $appSettings['currency'] }}0.00</span>
    </div>
    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
        <span class="text-muted">Tax ({{ $appSettings['tax_percentage'] }}%)</span>
        <span class="fw-bold" id="taxLabel">{{ $appSettings['currency'] }}0.00</span>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="h5 fw-black mb-0">Total</span>
        <div class="text-end" id="totalDisplayArea">
            {{-- Area will be updated by JS --}}
        </div>
    </div>
</div>

<div class="checkout-actions p-2 border-top shadow-sm">
    <button class="btn btn-success w-100 rounded-lg shadow-sm d-flex align-items-center justify-content-center gap-2 transform-active text-white" 
            onclick="goToCheckout(this)">
        <i data-lucide="credit-card" style="width: 20px;"></i>
        <span style="letter-spacing: 0.5px;">{{ __('PAYMENT & CHECKOUT') }}</span>
    </button>
</div>
