@extends('layouts.app')

@section('title', __('Menu Items'))

@section('content')
<x-master-table
    title="{{ __('Menu Management') }}"
    subtitle="{{ __('Coordinate your culinary collection and service availability') }}"
    :createRoute="route('menu.create')"
    createLabel="{{ __('Add Item') }}"
    searchPlaceholder="{{ __('Search by name or description...') }}"
    :headers="[__('#'), __('Image'), __('Name'), __('Category'), __('Price'), __('Status'), __('Actions')]"
    :items="$menuItems">

    <x-slot name="filters">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 m-0 align-items-center">
            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:140px; border-radius:4px;">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            @if(request()->anyFilled(['search', 'category']))
            <a href="{{ route('menu.index') }}" class="action-btn reset-btn" title="{{ __('Clear Filters') }}">
                <i data-lucide="rotate-ccw" style="width:14px;height:14px;"></i>
            </a>
            @endif
        </form>
    </x-slot>

    @forelse($menuItems as $item)
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ $loop->iteration }}</span>
        </td>
        <td class="ps-3" style="width:80px;">
            <img src="{{ $item->display_image }}" alt="{{ $item->name }}"
                 class="rounded border" style="width:52px; height:52px; object-fit:cover; border-color:#dee2e6 !important;">
        </td>
        <td>
            <div class="fw-semibold text-dark">{{ $item->name }}</div>
            <small class="text-muted text-truncate d-inline-block" style="max-width:200px; font-size:0.78rem;">{{ $item->description }}</small>
        </td>
        <td class="text-center">
            <span class="cat-badge">{{ $item->category->name }}</span>
        </td>
        <td class="text-center">
            <span class="fw-semibold text-dark small">{{ $appSettings['currency'] }}{{ number_format($item->price, 2) }}</span>
        </td>
        <td class="text-center">
            @if($item->status == 'available')
            <span class="status-badge available"><span class="status-dot"></span> {{ __('Available') }}</span>
            @else
            <span class="status-badge unavailable"><span class="status-dot"></span> {{ __('Unavailable') }}</span>
            @endif
        </td>
        <td class="text-end pe-4" style="width:120px;">
            <div class="d-flex justify-content-end gap-1">
                <a href="{{ route('menu.show', $item->id) }}" class="action-btn view-btn" title="{{ __('View') }}">
                    <i data-lucide="eye" style="width:14px;height:14px;"></i>
                </a>
                <a href="{{ route('menu.edit', $item->id) }}" class="action-btn edit-btn" title="{{ __('Edit') }}">
                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                </a>
                <button type="button" class="action-btn delete-btn" title="{{ __('Delete') }}"
                        onclick="confirmDelete('delete-form-{{ $item->id }}', '{{ $item->name }}')">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                </button>
                <form id="delete-form-{{ $item->id }}" action="{{ route('menu.destroy', $item->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="py-5">
            <div class="text-center">
                <i data-lucide="utensils" style="width:48px;height:48px;color:#ced4da;"></i>
                <p class="text-muted mt-3 fw-semibold mb-1">{{ __('No items match your criteria') }}</p>
                <small class="text-muted">{{ __('Try adjusting your search or category filter.') }}</small>
            </div>
        </td>
    </tr>
    @endforelse
</x-master-table>

<style>
    body { background-color: #f8fafc !important; }

    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px;
        background: #f1f3f5; border-radius: 50%;
        font-size: 0.75rem; font-weight: 600; color: #6c757d;
    }

    .cat-badge {
        display: inline-block; padding: 3px 10px;
        background: #e9ecef; border: 1px solid #dee2e6;
        border-radius: 20px; font-size: 0.78rem; color: #495057; font-weight: 500;
    }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;
    }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.available   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.available .status-dot   { background: #22c55e; }
    .status-badge.unavailable { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge.unavailable .status-dot { background: #ef4444; }

    .action-btn {
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid #e9ecef; background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .action-btn.view-btn   { color: #6366f1; }
    .action-btn.view-btn:hover   { background: #6366f1; color: #fff; border-color: #6366f1; }
    .action-btn.edit-btn   { color: #3b82f6; }
    .action-btn.edit-btn:hover   { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .action-btn.delete-btn { color: #ef4444; }
    .action-btn.delete-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .action-btn.reset-btn  { color: #6c757d; }
    .action-btn.reset-btn:hover  { background: #6c757d; color: #fff; border-color: #6c757d; }

    .form-select-sm { font-size: 0.875rem; border-color: #ced4da; }
    .form-select-sm:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.15); }
</style>
@endsection