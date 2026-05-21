@extends('layouts.app')

@section('title', __('Categories'))

@section('content')
<x-master-table 
    title="{{ __('Category Management') }}" 
    subtitle="{{ __('Organize your menu items into structured groups') }}" 
    :createRoute="route('categories.create')" 
    createLabel="{{ __('Add Category') }}" 
    searchPlaceholder="{{ __('Search category name...') }}" 
    :headers="[__('#'), __('Category Details'), __('Item Count'), __('Status'), __('Actions')]" 
    :items="$categories"
>
    @forelse($categories as $category)
    <tr>
        <td class="text-center">
            <span class="text-muted fw-bold">{{ $loop->iteration }}</span>
        </td>
        <td class="ps-4">
            <div class="d-flex align-items-center gap-3">
                <div class="cat-icon-box bg-light text-primary">
                    <i data-lucide="grid" style="width: 18px;"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">{{ $category->name }}</div>
                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $category->description ?? __('No description provided') }}</small>
                </div>
            </div>
        </td>
        <td class="text-center">
            @php $count = \App\Models\MenuItem::where('category_id', $category->id)->count(); @endphp
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-light rounded-pill border">
                <i data-lucide="package" class="text-muted" style="width: 14px;"></i>
                <span class="fw-bold text-dark small">{{ $count }} {{ __('Items') }}</span>
            </div>
        </td>
        <td class="text-center">
            @if($category->status)
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                    <i data-lucide="check-circle" style="width: 14px;"></i>
                    {{ __('ACTIVE') }}
                </span>
            @else
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                    <i data-lucide="slash" style="width: 14px;"></i>
                    {{ __('DISABLED') }}
                </span>
            @endif
        </td>
        <td class="text-end pe-4">
            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-action edit" title="{{ __('Edit Category') }}">
                    <i data-lucide="edit-3"></i>
                </a>
                <button type="button" class="btn btn-action delete" title="{{ __('Delete Category') }}" onclick="confirmDelete('delete-form-{{ $category->id }}', '{{ $category->name }}')">
                    <i data-lucide="trash-2"></i>
                </button>
                <form id="delete-form-{{ $category->id }}" action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="text-center py-5">
            <i data-lucide="folder-x" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
            <p class="text-muted">{{ __('No categories found.') }}</p>
        </td>
    </tr>
    @endforelse
</x-master-table>

<style>
    .cat-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection
