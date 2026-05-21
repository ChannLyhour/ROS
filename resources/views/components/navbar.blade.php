<header class="admin-navbar shadow-sm">
    @php
        $isPos = request()->routeIs('pos.index') || request()->routeIs('orders.create') || request()->routeIs('orders.edit') || request()->routeIs('pos.checkout');
    @endphp

    <div class="navbar-left d-flex align-items-center gap-2">
        @if(!$isPos)
        <button id="sidebarToggle" class="navbar-toggle" title="{{ __('Toggle Sidebar') }}">
            <i data-lucide="menu"></i>
        </button>
        @endif
        
        @if($isPos)
        <a href="{{ route('home') }}" class="btn btn-light border rounded-pill px-4 fw-bold d-flex align-items-center gap-2 shadow-sm animate__animated animate__fadeInLeft" style="height: 40px; font-size: 0.85rem;">
            <i data-lucide="layout-dashboard" style="width: 18px;" class="text-primary"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
        @endif

        <!-- Global Search Trigger -->
        @if(!$isPos)
        <button class="nav-search-btn d-none d-lg-flex align-items-center justify-content-between" 
                data-bs-toggle="modal" 
                data-bs-target="#commandSearchModal"
                onclick="window.searchType = 'all';">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                <span class="fw-semibold">{{ __('Search...') }}</span>
            </div>
            <kbd class="kbd-shortcut ms-auto">
                <span class="opacity-75">Ctrl</span> K
            </kbd>
        </button>
        @endif
    </div>

    <div class="navbar-right d-flex align-items-center gap-3">
        @if(!$isPos)
        <a href="{{ route('pos.index') }}" class="pos-create-btn animate__animated animate__pulse animate__infinite">
            <i data-lucide="plus-circle"></i>
            <span>POS</span>
        </a>
        @endif
        <!-- Language Trigger -->
        <button class="btn btn-sm btn-light border-0 rounded-pill px-3 fw-bold d-flex align-items-center gap-2"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#languageModal"
            style="font-size: 0.8rem; height: 40px; background: #f8f9fa;">
            <i data-lucide="languages" style="width: 16px;" class="text-primary"></i>
            <span class="d-none d-md-inline">{{ app()->getLocale() == 'kh' ? 'ភាសាខ្មែរ' : 'English' }}</span>
        </button>
        <div class="dropdown">
            <div class="user-profile dropdown-toggle" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="d-flex flex-column align-items-end me-2 d-none d-sm-flex">
                    <span class="fw-bold lh-1 mb-1">{{ auth()->user()->name }}</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small py-1 px-2 rounded-pill" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ strtoupper(auth()->user()->role->name ?? 'User') }}</span>
                </div>
                <div class="user-avatar overflow-hidden">
                    @if(auth()->user()->image)
                    <img src="{{ auth()->user()->display_image }}" class="w-100 h-100 object-fit-cover">
                    @else
                    {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-5 animate__animated animate__fadeInDown" aria-labelledby="userDropdown" style="border-radius: 15px; min-width: 230px;">
                <li class="px-3 py-3 border-bottom mb-2 bg-light-subtle rounded-top-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar-lg overflow-hidden shadow-sm">
                            @if(auth()->user()->image)
                            <img src="{{ auth()->user()->display_image }}" class="w-100 h-100 object-fit-cover">
                            @else
                            {{ substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-truncate" style="max-width: 140px;">{{ auth()->user()->name }}</div>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <span class="badge bg-primary text-white extra-small px-2 py-1 rounded-pill" style="font-size: 0.55rem; font-weight: 800;">{{ strtoupper(auth()->user()->role->name ?? 'User') }}</span>
                                <div class="text-muted extra-small" style="font-size: 0.7rem;">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.index') }}">
                        <i data-lucide="user" style="width: 16px;"></i> {{ __('My Profile') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('settings.index') }}">
                        <i data-lucide="settings" style="width: 16px;"></i> {{ __('System Settings') }}
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider opacity-50">
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-lucide="log-out" style="width: 16px;"></i> {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>