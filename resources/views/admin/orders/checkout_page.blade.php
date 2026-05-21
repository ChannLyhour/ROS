@extends('layouts.app')

@section('title', __('Checkout'))

@section('content')
<div class="checkout-page-container py-3 px-3">
    <div class="container-fluid px-0">
        
        <!-- Navigation & Header Bar -->
        <div class="checkout-header-bar d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 rounded shadow-sm bg-white border">
            <div class="d-flex align-items-center gap-3">
                @if(isset($existingOrder) && $existingOrder)
                    <a href="{{ route('orders.edit', $existingOrder->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 fw-bold text-decoration-none">
                        <i data-lucide="chevron-left" style="width: 18px;"></i>
                        <span>{{ __('Back to POS') }}</span>
                    </a>
                @else
                    <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 fw-bold text-decoration-none">
                        <i data-lucide="chevron-left" style="width: 18px;"></i>
                        <span>{{ __('Back to POS') }}</span>
                    </a>
                @endif
                <div class="border-end d-none d-sm-block" style="height: 30px; border-color: #dee2e6 !important;"></div>
                <div>
                    <span class="badge bg-secondary px-2 py-1 small text-uppercase mb-1 d-inline-block fw-bold">
                        {{ __('Point of Sale') }}
                    </span>
                    <h4 class="mb-0 fw-bold text-dark">
                        @if(isset($existingOrder) && $existingOrder)
                            {{ __('Modify Order') }} #{{ $existingOrder->order_no }}
                        @else
                            {{ __('New Transaction') }}
                        @endif
                    </h4>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <!-- Order Type Badge -->
                <div class="d-flex align-items-center gap-2 bg-light px-3 py-2 rounded border">
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.75rem;">{{ __('Service') }}:</span>
                    <span id="orderTypeBadge" class="badge bg-warning text-dark fw-bold px-3 py-2 text-uppercase">
                        {{ __('Loading...') }}
                    </span>
                </div>
                
                <!-- Table Number Badge (conditional) -->
                <div id="tableBadgeContainer" class="d-none align-items-center gap-2 bg-light px-3 py-2 rounded border">
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.75rem;">{{ __('Table') }}:</span>
                    <span id="tableNumberBadge" class="badge bg-primary text-white fw-bold px-3 py-2 text-uppercase">
                        —
                    </span>
                </div>
            </div>
        </div>

        @php
            $initialCart = [];
            if (isset($existingOrder) && $existingOrder) {
                $initialCart = $existingOrder->items->map(function ($item) {
                    return [
                        'id' => (int) $item->menu_item_id,
                        'name' => optional($item->menuItem)->name ?? 'Unknown Item',
                        'price' => (float) $item->price,
                        'display_image' => optional($item->menuItem)->display_image ?? asset('images/placeholder.jpg'),
                        'qty' => (int) ($item->quantity ?? 1)
                    ];
                })->values()->toArray();
            }
        @endphp

        <!-- We must declare the global cart variables BEFORE including the payment panel -->
        <script>
            window.cart = [];
            // Parse local storage or initial values immediately to feed the partial's load
            (function() {
                const saved = localStorage.getItem('pos_cart');
                let loadedFromStorage = false;
                if (saved) {
                    try {
                        const data = JSON.parse(saved);
                        window.cart = data.items || [];
                        if (window.cart.length > 0) loadedFromStorage = true;
                    } catch(e) {
                        console.error("Failed to parse cart storage", e);
                    }
                }
                if (!loadedFromStorage) {
                    const backendCart = @json($initialCart);
                    if (backendCart && backendCart.length > 0) {
                        window.cart = backendCart;
                    }
                }
            })();
            var cart = window.cart;
        </script>

        <!-- Render the inline payment panel partial -->
        <div class="mt-2">
            @include('admin.orders.partials.payment_panel')
        </div>
        
    </div>
</div>

<style>
    /* Scoped stylesheet overrides to unlock maximum viewport space */
    body:has(.checkout-page-container) .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }

    .checkout-page-container {
        background-color: #f1f5f9;
        min-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }

    .pp-left, .pp-right {
        height: calc(100vh - 200px) !important;
        max-height: calc(100vh - 200px) !important;
        min-height: 480px !important;
    }

    @media (max-width: 991.98px) {
        .pp-left, .pp-right {
            height: auto !important;
            max-height: none !important;
            min-height: 0 !important;
        }
        .checkout-page-container {
            min-height: auto;
        }
    }
</style>

@push('js')
<script>
    (function () {
        const urlParams = new URLSearchParams(window.location.search);
        const orderType = urlParams.get('type') || 'takeaway';
        const tableId = urlParams.get('table') || '';
        const orderId = urlParams.get('order_id') || '';

        // Load Notes from Local Storage if present
        let storageNotes = '';
        const saved = localStorage.getItem('pos_cart');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                storageNotes = data.notes || '';
            } catch(e) {
                console.error(e);
            }
        }

        // Initialize badges
        const typeBadge = document.getElementById('orderTypeBadge');
        if (typeBadge) {
            if (orderType === 'dine_in') {
                typeBadge.innerText = "{{ __('Dine In') }}";
                typeBadge.className = "badge bg-warning text-dark px-3 py-2 text-uppercase fw-bold";
                
                // Show table badge
                const tableBadgeWrap = document.getElementById('tableBadgeContainer');
                const tableNumberBadge = document.getElementById('tableNumberBadge');
                if (tableBadgeWrap && tableNumberBadge) {
                    tableBadgeWrap.classList.remove('d-none');
                    tableBadgeWrap.classList.add('d-flex');
                    
                    // Match table name from Laravel $tables collections
                    const tables = @json($tables);
                    const matchedTable = tables.find(t => t.id == tableId);
                    tableNumberBadge.innerText = matchedTable ? matchedTable.name : (tableId || '—');
                }
            } else {
                typeBadge.innerText = "{{ __('Takeaway') }}";
                typeBadge.className = "badge bg-info text-white px-3 py-2 text-uppercase fw-bold";
            }
        }

        // Populate Notes and fields
        window.addEventListener('load', function () {
            // Notes
            const notesTextarea = document.getElementById('ppOrderNotes');
            if (notesTextarea) {
                @if(isset($existingOrder) && $existingOrder)
                    notesTextarea.value = storageNotes || {!! json_encode($existingOrder->notes ?? '') !!};
                @else
                    notesTextarea.value = storageNotes;
                @endif
            }

            // Trigger payment_panel initial rendering
            if (typeof ppRenderItems === 'function') {
                ppRenderItems();
            }
        });

        // Re-calculate change automatically when total updates
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (typeof ppCalculateChange === 'function') {
                    ppCalculateChange();
                }
            }, 100);
        });

        /* ── POS API Module ── */
        const POS = {
            isProcessing: false,
            
            async request(endpoint, payload) {
                if (this.isProcessing) return;
                this.setLoading(true);

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();
                    if (!response.ok) throw result;
                    return result;
                } catch (error) {
                    this.handleError(error);
                    throw error;
                } finally {
                    this.setLoading(false);
                }
            },

            setLoading(status) {
                this.isProcessing = status;
                const btnLater = document.getElementById('ppSaveLaterBtn');
                const btnNow = document.getElementById('ppPayNowBtn');
                [btnLater, btnNow].forEach(btn => {
                    if (!btn) return;
                    if (status) {
                        btn.dataset.originalHtml = btn.innerHTML;
                        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Processing...`;
                        btn.disabled = true;
                    } else {
                        btn.innerHTML = btn.dataset.originalHtml || btn.innerText;
                        btn.disabled = false;
                    }
                });
            },

            handleError(error) {
                console.error('POS API Error:', error);
                let message = "{{ __('An unexpected error occurred.') }}";

                if (error.errors) {
                    message = Object.values(error.errors).flat().join('\n');
                } else if (error.message) {
                    message = error.message;
                }

                if (window.showToast) {
                    showToast(message, 'error');
                } else {
                    alert(message);
                }
            }
        };

        /* ── processPayment Function ── */
        window.processPayment = async function (isPaid = true) {
            if (!window.cart || window.cart.length === 0) {
                if (window.showToast) {
                    showToast("{{ __('Your cart is empty.') }}", 'error');
                } else {
                    alert("{{ __('Your cart is empty.') }}");
                }
                return;
            }

            const payMethod = document.querySelector('input[name="pp_pay_method"]:checked')?.value || 'cash';

            if (isPaid && payMethod === 'qr') {
                const payerName = document.getElementById('ppPayerName')?.value.trim();
                const confirmed = confirm("{{ __('QR Pay selected. Click OK once mobile transaction is complete.') }}");
                if (!confirmed) {
                    return;
                }
            }

            const payload = {
                order_id: orderId ? parseInt(orderId) : null,
                order_type: orderType,
                table_id: tableId ? parseInt(tableId) : null,
                notes: document.getElementById('ppOrderNotes')?.value || '',
                items: window.cart.map(i => ({
                    menu_item_id: i.id,
                    quantity: i.qty
                })),
                payment_method: isPaid ? payMethod : null,
                paid_amount: isPaid ? (payMethod === 'qr' ? window.ppCurrentTotalUSD || 0 : (parseFloat(document.getElementById('ppCashReceived')?.value) || 0)) : 0,
                payer_name: isPaid && payMethod === 'qr' ? document.getElementById('ppPayerName')?.value.trim() : null,
                payer_account: isPaid && payMethod === 'qr' ? document.getElementById('ppPayerAccount')?.value.trim() : null
            };

            try {
                const result = await POS.request("{{ route('orders.store') }}", payload);
                if (result.success) {
                    localStorage.removeItem('pos_cart');
                    if (window.showToast) {
                        showToast(isPaid ? "{{ __('Order completed successfully!') }}" : "{{ __('Order saved to draft successfully!') }}", 'success');
                    }
                    
                    // Redirect to receipt or orders list after a brief delay
                    const receiptUrlPattern = "{{ route('orders.receipt', ':id') }}";
                    setTimeout(function () {
                        if (isPaid) {
                            window.location.href = receiptUrlPattern.replace(':id', result.order_id);
                        } else {
                            window.location.href = "{{ route('orders.index') }}";
                        }
                    }, 1000);
                }
            } catch (e) {
                // Handled in POS.handleError
            }
        };
        
    })();
</script>
@endpush
@endsection
