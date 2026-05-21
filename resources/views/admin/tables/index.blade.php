@extends('layouts.app')

@section('title', __('Table Management'))

@section('content')
<x-master-table
    title="{{ __('Table Management') }}"
    subtitle="{{ __('Organize your dining area and track occupancy') }}"
    :createRoute="route('tables.create')"
    createPermission="create-tables"
    createLabel="{{ __('Add Table') }}"
    searchPlaceholder="{{ __('Search by table name...') }}"
    :headers="[
        ['text' => '#', 'align' => 'center'],
        ['text' => __('Table'), 'align' => 'ps-3'],
        ['text' => __('Capacity'), 'align' => 'center'],
        ['text' => __('Status'), 'align' => 'center'],
        ['text' => __('Actions'), 'align' => 'end']
    ]"
    :items="$tables">

    <x-slot name="filters">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 m-0 align-items-center">
            <select name="status" class="form-select form-select-sm select2" onchange="this.form.submit()" style="min-width:130px; border-radius:4px;">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="{{ __('Available') }}" {{ request('status') == __('Available') ? 'selected' : '' }}>{{ __('Available') }}</option>
                <option value="{{ __('Taken') }}"  {{ request('status') == __('Taken')  ? 'selected' : '' }}>{{ __('Taken') }}</option>
                <option value="{{ __('Reserved') }}"  {{ request('status') == __('Reserved')  ? 'selected' : '' }}>{{ __('Reserved') }}</option>
            </select>
            @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('tables.index') }}" class="action-btn reset-btn" title="{{ __('Clear Filters') }}">
                <i data-lucide="rotate-ccw" style="width:14px;height:14px;"></i>
            </a>
            @endif
        </form>
    </x-slot>

    @forelse($tables as $table)
    @php
    $statusMap = [
        __('Available') => ['class' => 'available', 'icon' => 'check-circle'],
        __('Taken')     => ['class' => 'taken',     'icon' => 'user-minus'],
        __('Reserved')  => ['class' => 'reserved',  'icon' => 'clock'],
    ];
    $s = $statusMap[$table->status] ?? $statusMap[__('Available')];
    @endphp
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ $loop->iteration }}</span>
        </td>
        <td class="ps-3">
            <div class="d-flex align-items-center gap-3">
                <div class="table-icon-box">
                    <i data-lucide="layout-dashboard" style="width:15px;height:15px;"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark">{{ $table->name }}</div>
                    <small class="text-muted" style="font-size:0.75rem;">{{ __('Dining Table') }}</small>
                </div>
            </div>
        </td>
        <td class="text-center">
            <span class="cap-badge">
                <i data-lucide="users" style="width:12px;height:12px;"></i>
                {{ $table->capacity }}
            </span>
        </td>
        <td class="text-center">
            <span class="status-badge {{ $s['class'] }}">
                <span class="status-dot"></span>
                {{ __(ucfirst($table->status)) }}
            </span>
        </td>
        <td class="text-end pe-4" style="width:120px;">
            <x-table-actions
                :editRoute="route('tables.edit', $table->id)"
                editPermission="edit-tables"
                :viewRoute="route('tables.show', $table->id)"
                viewPermission="view-tables"
                :deleteRoute="route('tables.destroy', $table->id)"
                deletePermission="delete-tables"
                :id="$table->id"
                :name="$table->name" />
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="py-5">
            <div class="text-center">
                <i data-lucide="layout-dashboard" style="width:48px;height:48px;color:#ced4da;"></i>
                <p class="text-muted mt-3 fw-semibold mb-1">{{ __('No tables found') }}</p>
                <small class="text-muted">{{ __('Add your first table to get started.') }}</small>
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
    .table-icon-box {
        width: 36px; height: 36px; border-radius: 8px;
        background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .cap-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        font-size: 0.8rem; font-weight: 600; color: #475569;
    }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;
    }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.available { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.available .status-dot { background: #22c55e; }
    .status-badge.taken     { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge.taken .status-dot     { background: #ef4444; }
    .status-badge.reserved  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-badge.reserved .status-dot  { background: #f59e0b; }
    .action-btn {
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid #e9ecef; background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; text-decoration: none; color: #6c757d;
    }
    .action-btn.reset-btn:hover { background: #6c757d; color: #fff; border-color: #6c757d; }
    .form-select-sm { font-size: 0.875rem; border-color: #ced4da; }
</style>
@endsection