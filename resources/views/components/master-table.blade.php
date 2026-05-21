@props([
    'title',
    'subtitle' => null,
    'createRoute' => null,
    'createLabel' => 'Add New',
    'createPermission' => null,
    'searchPlaceholder' => 'Search items...',
    'headers' => [],
    'items' => null
])

<style>
    /* ── Container ─────────────────────────────── */
    .master-table-container {
        font-family: inherit;
    }

    /* ── Page Header ────────────────────────────── */
    .mst-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .mst-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #212529;
        margin: 0 0 2px;
        line-height: 1.3;
    }

    .mst-subtitle {
        font-size: 0.8rem;
        color: #6c757d;
        margin: 0;
    }

    /* ── Toolbar ─────────────────────────────────── */
    .mst-toolbar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Search */
    .mst-search-form {
        position: relative;
        min-width: 280px;
    }
    .mst-search-form .search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
        pointer-events: none;
        width: 16px;
        height: 16px;
    }
    .mst-search-input {
        width: 100%;
        height: 36px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0 12px 0 34px;
        font-size: 0.875rem;
        color: #495057;
        background: #fff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .mst-search-input::placeholder { color: #adb5bd; }
    .mst-search-input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    /* Create button */
    .mst-create-btn {
        height: 36px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        background: #0d6efd;
        color: #fff;
        border: none;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.15s, box-shadow 0.15s;
    }
    .mst-create-btn:hover {
        background: #0b5ed7;
        color: #fff;
        box-shadow: 0 2px 8px rgba(13,110,253,0.3);
    }
    .mst-create-btn svg { width: 14px; height: 14px; }

    /* ── Card Shell ──────────────────────────────── */
    .mst-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }

    /* ── Table Head ──────────────────────────────── */
    .mst-thead {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    .mst-thead th {
        font-size: 0.7rem !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6c757d !important;
        padding: 12px 16px !important;
        white-space: nowrap;
        vertical-align: middle !important;
        border: none !important;
    }

    /* ── Table Body ──────────────────────────────── */
    .master-table-container tbody tr {
        border-bottom: 1px solid #f1f3f5;
        transition: background 0.12s;
    }
    .master-table-container tbody tr:last-child { border-bottom: none; }
    .master-table-container tbody tr:hover { background: #f8f9fa; }
    .master-table-container tbody td { vertical-align: middle; }

    /* ── Pagination Footer ───────────────────────── */
    .mst-footer {
        padding: 14px 20px;
        border-top: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    .mst-pagination-info {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .mst-pagination-info strong { color: #212529; }

    /* Override Bootstrap pagination to match POS flat style */
    .mst-footer .pagination {
        margin: 0 !important;
        gap: 3px;
    }
    .mst-footer .page-link {
        border-radius: 5px !important;
        border: 1px solid #dee2e6 !important;
        width: 32px;
        height: 32px;
        display: flex !important;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: #495057;
        padding: 0 !important;
        background: #fff;
        transition: all 0.15s;
    }
    .mst-footer .page-link:hover {
        background: #e9ecef;
        border-color: #ced4da !important;
        color: #212529;
    }
    .mst-footer .page-item.active .page-link {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
        box-shadow: 0 2px 6px rgba(13,110,253,0.3);
    }
    .mst-footer .page-item.disabled .page-link {
        color: #adb5bd; background: #f8f9fa;
    }

    /* Kill the laravel default nav "showing x to y" duplicate */
    .mst-footer .pagination-links nav > div:first-child,
    .mst-footer .pagination-links .text-sm,
    .mst-footer .pagination-links p.small,
    .mst-footer .pagination-links .d-sm-none { display: none !important; }
    
    .mst-footer .pagination-links nav > div.d-none.d-sm-flex > div:first-child { display: none !important; }

    /* ── Responsive ──────────────────────────────── */
    @media (max-width: 768px) {
        .mst-toolbar { width: 100%; }
        .mst-search-form { min-width: 0; width: 100%; flex: 1; }
        .mst-create-btn { width: 100%; justify-content: center; }
        .mst-header { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="master-table-container">

    {{-- ── Header ─────────────────────────────── --}}
    <div class="mst-header">
        <div>
            <h2 class="mst-title">{{ $title }}</h2>
            @if($subtitle)
            <p class="mst-subtitle">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="mst-toolbar">
            {{-- Search --}}
            <form action="{{ url()->current() }}" method="GET" class="mst-search-form m-0">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text"
                       name="search"
                       class="mst-search-input"
                       placeholder="{{ $searchPlaceholder }}"
                       value="{{ request('search') }}">
                @foreach(request()->except(['search', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
            </form>

            {{-- Filters Slot --}}
            {{ $filters ?? '' }}

            {{-- Create Button --}}
            @if($createRoute)
            @if(!$createPermission || auth()->user()->can($createPermission))
            <a href="{{ $createRoute }}" class="mst-create-btn">
                <i data-lucide="plus"></i>
                <span>{{ $createLabel }}</span>
            </a>
            @endif
            @endif
        </div>
    </div>

    {{-- ── Table Card ──────────────────────────── --}}
    <div class="mst-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="mst-thead">
                    <tr>
                        @foreach($headers as $header)
                        @php
                            $alignClass = '';
                            $headerText = '';
                            if (is_array($header)) {
                                $headerText = $header['text'] ?? '';
                                $align = $header['align'] ?? '';
                                if ($align === 'center') {
                                    $alignClass = 'text-center';
                                } elseif ($align === 'end' || $align === 'right') {
                                    $alignClass = 'text-end pe-4';
                                } elseif ($align === 'start' || $align === 'left') {
                                    $alignClass = 'text-start';
                                } else {
                                    $alignClass = $align;
                                }
                            } else {
                                $headerText = $header;
                                $cleanHeader = strtolower(trim(strip_tags($header)));
                                
                                // Support matching for standard English and translation values
                                $centerOptions = ['#', 'category', 'price', 'status', 'items', 'symbol', 'role', 'type', 'amount', 'date', 'created by', 'created_by'];
                                $isCenter = false;
                                foreach ($centerOptions as $opt) {
                                    if ($cleanHeader === $opt || $cleanHeader === strtolower(trim(__($opt)))) {
                                        $isCenter = true;
                                        break;
                                    }
                                }

                                if ($isCenter) {
                                    $alignClass = 'text-center';
                                }
                                if ($cleanHeader === 'actions' || $cleanHeader === strtolower(trim(__('Actions')))) {
                                    $alignClass = 'text-end pe-4';
                                }
                                if ($cleanHeader === 'image' || $cleanHeader === strtolower(trim(__('Image')))) {
                                    $alignClass = 'ps-4';
                                }
                            }
                        @endphp
                        <th class="{{ $alignClass }}">{!! is_array($header) ? $headerText : e($headerText) !!}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        {{-- ── Pagination Footer ──────────────── --}}
        @if($items && $items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
        <div class="mst-footer">
            <div class="mst-pagination-info">
                Showing
                <strong>{{ $items->firstItem() ?? 0 }}</strong>
                -
                <strong>{{ $items->lastItem() ?? 0 }}</strong>
                of
                <strong>{{ $items->total() }}</strong>
                results
            </div>
            <div class="pagination-links">
                {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>

</div>