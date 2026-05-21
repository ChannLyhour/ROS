@extends('layouts.app')

@section('title', __('Translations'))

@section('content')
<x-master-table
    title="{{ __('Translations') }}"
    subtitle="{{ __('Manage multilingual key-value pairs for your application') }}"
    :createRoute="route('translations.create')"
    createPermission="manage-translations"
    createLabel="{{ __('Add Translation') }}"
    searchPlaceholder="{{ __('Search by key or text...') }}"
    :headers="['#', __('Group'), __('Key'), __('English'), __('Khmer'), __('Actions')]"
    :items="$translations">

    @forelse($translations as $item)
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ ($translations->currentPage() - 1) * $translations->perPage() + $loop->iteration }}</span>
        </td>
        <td class="text-center" style="width:100px;">
            <span class="group-badge">{{ $item->group }}</span>
        </td>
        <td class="ps-3">
            <code class="text-primary fw-semibold" style="font-size:0.82rem;">{{ $item->key }}</code>
        </td>
        <td>
            <span class="small text-dark">{{ Str::limit($item->en, 50) }}</span>
        </td>
        <td>
            <span class="small text-dark text-khmer">{{ Str::limit($item->kh, 50) }}</span>
        </td>
        <td class="text-end pe-4" style="width:100px;">
            <x-table-actions
                :editRoute="route('translations.edit', $item->id)"
                editPermission="manage-translations"
                :deleteRoute="route('translations.destroy', $item->id)"
                deletePermission="manage-translations"
                :id="$item->id"
                :name="$item->key" />
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="py-5">
            <div class="text-center">
                <i data-lucide="languages" style="width:48px;height:48px;color:#ced4da;"></i>
                <p class="text-muted mt-3 fw-semibold mb-1">{{ __('No translations found') }}</p>
            </div>
        </td>
    </tr>
    @endforelse
</x-master-table>

<style>
    body { background-color: #f8fafc !important; }
    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; background: #f1f3f5; border-radius: 50%;
        font-size: 0.75rem; font-weight: 600; color: #6c757d;
    }
    .group-badge {
        display: inline-block; padding: 3px 10px; border-radius: 20px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        font-size: 0.72rem; font-weight: 600; color: #475569; text-transform: uppercase;
    }
</style>
@endsection
