@props([
    'editRoute'   => null,
    'deleteRoute' => null,
    'viewRoute'   => null,
    'printRoute'  => null,
    'id'          => null,
    'name'        => 'Item'
])

<div class="d-flex justify-content-end gap-1 table-actions-wrapper">
    @if($viewRoute)
    <a href="{{ $viewRoute }}" class="ta-btn view-btn" title="{{ __('View Details') }}">
        <i data-lucide="eye" style="width:14px;height:14px;"></i>
    </a>
    @endif

    @if($printRoute)
    <a href="{{ $printRoute }}" class="ta-btn print-btn" title="{{ __('Print Receipt') }}">
        <i data-lucide="printer" style="width:14px;height:14px;"></i>
    </a>
    @endif

    @if($editRoute)
    <a href="{{ $editRoute }}" class="ta-btn edit-btn" title="{{ __('Edit') }}">
        <i data-lucide="pencil" style="width:14px;height:14px;"></i>
    </a>
    @endif

    @if($deleteRoute)
    <button type="button" class="ta-btn delete-btn" title="{{ __('Delete') }}"
        onclick="confirmDelete('delete-form-{{ $id }}', '{{ addslashes($name) }}')">
        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
    </button>
    <form id="delete-form-{{ $id }}" action="{{ $deleteRoute }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    @endif
</div>

<style>
    .ta-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .ta-btn.view-btn   { color: #6366f1; }
    .ta-btn.view-btn:hover   { background: #6366f1; color: #fff; border-color: #6366f1; }
    .ta-btn.edit-btn   { color: #3b82f6; }
    .ta-btn.edit-btn:hover   { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .ta-btn.delete-btn { color: #ef4444; }
    .ta-btn.delete-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .ta-btn.print-btn  { color: #6c757d; }
    .ta-btn.print-btn:hover  { background: #6c757d; color: #fff; border-color: #6c757d; }
</style>