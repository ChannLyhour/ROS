@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div class="">
    <div class="pos-container" id="posApp">
        <div class="row g-0 h-100 position-relative overflow-hidden">
            <!-- Left: Menu Selection (8 Columns) -->
            <div class="col-lg-8 d-flex flex-column bg-light border-end overflow-hidden" style="height: calc(100vh - 80px);">
                <!-- POS Search & Categories -->
                <div class="p-3 bg-white shadow-sm border-bottom">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="header-info">
                            <h4 class="fw-black mb-1 responsive-h2" style="color: #0f172a; letter-spacing: -0.5px;">
                                @if(isset($existingOrder) && $existingOrder)
                                Resume #{{ $existingOrder->order_no }}
                                @else
                                New Order
                                @endif
                                </h2>
                                <p class="text-muted small mb-0 fw-medium">
                                    @if(isset($existingOrder) && $existingOrder)
                                    <span class="badge bg-warning-subtle text-warning border-warning border-opacity-25 px-2">Draft</span> Modification in progress
                                    @else
                                    <span class="badge bg-success-subtle text-success border-success border-opacity-25 px-2">New</span> Start a fresh service
                                    @endif
                                </p>
                        </div>

                        <div class="search-box flex-grow-1" style="max-width: 280px;">
                            <button class="nav-search-btn w-100 d-flex align-items-center justify-content-between"
                                data-bs-toggle="modal"
                                data-bs-target="#commandSearchModal"
                                onclick="window.searchType = 'categories';">
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                                    <span class="fw-semibold text-muted small">{{ __('Search...') }}</span>
                                </div>
                                <kbd class="kbd-shortcut ms-auto d-none d-sm-block">
                                    <span class="opacity-75">Ctrl</span> O
                                </kbd>
                            </button>
                        </div>

                    </div>

                    <div class="mt-3 pt-2 border-top bg-white z-index-10 w-100">
                        <div class="d-flex gap-2 overflow-auto hide-scrollbar pb-3 px-1">
                            <button class="btn btn-category active" data-category="all">
                                <i data-lucide="layout-grid" class="me-2" style="width: 14px;"></i> {{ __('All Items') }}
                            </button>
                            @foreach($categories as $cat)
                            <button class="btn btn-category shadow-sm" data-category="{{ $cat->id }}">
                                {{ $cat->name }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Menu Grid -->
                <div class="flex-grow-1 overflow-auto p-4">
                    <div class="row g-4" id="menuGrid">
                        @forelse($menuItems as $item)
                        <div class="col-xl-3 col-lg-4 col-md-6 menu-item-card" data-id="{{ $item->id }}" data-category="{{ $item->category_id }}" data-name="{{ strtolower($item->name) }}">
                            <div class="card h-100 border-0 shadow-sm rounded-xl overflow-hidden item-interactive" onclick="addToCart({{ json_encode($item) }})">
                                <div class="d-flex p-3 gap-3">
                                    <div class="product-image-wrapper flex-shrink-0">
                                        <img src="{{ $item->display_image }}" class="rounded-lg shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                        <div class="add-btn-mini">
                                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 d-flex flex-column justify-content-between overflow-hidden">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.9rem;">{{ $item->name }}</h6>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="extra-small text-muted fw-medium">{{ $item->category->name }}</span>
                                                <span class="text-success extra-small fw-bold">• {{ rand(1, 50) }} left</span>
                                            </div>
                                        </div>
                                        <div class="text-end mt-auto">
                                            <div class="price-text text-primary fw-black" style="font-size: 1.1rem;">
                                                <span class="extra-small fw-normal text-muted me-1">From</span>{{ $appSettings['currency'] }}{{ number_format($item->price, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <i data-lucide="frown" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                            <p class="text-muted">No items available in the menu.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right: Cart & Checkout (4 Columns) -->
            <div class="col-lg-4 d-flex flex-column bg-white shadow-lg cart-sidebar" id="cartSidebar" style="height: calc(100vh - 80px);">
                @include('admin.cart.index')
            </div>

            <!-- Mobile Cart Toggle -->
            <button class="btn btn-primary d-lg-none mobile-cart-toggle shadow-lg animate__animated animate__bounceIn animate__infinite animate__pulse" id="mobileCartToggle" onclick="toggleMobileCart()" style="animation-duration: 2s;">
                <div class="position-relative">
                    <i data-lucide="shopping-cart" style="width: 24px; height: 24px;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" id="mobileCartBadge" style="font-size: 0.6rem; padding: 0.25em 0.5em;">0</span>
                </div>
            </button>
            <div class="sidebar-overlay d-lg-none" id="cartOverlay" onclick="toggleMobileCart()"></div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-black">Complete Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4" id="modalTotalDisplayArea">
                    {{-- Area will be updated by JS --}}
                </div>

                <div class="payment-methods row g-2 mb-4">
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="pay_method" id="pay_cash" value="cash" checked>
                        <label class="btn btn-outline-primary w-100 py-3" for="pay_cash">
                            <i data-lucide="banknote" class="d-block mb-1 mx-auto"></i>
                            <span class="small fw-bold">Cash</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="pay_method" id="pay_card" value="card">
                        <label class="btn btn-outline-primary w-100 py-3" for="pay_card">
                            <i data-lucide="credit-card" class="d-block mb-1 mx-auto"></i>
                            <span class="small fw-bold">Card</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="pay_method" id="pay_qr" value="qr">
                        <label class="btn btn-outline-primary w-100 py-3" for="pay_qr">
                            <i data-lucide="qr-code" class="d-block mb-1 mx-auto"></i>
                            <span class="small fw-bold">QR Pay</span>
                        </label>
                    </div>
                </div>

                <div id="qrPaymentInfo" class="p-3 bg-light rounded-lg border mb-4 d-none">
                    <div class="mb-3">
                        <h6 class="fw-bold mb-2">QR Pay Instructions</h6>
                        <p class="small text-muted mb-0">Scan the QR code below with your mobile wallet, then enter the payer name shown on the phone receipt.</p>
                    </div>
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/myqr.jpg') }}" alt="QR Code" class="img-fluid" style="max-width: 240px;">
                    </div>
                    <div class="text-center">
                        <a href="{{ asset('images/myqr.jpg') }}" target="_blank" class="btn btn-outline-secondary btn-sm">Open QR in new tab</a>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-12">
                            <label class="extra-small fw-black text-muted text-uppercase mb-1 d-block">Paid By</label>
                            <input type="text" id="payerName" class="form-control premium-field" placeholder="Customer account name">
                        </div>
                        <div class="col-12">
                            <label class="extra-small fw-black text-muted text-uppercase mb-1 d-block">Account / Phone</label>
                            <input type="text" id="payerAccount" class="form-control premium-field" placeholder="Phone number or account id">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="info-label mb-2">Internal Order Notes :</label>
                    <textarea id="orderNotes" class="form-control premium-field" rows="2" placeholder="Special requests, allergies, etc."></textarea>
                </div>

                <!-- Cash Payment Calculator -->
                <div id="cashCalculator" class="p-3 bg-light rounded-lg border mb-4 animate__animated animate__fadeIn">
                    <div class="row g-2 align-items-center">
                        <div class="col-6">
                            <label class="extra-small fw-black text-muted text-uppercase mb-1 d-block">Amount Paid</label>
                            <div class="input-group premium-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 py-1 px-2 fw-bold text-muted">{{ $appSettings['currency'] }}</span>
                                <input type="number" id="cashReceived" class="form-control premium-field border-start-0 py-1" step="0.01" placeholder="0.00" oninput="calculateChange()">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="extra-small fw-black text-muted text-uppercase mb-1 d-block">Change Due</label>
                            <div class="h4 fw-black mb-0 text-success" id="changeAmount">{{ $appSettings['currency'] }}0.00</div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-warning w-100 py-3 fw-bold rounded-lg border shadow-sm" onclick="processPayment(false)">
                            <i data-lucide="clock" class="me-2" style="width: 18px;"></i> SAVE (PAY LATER)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-success w-100 py-3 fw-bold rounded-lg" onclick="processPayment(true)">
                            <i data-lucide="check" class="me-2" style="width: 18px;"></i> PAY NOW
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    body {
        background-color: #f8fafc !important;
    }

    .pos-container {
        height: calc(100vh - 80px);
    }

    .fw-black {
        font-weight: 900;
    }

    .rounded-lg {
        border-radius: 12px !important;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    .z-index-10 {
        z-index: 10 !important;
    }

    /* Category Buttons */
    .btn-category {
        padding: 10px 24px;
        border-radius: 100px;
        background: #fff;
        color: #64748b;
        border: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        display: flex;
        align-items: center;
    }

    .btn-category:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .btn-category.active {
        background: #f08913;
        color: white;
        border-color: #f08913;
        box-shadow: 0 4px 12px rgba(240, 137, 19, 0.3) !important;
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .menu-item-card .card {
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9 !important;
    }

    .menu-item-card .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        border-color: #f08913 !important;
    }

    .item-interactive {
        cursor: pointer;
    }

    .price-pill {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        color: #0f172a;
        padding: 6px 14px;
        border-radius: 100px;
        font-weight: 900;
        font-size: 0.9rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-premium-lg {
        background: linear-gradient(135deg, #f08913 0%, #d97706 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-premium-lg:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(240, 137, 19, 0.4) !important;
    }

    /* Cart Item Styling */
    .cart-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 12px;
        padding: 12px;
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .cart-item:hover {
        border-color: #f0891320;
        background: #f8fafc;
    }

    .qty-controls {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 50px;
        padding: 4px;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        font-weight: bold;
        color: #0f172a;
        transition: all 0.2s;
    }

    .qty-btn:hover {
        background: #f08913;
        color: #fff;
    }

    .btn-premium-toggle {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        border-radius: 10px;
        padding: 12px 10px;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.2s ease;
        letter-spacing: 0.5px;
    }

    .btn-check:checked+.btn-premium-toggle {
        background: #f08913;
        border-color: #f08913;
        color: #fff;
        box-shadow: 0 4px 12px rgba(240, 137, 19, 0.2);
    }

    /* Select2 Premium Styling */
    .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        height: 48px !important;
        background-color: #f8fafc !important;
        transition: all 0.3s;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 15px !important;
        color: #64748b !important;
        font-weight: 600 !important;
        line-height: 48px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: #f08913 !important;
        box-shadow: 0 0 0 4px rgba(240, 137, 19, 0.1) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 12px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-dropdown {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
    }

    .select2-search__field {
        border-radius: 8px !important;
        padding: 8px 12px !important;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #f08913 !important;
    }

    .btn-check:checked+.btn-premium-toggle i {
        opacity: 1;
    }

    /* Modal Animation */
    #cashCalculator {
        transition: all 0.3s ease;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
    }

    .transform-active:active {
        transform: scale(0.97);
    }

    .nav-search-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .nav-search-btn:hover {
        background: #fff;
        border-color: #f08913;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .kbd-shortcut {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        color: #64748b;
        display: inline-block;
        font-family: inherit;
        font-size: 0.65rem;
        font-weight: 700;
        line-height: 1;
        padding: 4px 6px;
        white-space: nowrap;
    }

    .fw-semibold {
        font-weight: 600 !important;
    }

    /* Responsive Sidebar Drawer */
    @media (max-width: 991.98px) {
        .header-info {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
        }

        .search-box {
            max-width: 100% !important;
            width: 100%;
        }

        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 90% !important;
            max-width: 400px;
            z-index: 1040;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100vh !important;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
        }

        .cart-sidebar.active {
            right: 0;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            z-index: 1030;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-cart-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            left: auto;
            transform: none;
            z-index: 1000;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #f08913 !important;
            border: 3px solid rgba(255,255,255,0.3);
            color: #fff !important;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(240, 137, 19, 0.5);
            transition: all 0.3s ease;
            animation: pulse-orange 2s infinite;
        }

        .mobile-cart-toggle:active {
            transform: scale(0.9);
        }

        @keyframes pulse-orange {
            0% { box-shadow: 0 0 0 0 rgba(240, 137, 19, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(240, 137, 19, 0); }
            100% { box-shadow: 0 0 0 0 rgba(240, 137, 19, 0); }
        }

        .pos-container {
            height: 100vh;
        }

        .col-lg-8 {
            height: 100vh !important;
            padding-bottom: 100px !important;
        }

        .cart-badge {
            font-size: 0.65rem;
            border: 2px solid #f08913;
        }
    }

    @media (max-width: 575.98px) {
        .menu-item-card .card .d-flex {
            gap: 10px !important;
            padding: 12px !important;
        }

        .product-image-wrapper img {
            width: 70px !important;
            height: 70px !important;
        }

        .price-text {
            font-size: 1rem !important;
        }

        .header-info h2 {
            font-size: 1.4rem !important;
        }
    }

    .rounded-xl {
        border-radius: 16px !important;
    }

    .product-image-wrapper {
        position: relative;
    }

    .add-btn-mini {
        position: absolute;
        bottom: -5px;
        right: -5px;
        width: 24px;
        height: 24px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        color: #64748b;
        z-index: 2;
    }

    .menu-item-card .card:hover {
        border-color: #f08913 !important;
        background: #fffaf5;
        transform: translateY(-5px);
    }

    .menu-item-card .card:hover .add-btn-mini {
        background: #f08913;
        color: #fff;
        border-color: #f08913;
    }

    .customer-selector {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 15px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .customer-selector:hover {
        border-color: #f08913;
        background: #fffaf5;
    }

    .cart-header-title {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

@push('js')
<script>
    let cart = {!! json_encode($initialCart) !!};
    const taxRate = parseFloat("{{ $appSettings['tax_percentage'] }}") / 100;
    const currency = "{{ $appSettings['currency'] }}";
    const exchangeRate = parseFloat("{{ $appSettings['exchange_rate'] }}") || 4100;

    // Filter Logic
    function filterByCategory(catId) {
        document.querySelectorAll('.btn-category').forEach(btn => {
            if (btn.dataset.category == catId || (catId === 'all' && btn.dataset.category === 'all')) {
                btn.click();
                btn.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            }
        });
    }
    window.filterByCategory = filterByCategory;

    document.querySelectorAll('.btn-category').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelector('.btn-category.active').classList.remove('active');
            this.classList.add('active');
            const cat = this.dataset.category;

            document.querySelectorAll('.menu-item-card').forEach(card => {
                if (cat === 'all' || card.dataset.category === cat) {
                    card.classList.remove('d-none');
                } else {
                    card.classList.add('d-none');
                }
            });
        });
    });

    // Search Logic
    const menuSearch = document.getElementById('menuSearch');
    if (menuSearch) {
        menuSearch.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.menu-item-card').forEach(card => {
                if (card.dataset.name.includes(term)) {
                    card.classList.remove('d-none');
                } else {
                    card.classList.add('d-none');
                }
            });
        });
    }

    function toggleTable() {
        const checkedEl = document.querySelector('input[name="orderType"]:checked');
        if (!checkedEl) return;

        const type = checkedEl.value;
        const container = document.getElementById('tableContainer');
        if (type === 'dine_in') {
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
            document.getElementById('tableId').disabled = false;
        } else {
            container.style.opacity = '0';
            container.style.transform = 'translateY(-10px)';
            document.getElementById('tableId').disabled = true;
        }
    }

    function addToCart(item) {
        const existing = cart.find(i => i.id === item.id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                ...item,
                qty: 1
            });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id == id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) cart = cart.filter(i => i.id != id);
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        if (!container) return;

        if (!Array.isArray(cart) || cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 opacity-50">
                    <i data-lucide="shopping-bag" class="mb-3" style="width: 48px; height: 48px;"></i>
                    <p class="fw-bold">Your cart is empty</p>
                </div>`;
            updateTotals(0);
            if (window.lucide) lucide.createIcons();
            return;
        }

        let html = '';
        let subtotal = 0;
        cart.forEach(item => {
            if (!item) return;
            const price = parseFloat(item.price) || 0;
            const qty = parseInt(item.qty) || 0;
            const lineTotal = price * qty;
            subtotal += lineTotal;

            const itemId = item.id;
            const itemName = item.name || 'Unknown Item';
            const itemImg = item.display_image || "{{ asset('images/placeholder.jpg') }}";

            html += `
                <div class="cart-item" data-id="${itemId}">
                    <img src="${itemImg}" class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark extra-small text-truncate" style="max-width: 150px;">${itemName}</div>
                        <div class="text-primary fw-bold small">${currency}${lineTotal.toFixed(2)}</div>
                    </div>
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateQty('${itemId}', -1)">-</button>
                        <span class="mx-2 fw-bold small">${qty}</span>
                        <button class="qty-btn" onclick="updateQty('${itemId}', 1)">+</button>
                    </div>
                </div>`;
        });
        container.innerHTML = html;
        updateTotals(subtotal);
        updateMobileBadge(cart.reduce((acc, item) => acc + (item.qty || 0), 0));

        const countLabel = document.getElementById('cartItemCountLabel');
        if (countLabel) countLabel.innerText = `${cart.length} items selected`;

        if (window.lucide) lucide.createIcons();
        saveCartToStorage();
    }

    function updateTotals(subtotal) {
        const tax = subtotal * taxRate;
        const total = subtotal + tax;
        const totalRiel = Math.round(total * exchangeRate);
        const displayType = document.querySelector('input[name="displayCurrency"]:checked')?.value || 'USD';

        // Header summary
        document.getElementById('subtotalLabel').innerText = `$${subtotal.toFixed(2)}`;
        document.getElementById('taxLabel').innerText = `$${tax.toFixed(2)}`;

        // Total display logic
        let totalHTML = '';
        let modalTotalHTML = '';

        if (displayType === 'USD') {
            totalHTML = `
                <span class="h3 fw-black text-primary mb-0 d-block">$${total.toFixed(2)}</span>
                <span class="badge bg-light text-muted fw-bold border" style="font-size: 0.7rem;">៛${totalRiel.toLocaleString()}</span>
            `;
            modalTotalHTML = `
                <h1 class="fw-black text-primary mb-0">$${total.toFixed(2)}</h1>
                <div class="badge bg-primary-subtle text-primary fw-bold mb-3 px-3 py-2" style="font-size: 1rem;">៛${totalRiel.toLocaleString()}</div>
                <p class="text-muted small">Total amount due (USD Primary)</p>
            `;
        } else {
            totalHTML = `
                <span class="h3 fw-black text-primary mb-0 d-block">៛${totalRiel.toLocaleString()}</span>
                <span class="badge bg-light text-muted fw-bold border" style="font-size: 0.7rem;">$${total.toFixed(2)}</span>
            `;
            modalTotalHTML = `
                <h1 class="fw-black text-primary mb-0">៛${totalRiel.toLocaleString()}</h1>
                <div class="badge bg-primary-subtle text-primary fw-bold mb-3 px-3 py-2" style="font-size: 1rem;">$${total.toFixed(2)}</div>
                <p class="text-muted small">Total amount due (Riel Primary)</p>
            `;
        }

        document.getElementById('totalDisplayArea').innerHTML = totalHTML;
        document.getElementById('modalTotalDisplayArea').innerHTML = modalTotalHTML;

        // For calculation purposes, we still need these IDs accessible or handle logic differently
        window.currentTotalUSD = total;
        calculateChange();
    }

    function calculateChange() {
        const total = window.currentTotalUSD || 0;
        const paid = parseFloat(document.getElementById('cashReceived').value) || 0;
        const change = paid - total;

        const changeDisplay = document.getElementById('changeAmount');
        if (change >= 0) {
            changeDisplay.innerText = `$${change.toFixed(2)}`;
            changeDisplay.className = 'h4 fw-black mb-0 text-success';
        } else {
            changeDisplay.innerText = `$${Math.abs(change).toFixed(2)}`;
            changeDisplay.className = 'h4 fw-black mb-0 text-danger';
        }
    }

    // Toggle payment UI based on selected method
    document.querySelectorAll('input[name="pay_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const calc = document.getElementById('cashCalculator');
            const qrInfo = document.getElementById('qrPaymentInfo');
            if (this.id === 'pay_cash') {
                calc.classList.remove('d-none');
                qrInfo.classList.add('d-none');
            } else if (this.id === 'pay_qr') {
                calc.classList.add('d-none');
                qrInfo.classList.remove('d-none');
            } else {
                calc.classList.add('d-none');
                qrInfo.classList.add('d-none');
            }
        });
    });

    function clearCart() {
        if (!confirm('Are you sure you want to clear the entire order?')) return;
        cart = [];
        localStorage.removeItem('pos_cart');
        renderCart();
    }

    // Auto-fill payment amount on modal show
    document.getElementById('paymentModal').addEventListener('show.bs.modal', function() {
        const total = window.currentTotalUSD || 0;
        document.getElementById('cashReceived').value = total.toFixed(2);
        calculateChange();
    });

    /**
     * POS Service Module
     * Encapsulates AJAX calls and UI state management
     */
    const POS = {
        isProcessing: false,
        btnSelector: '.btn-orange[onclick*="processPayment"]',

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
            const btns = document.querySelectorAll(this.btnSelector);
            btns.forEach(btn => {
                if (status) {
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Processing...`;
                    btn.disabled = true;
                } else {
                    btn.innerHTML = btn.dataset.originalHtml || 'PAYMENT & CHECKOUT';
                    btn.disabled = false;
                }
            });
        },

        handleError(error) {
            console.error('POS API Error:', error);
            let message = 'An unexpected error occurred.';

            if (error.errors) {
                message = Object.values(error.errors).flat().join('\n');
            } else if (error.message) {
                message = error.message;
            }

            alert(message);
        }
    };

    /**
     * Primary Order Processing Function
     */
    async function processPayment(isPaid = true) {
        if (cart.length === 0) {
            alert('Your cart is empty.');
            return;
        }

        const type = document.querySelector('input[name="orderType"]:checked')?.value || 'takeaway';
        const tableId = document.getElementById('tableId')?.value;

        if (type === 'dine_in' && !tableId) {
            alert('Please assign a table for Dine-In.');
            return;
        }

        const payMethod = document.querySelector('input[name="pay_method"]:checked')?.value || 'cash';

        if (isPaid && payMethod === 'qr') {
            const payerName = document.getElementById('payerName')?.value.trim();
            const confirmed = confirm(`QR Pay selected.${payerName ? ` Paid by ${payerName}.` : ''} Click OK once the phone payment is completed.`);
            if (!confirmed) {
                return;
            }
        }

        const payload = {
            order_id: {!! json_encode($existingOrder->id ?? null) !!},
            order_type: type,
            table_id: tableId,
            notes: document.getElementById('orderNotes')?.value,
            items: cart.map(i => ({
                menu_item_id: i.id,
                quantity: i.qty
            })),
            payment_method: isPaid ? payMethod : null,
            paid_amount: isPaid ? (payMethod === 'qr' ? window.currentTotalUSD || 0 : (parseFloat(document.getElementById('cashReceived')?.value) || 0)) : 0,
            payer_name: isPaid && payMethod === 'qr' ? document.getElementById('payerName')?.value.trim() : null,
            payer_account: isPaid && payMethod === 'qr' ? document.getElementById('payerAccount')?.value.trim() : null
        };

        try {
            const result = await POS.request('/api/v1/orders', payload);
            if (result.success) {
                localStorage.removeItem('pos_cart');
                if (isPaid && payMethod === 'qr') {
                    const payer = payload.payer_name || 'mobile account';
                    alert(`Payment received by ${payer}.`);
                }
                window.location.href = "{{ route('orders.index') }}";
            }
        } catch (e) {
            // Error managed by POS.handleError
        }
    }

    // Storage Logic
    function saveCartToStorage() {
        const data = {
            items: cart,
            type: document.querySelector('input[name="orderType"]:checked') ? document.querySelector('input[name="orderType"]:checked').value : 'dine_in',
            table_id: document.getElementById('tableId').value,
            notes: document.getElementById('orderNotes').value
        };
        localStorage.setItem('pos_cart', JSON.stringify(data));
    }

    function loadCartFromStorage() {
        // If we have an existing order from backend, prioritize it over localStorage
        @if(isset($existingOrder) && $existingOrder)
        if (document.getElementById('orderNotes')) {
            document.getElementById('orderNotes').value = {!! json_encode($existingOrder->notes ?? '') !!};
        }
        if (document.getElementById('tableId')) {
            document.getElementById('tableId').value = "{{ $existingOrder->table_id ?? '' }}";
        }
        if ("{{ $existingOrder->order_type }}") {
            const radio = document.getElementById("{{ $existingOrder->order_type }}");
            if (radio) radio.checked = true;
        }
        renderCart();
        return;
        @endif

        const saved = localStorage.getItem('pos_cart');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                cart = data.items || [];
                if (data.type) {
                    const radio = document.getElementById(data.type);
                    if (radio) radio.checked = true;
                }
                if (data.table_id) document.getElementById('tableId').value = data.table_id;
                if (data.notes) document.getElementById('orderNotes').value = data.notes;
            } catch (e) {
                console.error("Failed to load cart", e);
            }
        }
    }

    function persistCartManually() {
        saveCartToStorage();
        showToast('Order saved to local storage.', 'success');
    }

    // Initialize state
    window.addEventListener('load', function() {
        loadCartFromStorage();
        renderCart();
        toggleTable();

        // Re-initialize Select2 if needed
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#posApp')
            });
        }

        if (window.lucide) lucide.createIcons();
    });

    function toggleMobileCart() {
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('cartOverlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    function updateMobileBadge(count) {
        const badge = document.getElementById('mobileCartBadge');
        if (badge) {
            badge.innerText = count;
            const toggle = document.getElementById('mobileCartToggle');
            if (count > 0) {
                toggle.classList.add('animate__pulse');
            } else {
                toggle.classList.remove('animate__pulse');
            }
        }
    }
</script>
@endpush
@endsection
