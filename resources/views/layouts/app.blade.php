<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') | {{ $appSettings['name'] }}</title>
    <link rel="icon" type="image/x-icon" href="{{ $appSettings['favicon'] }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">

        @guest
        @yield('content')
        @else
        @php
            $isPos = request()->routeIs('pos.index') || request()->routeIs('orders.create') || request()->routeIs('orders.edit') || request()->routeIs('pos.checkout');
        @endphp
        <div class="admin-layout {{ $isPos ? 'hide-sidebar' : '' }}" id="adminLayout">
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            @if(!$isPos)
            <x-sidebar />
            @endif
            <main class="main-content {{ $isPos ? 'ms-0 w-100' : '' }}">
                <x-navbar />
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- GLOBAL DELETE MODAL -->
        <div class="modal fade animate__animated animate__fadeIn" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 24px;">
                    <div class="modal-body p-4 p-md-5 text-center">
                        <div class="mb-4">
                            <div class="delete-icon-wrapper mx-auto bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center animate__animated animate__pulse animate__infinite" style="width: 90px; height: 90px;">
                                <i data-lucide="trash-2" style="width: 45px; height: 45px;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-2" style="color: #1e293b;">{{ __('Confirm Delete') }}</h4>
                        <p class="text-muted mb-4">{{ __('Are you sure you want to permanently delete') }} <strong id="deleteItemName" class="text-dark"></strong>? {{ __('This action cannot be reversed.') }}</p>
                        <div class="d-flex flex-column gap-2">
                            <button type="button" id="confirmDeleteBtn" class="btn btn-danger py-3 fw-bold shadow-sm" style="border-radius: 14px;">
                                <i data-lucide="trash-2" class="me-2" style="width: 18px;"></i> {{ __('Yes, Delete Permanently') }}
                            </button>
                            <button type="button" class="btn btn-white border-0 text-muted py-2 fw-bold" data-bs-dismiss="modal">{{ __('Cancel & Return') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endguest
    </div>

    <!-- GLOBAL COMMAND SEARCH MODAL -->
    <div class="modal fade" id="commandSearchModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(10px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: #fff;">
                <div class="modal-header border-0 p-4 pb-0 d-flex flex-column align-items-stretch">
                    <div class="d-flex align-items-center justify-content-between mb-3 bg-primary bg-opacity-10 p-3 rounded-pill">
                        <div class="d-flex align-items-center gap-2 small text-primary fw-bold">
                            <i data-lucide="info" style="width: 18px; height: 18px;"></i>
                            <span>Use UP/DOWN <kbd class="bg-primary text-white border-0 px-2 py-0">↑</kbd> <kbd class="bg-primary text-white border-0 px-2 py-0">↓</kbd> to browse, ENTER <kbd class="bg-primary text-white border-0 px-2 py-0">↵</kbd> to select.</span>
                        </div>
                    </div>
                    <div class="position-relative">
                        <i data-lucide="search" class="position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; z-index: 10;"></i>
                        <input type="text" id="commandSearchInput" class="form-control border-2 py-3 fw-bold" placeholder="{{ __('Search by keyword') }}" style="border-radius: 12px; font-size: 1.1rem; border-color: #f1f5f9; padding-left: 50px !important;">
                        <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                            <kbd class="bg-light text-muted border px-2 py-1 small" data-bs-dismiss="modal" style="cursor: pointer;">Esc</kbd>
                        </div>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div id="commandSearchResults" class="overflow-auto" style="max-height: 400px; padding: 10px 0;">
                        <!-- Results will be injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATIONS  -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- LANGUAGE SELECTION MODAL -->
    <div class="modal fade animate__animated animate__fadeIn" id="languageModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-black mb-0" style="color: #0f172a;">{{ __('Select Language') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">{{ __('Choose your preferred language for the system interface.') }}</p>

                    <div class="d-flex flex-column gap-3">
                        <!-- English -->
                        <a href="javascript:void(0)" onclick="switchLanguage('{{ route('lang.switch', 'en') }}', this)" class="language-card {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="flag-icon">🇺🇸</div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold lang-name">English</div>
                                    <div class="text-muted extra-small">United States</div>
                                </div>
                                @if(app()->getLocale() == 'en')
                                <span class="badge bg-primary rounded-pill px-3">{{ __('Current') }}</span>
                                @endif
                            </div>
                        </a>

                        <!-- Khmer -->
                        <a href="javascript:void(0)" onclick="switchLanguage('{{ route('lang.switch', 'kh') }}', this)" class="language-card {{ app()->getLocale() == 'kh' ? 'active' : '' }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="flag-icon">🇰🇭</div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold lang-name text-khmer">ភាសាខ្មែរ</div>
                                    <div class="text-muted extra-small">Kingdom of Cambodia</div>
                                </div>
                                @if(app()->getLocale() == 'kh')
                                <span class="badge bg-primary rounded-pill px-3">{{ __('Current') }}</span>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .toast-magic {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            border-left: 5px solid var(--primary-color);
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .language-card {
            display: block;
            padding: 20px;
            border: 2px solid #f1f5f9;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
        }

        .language-card:hover {
            border-color: #f08913;
            background: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .language-card.active {
            border-color: #f08913;
            background: #fffaf5;
        }

        .flag-icon {
            font-size: 2rem;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border-radius: 12px;
        }

        .lang-name {
            font-size: 1.1rem;
            color: #0f172a;
        }

        .text-khmer {
            font-family: 'Kantumruy Pro', sans-serif;
        }
    </style>

    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('js')
    <script>
        lucide.createIcons();

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });
        });

        // ToastMagic Function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast-magic ${type}`;
            const icon = type === 'success' ? 'check-circle' : 'alert-circle';

            toast.innerHTML = `
                <i data-lucide="${icon}"></i>
                <span>${message}</span>
            `;

            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => toast.classList.add('show'), 100);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        }

        // Session Flash Listener
        @if(session('success')) showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error')) showToast("{{ session('error') }}", 'error');
        @endif

        // Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const adminLayout = document.getElementById('adminLayout');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                const isMobile = window.innerWidth < 992;
                if (isMobile) {
                    sidebar?.classList.toggle('active');
                    sidebarOverlay?.classList.toggle('active');
                } else {
                    sidebar?.classList.toggle('collapsed');
                    adminLayout?.classList.toggle('collapsed-sidebar');
                }
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar?.classList.remove('active');
                sidebarOverlay?.classList.remove('active');
            });
        }

        // Global Delete Confirmation Logic
        let currentDeleteFormId = null;

        window.confirmDelete = function(formId, itemName) {
            currentDeleteFormId = formId;
            const nameSpan = document.getElementById('deleteItemName');
            if (nameSpan) nameSpan.innerText = itemName;

            const modalElement = document.getElementById('deleteConfirmModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            // Re-render icons in modal
            lucide.createIcons();
        };

        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (currentDeleteFormId) {
                    const form = document.getElementById(currentDeleteFormId);
                    if (form) form.submit();
                }
            });
        }

        // Language Switcher with Delay
        window.switchLanguage = function(url, element) {
            // Add visual feedback
            element.style.opacity = '0.5';
            element.style.pointerEvents = 'none';
            element.innerHTML = `
                <div class="d-flex align-items-center justify-content-center w-100 py-2">
                    <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                </div>
            `;

            // Artificial delay for premium feel
            setTimeout(() => {
                window.location.href = url;
            }, 600);
        }
        // Global Command Search Logic
        // Global Command Search Logic
        const commandInput = document.getElementById('commandSearchInput');
        const commandResults = document.getElementById('commandSearchResults');
        let searchIndex = [];

        // Helper to get modal instance safely
        function getCommandModal() {
            const modalEl = document.getElementById('commandSearchModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                return bootstrap.Modal.getOrCreateInstance(modalEl);
            }
            return null;
        }

        // Build Search Index from Sidebar
        function buildSearchIndex() {
            const links = document.querySelectorAll('.sidebar-nav .nav-link');
            if (!links.length) return;

            searchIndex = Array.from(links).map(link => {
                const span = link.querySelector('span');
                const title = span ? span.innerText : link.innerText.trim();
                const i18n = span ? span.getAttribute('data-i18n') : '';
                const href = link.getAttribute('href');
                const path = href ? href.replace(window.location.origin, '') : '#';

                const iconTag = link.querySelector('i[data-lucide], svg[data-lucide]');
                const icon = iconTag ? iconTag.getAttribute('data-lucide') : 'circle';

                // Find Category (Header)
                let category = 'Menu';
                const navItem = link.closest('.nav-item');
                let sibling = navItem ? navItem.previousElementSibling : null;
                while (sibling) {
                    if (sibling.classList.contains('nav-header')) {
                        category = sibling.innerText;
                        break;
                    }
                    sibling = sibling.previousElementSibling;
                }

                return {
                    title,
                    i18n,
                    path,
                    icon,
                    category,
                    url: href || '#'
                };
            });
        }

        // Shortcuts Logic
        document.addEventListener('keydown', (e) => {
            if (!(e.ctrlKey || e.metaKey)) return;
            const key = e.key.toLowerCase();

            if (key === 'k' || key === 'o') {
                e.preventDefault();
                window.searchType = (key === 'o') ? 'categories' : 'all';
                const modal = getCommandModal();
                if (modal) modal.show();
            }
        });

        let searchTimeout = null;

        function performSearch() {
            const query = commandInput.value.trim();
            const type = window.searchType || 'all';

            // Client-side search (Sidebar Links)
            let localResults = [];
            if (type === 'all') {
                if (query.length > 0) {
                    const searchWords = query.toLowerCase().split(/\s+/).filter(w => w.length > 0);
                    localResults = searchIndex.filter(item => {
                        const t = (item.title || '').toLowerCase();
                        return searchWords.every(word => t.includes(word));
                    });
                } else {
                    localResults = searchIndex;
                }
            }

            renderResults(localResults);

            // Backend search
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchUrl = (type === 'all' && !window.location.href.includes('/orders/create')) ?
                    "{{ route('global-search') }}" :
                    "{{ route('pos.search') }}";

                $.ajax({
                    url: searchUrl,
                    method: "GET",
                    data: {
                        q: query,
                        type: type
                    },
                    success: function(backendResults) {
                        const results = Array.isArray(backendResults) ? backendResults : [];
                        const localUrls = new Set(localResults.map(i => i.url));
                        const uniqueBackend = results.filter(i => !localUrls.has(i.url));
                        const combined = [...localResults, ...uniqueBackend];
                        renderResults(combined);
                    },
                    error: function() {
                        renderResults(localResults);
                    }
                });
            }, 300);
        }

        if (commandInput) {
            commandInput.addEventListener('input', performSearch);
        }

        function renderResults(filtered) {
            if (!commandResults) return;

            const type = window.searchType || 'all';

            // Filter results to match the current search type
            let displayResults = filtered.filter(item => {
                if (type === 'categories') return item.type === 'category';
                if (type === 'items') return item.type === 'item';
                return true; // Show all for 'all'
            });

            if (displayResults.length === 0) {
                commandResults.innerHTML = `
                    <div class="p-5 text-center text-muted">
                        <i data-lucide="search-x" class="mb-2" style="width: 40px; height: 40px;"></i>
                        <div class="fw-bold">{{ __('No results found') }}</div>
                    </div>
                `;
                lucide.createIcons();
                return;
            }

            let html = '';
            let currentCat = '';

            displayResults.forEach((item, index) => {
                const isLogout = item.url.includes('/logout');
                let clickHandler = isLogout ? 'onclick="event.preventDefault(); document.getElementById(\'logout-form\').submit();"' : '';

                // Handle POS specific types
                if (item.type === 'item') {
                    clickHandler = `onclick="if(window.addToCart) { addToCart(${JSON.stringify(item.item_data).replace(/"/g, '&quot;')}); bootstrap.Modal.getInstance(document.getElementById('commandSearchModal')).hide(); }"`;
                } else if (item.type === 'category') {
                    clickHandler = `onclick="if(window.filterByCategory) { filterByCategory(${item.id}); bootstrap.Modal.getInstance(document.getElementById('commandSearchModal')).hide(); }"`;
                }

                if (item.category !== currentCat) {
                    currentCat = item.category;
                    html += `<div class="px-4 py-2 mt-2 extra-small fw-black text-muted text-uppercase tracking-wider opacity-50">${currentCat}</div>`;
                }
                html += `
                    <a href="${item.url}" 
                       class="command-result-item d-flex align-items-center gap-3 px-4 py-3 text-decoration-none ${index === 0 ? 'selected' : ''}"
                       ${clickHandler}>
                        <div class="bg-light p-2 rounded-lg text-primary">
                            <i data-lucide="${item.icon}" style="width: 18px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark mb-0">${item.title}</div>
                            <div class="extra-small text-muted text-truncate" style="max-width: 400px;">${item.path}</div>
                        </div>
                        <i data-lucide="chevron-right" class="text-muted opacity-0 arrow" style="width: 16px;"></i>
                    </a>
                `;
            });

            commandResults.innerHTML = html;
            lucide.createIcons();
            attachResultNavigation();
        }

        function attachResultNavigation() {
            const items = document.querySelectorAll('.command-result-item');
            let selectedIndex = 0;

            if (commandInput) {
                commandInput.onkeydown = (e) => {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        items[selectedIndex]?.classList.remove('selected');
                        selectedIndex = (selectedIndex + 1) % items.length;
                        items[selectedIndex]?.classList.add('selected');
                        items[selectedIndex]?.scrollIntoView({
                            block: 'nearest'
                        });
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        items[selectedIndex]?.classList.remove('selected');
                        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                        items[selectedIndex]?.classList.add('selected');
                        items[selectedIndex]?.scrollIntoView({
                            block: 'nearest'
                        });
                    } else if (e.key === 'Enter') {
                        if (items[selectedIndex]) items[selectedIndex].click();
                    }
                };
            }
        }

        // Initialize Index when modal shown
        const searchModalEl = document.getElementById('commandSearchModal');
        if (searchModalEl) {
            searchModalEl.addEventListener('shown.bs.modal', function() {
                buildSearchIndex();
                if (commandInput) {
                    commandInput.focus();

                    const query = commandInput.value.trim();
                    const type = window.searchType || 'all';

                    if (query.length > 0 || type === 'categories') {
                        performSearch();
                    } else {
                        renderResults(searchIndex);
                    }
                }
            });

            // Clear on hide for fresh start
            searchModalEl.addEventListener('hidden.bs.modal', function() {
                if (commandInput) commandInput.value = '';
                renderResults([]);
            });
        }
    </script>
</body>

</html>