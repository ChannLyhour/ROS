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
                            </h4>
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
                            <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden item-interactive" data-item="{{ json_encode($item) }}" onclick="addToCart(this)">
                                <div class="position-relative">
                                    <img src="{{ $item->display_image }}" class="card-img-top" style="height: 160px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                    <div class="price-pill">{{ $appSettings['currency'] }}{{ number_format($item->price, 2) }}</div>
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $item->name }}</h6>
                                    <p class="extra-small text-muted mb-0">{{ $item->category->name }}</p>
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



<style>
    body {
        background-color: #f8fafc !important;
    }

    .pos-container {
        height: calc(100vh - 80px);
    }

    .fw-bold {
        font-weight: 700 !important;
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Category Buttons */
    .btn-category {
        padding: 8px 16px;
        border-radius: 4px;
        background: #fff;
        color: #495057;
        border: 1px solid #ced4da;
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
        display: flex;
        align-items: center;
    }

    .btn-category:hover {
        background: #e9ecef;
        color: #212529;
    }

    .btn-category.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    /* Menu Items */
    .menu-item-card .card {
        border: 1px solid #dee2e6 !important;
        cursor: pointer;
    }

    .menu-item-card .card:hover {
        border-color: #0d6efd !important;
        background: #f8f9fa;
    }

    .price-pill {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.9);
        color: #212529;
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.85rem;
        border: 1px solid #dee2e6;
    }

    /* Cart Items */
    .cart-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .qty-controls {
        display: flex;
        align-items: center;
        background: #e9ecef;
        border-radius: 4px;
        padding: 2px;
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: none;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        font-weight: bold;
    }

    .qty-btn:hover {
        background: #0d6efd;
        color: #fff;
    }

    /* Select2 Basic clean styling */
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        height: 38px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057 !important;
        line-height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd !important;
    }

    .nav-search-btn {
        background: #fff;
        border: 1px solid #ced4da;
        padding: 6px 12px;
        border-radius: 4px;
        transition: border-color 0.15s ease-in-out;
    }

    .nav-search-btn:hover {
        border-color: #0d6efd;
    }

    .kbd-shortcut {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 3px;
        padding: 2px 4px;
        font-size: 0.75rem;
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
        if (item && typeof item.getAttribute === 'function') {
            const dataStr = item.getAttribute('data-item');
            if (dataStr) {
                try {
                    item = JSON.parse(dataStr);
                } catch (e) {
                    console.error('Error parsing item JSON:', e);
                    return;
                }
            }
        }

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

        if (displayType === 'USD') {
            totalHTML = `
                <span class="h3 fw-black text-primary mb-0 d-block">$${total.toFixed(2)}</span>
                <span class="badge bg-light text-muted fw-bold border" style="font-size: 0.7rem;">៛${totalRiel.toLocaleString()}</span>
            `;
        } else {
            totalHTML = `
                <span class="h3 fw-black text-primary mb-0 d-block">៛${totalRiel.toLocaleString()}</span>
                <span class="badge bg-light text-muted fw-bold border" style="font-size: 0.7rem;">$${total.toFixed(2)}</span>
            `;
        }

        document.getElementById('totalDisplayArea').innerHTML = totalHTML;
        window.currentTotalUSD = total;
    }

    function goToCheckout(btn) {
        if (cart.length === 0) {
            alert('Your cart is empty.');
            return;
        }

        const type = document.querySelector('input[name="orderType"]:checked')?.value || 'takeaway';
        const tableId = document.getElementById('tableId')?.value || '';

        if (type === 'dine_in' && !tableId) {
            alert('Please assign a table for Dine-In.');
            return;
        }

        // Save current cart state to local storage
        saveCartToStorage();

        // Redirect to pos/checkout page
        const orderId = {!! json_encode($existingOrder->id ?? null) !!};
        let url = "{{ route('pos.checkout') }}?type=" + encodeURIComponent(type);
        if (tableId) {
            url += "&table=" + encodeURIComponent(tableId);
        }
        if (orderId) {
            url += "&order_id=" + encodeURIComponent(orderId);
        }
        window.location.href = url;
    }

    // Storage Logic
    function saveCartToStorage() {
        const tableEl = document.getElementById('tableId');
        const notesEl = document.getElementById('orderNotes');
        const data = {
            items: cart,
            type: document.querySelector('input[name="orderType"]:checked') ? document.querySelector('input[name="orderType"]:checked').value : 'dine_in',
            table_id: tableEl ? tableEl.value : '',
            notes: notesEl ? notesEl.value : ''
        };
        localStorage.setItem('pos_cart', JSON.stringify(data));
    }

    function loadCartFromStorage() {
        // If we have an existing order from backend, prioritize it over localStorage
        @if(isset($existingOrder) && $existingOrder)
        const notesEl = document.getElementById('orderNotes');
        if (notesEl) {
            notesEl.value = {!! json_encode($existingOrder->notes ?? '') !!};
        }
        const tableEl = document.getElementById('tableId');
        if (tableEl) {
            tableEl.value = "{{ $existingOrder->table_id ?? '' }}";
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
                const tableEl = document.getElementById('tableId');
                if (tableEl && data.table_id) tableEl.value = data.table_id;

                const notesEl = document.getElementById('orderNotes');
                if (notesEl && data.notes) notesEl.value = data.notes;
            } catch (e) {
                console.error("Failed to load cart", e); 
            }
        }
    }

    function clearCart() {
        if (!confirm('Are you sure you want to clear the entire order?')) return;
        cart = [];
        localStorage.removeItem('pos_cart');
        renderCart();
    }

    function persistCartManually() {
        if (!Array.isArray(cart) || cart.length === 0) {
            if (typeof showToast === 'function') {
                showToast('Your cart is empty.', 'error');
            } else {
                alert('Your cart is empty.');
            }
            return;
        }

        const type = document.querySelector('input[name="orderType"]:checked')?.value || 'takeaway';
        const tableId = document.getElementById('tableId')?.value || '';

        if (type === 'dine_in' && !tableId) {
            if (typeof showToast === 'function') {
                showToast('Please assign a table for Dine-In.', 'error');
            } else {
                alert('Please assign a table for Dine-In.');
            }
            return;
        }

        const orderId = {!! json_encode($existingOrder->id ?? null) !!};
        const url = orderId ? "{{ route('orders.update', ':id') }}".replace(':id', orderId) : "{{ route('orders.store') }}";
        const method = orderId ? 'PUT' : 'POST';

        const payload = {
            order_id: orderId ? parseInt(orderId) : null,
            order_type: type,
            table_id: tableId ? parseInt(tableId) : null,
            items: cart.map(i => ({
                menu_item_id: i.id,
                quantity: i.qty
            })),
            notes: document.getElementById('orderNotes')?.value || '',
            payment_method: null,
            paid_amount: 0
        };

        // UI feedback - locate save button
        const saveBtns = document.querySelectorAll('[onclick="persistCartManually()"]');
        saveBtns.forEach(btn => {
            btn.disabled = true;
            btn.dataset.origHtml = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;
        });

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ...payload,
                _method: method
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                localStorage.removeItem('pos_cart');
                if (typeof showToast === 'function') {
                    showToast(orderId ? 'Draft order updated in database!' : 'Draft order saved to database!', 'success');
                } else {
                    alert(orderId ? 'Draft order updated in database!' : 'Draft order saved to database!');
                }

                // Brief redirect to clean state/index
                setTimeout(() => {
                    window.location.href = "{{ route('orders.index') }}";
                }, 1000);
            } else {
                throw new Error(result.message || 'Failed to persist order.');
            }
        })
        .catch(err => {
            console.error('AJAX Persist Error:', err);
            let message = 'Failed to save order to database.';
            if (err.errors) {
                message = Object.values(err.errors).flat().join('\n');
            } else if (err.message) {
                message = err.message;
            }

            if (typeof showToast === 'function') {
                showToast(message, 'error');
            } else {
                alert(message);
            }
        })
        .finally(() => {
            saveBtns.forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.origHtml || `<i data-lucide="save" style="width: 16px;"></i>`;
            });
            if (window.lucide) lucide.createIcons();
        });
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
            if (toggle) {
                if (count > 0) {
                    toggle.classList.add('animate__pulse');
                } else {
                    toggle.classList.remove('animate__pulse');
                }
            }
        }
    }

    // Global scope registrations
    window.toggleTable = toggleTable;
    window.addToCart = addToCart;
    window.updateQty = updateQty;
    window.renderCart = renderCart;
    window.updateTotals = updateTotals;
    window.goToCheckout = goToCheckout;
    window.saveCartToStorage = saveCartToStorage;
    window.loadCartFromStorage = loadCartFromStorage;
    window.clearCart = clearCart;
    window.persistCartManually = persistCartManually;
    window.toggleMobileCart = toggleMobileCart;
    window.updateMobileBadge = updateMobileBadge;
</script>
@endpush
@endsection