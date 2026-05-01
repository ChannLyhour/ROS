@props([
'title',
'subtitle' => null,
'createRoute' => null,
'createLabel' => 'Add New',
'searchPlaceholder' => 'Search items...',
'headers' => [],
'items' => null
])

<style>
    .fw-black {
        font-weight: 900;
    }

    .tracking-wider {
        letter-spacing: 0.1em;
    }

    .bg-light-subtle {
        background-color: #fff9f2 !important;
        border-bottom: 2px solid rgba(255, 140, 0, 0.1) !important;
    }

    .table thead th {
        color: #7c2d12 !important;
        font-size: 0.7rem !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-container {
        min-width: 320px;
    }

    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            gap: 15px;
        }

        .search-container {
            min-width: 0;
            width: 100%;
        }

        .btn-premium-lg {
            width: 100%;
            justify-content: center;
        }

        .master-table-container .justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }
    }

    .custom-input-group {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        padding: 0 15px;
        height: 48px;
        transition: all 0.3s ease;
    }

    .custom-input-group:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1);
    }

    .custom-input-group input {
        border: none;
        padding: 10px;
        font-size: 0.95rem;
        outline: none;
        width: 100%;
    }

    .header-select {
        height: 48px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0 20px;
        font-size: 0.95rem;
        cursor: pointer;
        min-width: 160px;
        background-color: #fff;
    }

    .btn-premium-lg {
        height: 48px;
        display: flex;
        align-items: center;
        padding: 0 25px;
        border-radius: 5px;
        font-weight: 700;
        background: linear-gradient(135deg, #ff8c00 0%, #ff5e00 100%);
        color: #fff;
        border: none;
        box-shadow: 0 4px 15px rgba(255, 94, 0, 0.3);
        transition: all 0.3s ease;
    }

    .btn-premium-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 94, 0, 0.4);
        color: #fff;
    }
</style>

<div class="master-table-container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-black text-dark mb-1 h3">{{ $title }}</h2>
            @if($subtitle)
            <p class="text-muted small mb-0">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="header-actions">
            <!-- Search -->
            <form action="{{ url()->current() }}" method="GET" class="search-container m-0">
                <div class="custom-input-group">
                    <i data-lucide="search" class="text-muted" style="width: 20px; height: 20px;"></i>
                    <input type="text" name="search" placeholder="{{ $searchPlaceholder }}" value="{{ request('search') }}">
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </div>
            </form>

            <!-- Filters Slot -->
            {{ $filters ?? '' }}

            <!-- Create Button -->
            @if($createRoute)
            <a href="{{ $createRoute }}" class="btn-premium-lg">
                <i data-lucide="plus" class="me-2" style="width: 20px; height: 20px;"></i>
                <span>{{ $createLabel }}</span>
            </a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            @foreach($headers as $header)
                            @php
                            $alignClass = '';
                            if($header === '#' || $header === 'Category' || $header === 'Price' || $header === 'Status') $alignClass = 'text-center';
                            if($header === 'Actions') $alignClass = 'text-end pe-4';
                            if($header === 'Image') $alignClass = 'ps-4';
                            @endphp
                            <th class="py-4 px-4 text-uppercase fw-black tracking-wider text-muted border-0 {{ $alignClass }}">{{ $header }}

                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{ $slot }}
                    </tbody>
                </table>
            </div>

            @if($items && $items instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-5 border-top">
                <x-pagination :paginator="$items" />
            </div>
            @endif
        </div>
    </div>
</div>