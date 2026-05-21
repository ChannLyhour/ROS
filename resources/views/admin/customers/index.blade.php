@extends('layouts.app')

@section('title', __('Customers'))

@section('content')
<x-master-table
    title="{{ __('Customer Management') }}"
    subtitle="{{ __('Manage your clientele records, contact details and histories') }}"
    :createRoute="route('customers.create')"
    createPermission="create-customers"
    createLabel="{{ __('Add Customer') }}"
    searchPlaceholder="{{ __('Search name, email or phone...') }}"
    :headers="[
        ['text' => __('#'), 'align' => 'center'],
        ['text' => __('Customer'), 'align' => 'ps-3'],
        ['text' => __('Email'), 'align' => 'start'],
        ['text' => __('Phone'), 'align' => 'center'],
        ['text' => __('Location'), 'align' => 'start'],
        ['text' => __('Actions'), 'align' => 'end']
    ]"
    :items="$customers">

    @forelse($customers as $customer)
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</span>
        </td>
        <td class="ps-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $customer->display_image }}" alt="{{ $customer->name }}"
                     class="rounded-circle border"
                     style="width:38px; height:38px; object-fit:cover; border-color:#dee2e6 !important; flex-shrink:0;">
                <div>
                    <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $customer->name }}</div>
                    <small class="text-muted" style="font-size:0.75rem;">{{ __('Client') }} #{{ $customer->id }}</small>
                </div>
            </div>
        </td>
        <td>
            <span class="small text-dark">{{ $customer->email ?? '—' }}</span>
        </td>
        <td class="text-center">
            <span class="small text-dark font-monospace">{{ $customer->phone ?? '—' }}</span>
        </td>
        <td>
            <div class="small text-dark fw-semibold">{{ $customer->city ?? '—' }}</div>
            @if($customer->address)
            <div class="text-muted text-truncate" style="font-size:0.75rem; max-width:180px;">{{ $customer->address }}</div>
            @endif
        </td>
        <td class="text-end pe-4" style="width:120px;">
            <div class="d-flex justify-content-end gap-1">
                @can('view-customers')
                <a href="{{ route('customers.show', $customer->id) }}" class="action-btn view-btn" title="{{ __('View') }}">
                    <i data-lucide="eye" style="width:14px;height:14px;"></i>
                </a>
                @endcan
                @can('edit-customers')
                <a href="{{ route('customers.edit', $customer->id) }}" class="action-btn edit-btn" title="{{ __('Edit') }}">
                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                </a>
                @endcan
                @can('delete-customers')
                <button type="button" class="action-btn delete-btn" title="{{ __('Delete') }}"
                        onclick="confirmDelete('delete-form-{{ $customer->id }}', '{{ $customer->name }}')">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                </button>
                <form id="delete-form-{{ $customer->id }}" action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
                @endcan
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="py-5">
            <div class="text-center">
                <i data-lucide="users" style="width:48px;height:48px;color:#ced4da;"></i>
                <p class="text-muted mt-3 fw-semibold mb-1">{{ __('No customers found') }}</p>
                <small class="text-muted">{{ __('Add your first customer to get started.') }}</small>
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
</style>
@endsection
