@props(['paginator'])

@if($paginator && $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div {{ $attributes->merge(['class' => 'pagination-container py-3 d-flex justify-content-between align-items-center']) }}>
    <div class="pagination-info text-muted small">
        {{ __('Showing') }}
        <span class="fw-semibold text-dark">{{ $paginator->firstItem() ?? 0 }}</span>
        {{ __('to') }}
        <span class="fw-semibold text-dark">{{ $paginator->lastItem() ?? 0 }}</span>
        {{ __('of') }}
        <span class="fw-semibold text-dark">{{ $paginator->total() }}</span>
        {{ __('results') }}
    </div>
    <div class="pagination-links">
        {{ $paginator->appends(request()->query())->links() }}
    </div>
</div>

<style>
    .pagination-container .pagination {
        margin: 0 !important;
        gap: 3px;
        display: flex;
    }
    .pagination-container .page-item .page-link {
        border-radius: 4px !important;
        border: 1px solid #dee2e6;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-weight: 600;
        font-size: 0.85rem;
        background: #fff;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
        padding: 0;
        line-height: 1;
    }
    .pagination-container .page-item.active .page-link {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
    }
    .pagination-container .page-item .page-link:hover:not([aria-disabled="true"]) {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #0d6efd;
    }
    .pagination-container .page-item.disabled .page-link {
        background: #f8f9fa;
        color: #adb5bd;
        border-color: #dee2e6;
    }
    /* Hide the redundant "Showing X to Y of Z results" from Laravel's default paginator */
    .pagination-links div[class*="flex-1"] > div:first-child,
    .pagination-links nav > div:first-child,
    .pagination-links .small.text-muted {
        display: none !important;
    }
</style>
@endif
