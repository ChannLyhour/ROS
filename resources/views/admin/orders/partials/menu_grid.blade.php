<div class="flex-grow-1 overflow-auto p-4" id="menuScrollContainer">
    <div class="row g-4" id="menuGrid">
        @forelse($menuItems as $item)
        <div class="col-xl-3 col-lg-4 col-md-6 menu-item-card" data-id="{{ $item->id }}" data-category="{{ $item->category_id }}" data-name="{{ strtolower($item->name) }}">
            <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden item-interactive" data-item="{{ json_encode($item) }}" onclick="addToCart(this)">
                <div class="position-relative">
                    <img src="{{ $item->display_image }}" class="card-img-top" style="height: 160px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    <div class="price-pill">{{ $appSettings['currency'] ?? '$' }}{{ number_format($item->price, 2) }}</div>
                </div>
                <div class="card-body p-3 d-flex justify-content-between align-items-center gap-2">
                    <div class="overflow-hidden">
                        <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $item->name }}</h6>
                        <p class="extra-small text-muted mb-0 text-truncate">{{ $item->category->name }}</p>
                    </div>
                    <div class="flex-shrink-0 bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm add-icon-wrapper" style="width: 32px; height: 32px; transition: transform 0.2s;">
                        <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
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

@if($menuItems->hasPages())
<div class="p-3 bg-white border-top shadow-sm pos-pagination mt-auto z-3 position-relative">
    {{ $menuItems->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endif
