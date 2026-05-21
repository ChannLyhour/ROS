@extends('layouts.app')

@section('title', __('Roles & Permissions'))

@section('content')
<x-master-table
    title="{{ __('Roles & Permissions') }}"
    subtitle="{{ __('Define access hierarchies and system permission profiles') }}"
    :createRoute="route('roles.create')"
    createLabel="{{ __('Create Role') }}"
    :headers="['#', __('Role'), __('Permissions'), __('Staff'), __('Actions')]"
    :items="$roles">

    @foreach($roles as $role)
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ $loop->iteration }}</span>
        </td>
        <td class="ps-3">
            <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $role->name }}</div>
            <code class="text-muted" style="font-size:0.75rem;">{{ $role->slug }}</code>
        </td>
        <td style="max-width:320px;">
            <div class="d-flex flex-wrap gap-1">
                @forelse($role->permissions->take(5) as $permission)
                <span class="perm-badge">{{ strtoupper($permission->name) }}</span>
                @empty
                <span class="text-muted small fst-italic">{{ __('No permissions') }}</span>
                @endforelse
                @if($role->permissions->count() > 5)
                <span class="perm-badge more">+{{ $role->permissions->count() - 5 }}</span>
                @endif
            </div>
        </td>
        <td class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <i data-lucide="users" class="text-muted" style="width:14px;"></i>
                <span class="fw-semibold text-dark small">{{ $role->users_count }}</span>
            </div>
        </td>
        <td class="text-end pe-4" style="width:100px;">
            <x-table-actions
                :editRoute="route('roles.edit', $role->id)"
                :deleteRoute="route('roles.destroy', $role->id)"
                :id="$role->id"
                :name="$role->name" />
        </td>
    </tr>
    @endforeach
</x-master-table>

<style>
    body { background-color: #f8fafc !important; }
    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; background: #f1f3f5; border-radius: 50%;
        font-size: 0.75rem; font-weight: 600; color: #6c757d;
    }
    .perm-badge {
        display: inline-block; padding: 2px 8px; border-radius: 4px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        font-size: 0.65rem; font-weight: 700; color: #475569;
        text-transform: uppercase; letter-spacing: 0.04em;
    }
    .perm-badge.more {
        background: #dbeafe; border-color: #bfdbfe; color: #1d4ed8;
    }
</style>
@endsection