@extends('layouts.app')

@section('title', __('User Management'))

@section('content')
<x-master-table
    title="{{ __('Staff Management') }}"
    subtitle="{{ __('Coordinate your workforce and system access levels') }}"
    :createRoute="auth()->user()->can('create-users') ? route('users.create') : null"
    createLabel="{{ __('Add Staff') }}"
    searchPlaceholder="{{ __('Search by name or email...') }}"
    :headers="['#', __('Staff'), __('Phone'), __('Role'), __('Status'), __('Actions')]"
    :items="$users">

    <x-slot name="filters">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 m-0 align-items-center">
            <select name="role" class="form-select form-select-sm select2" onchange="this.form.submit()"
                    data-placeholder="{{ __('All Roles') }}" style="min-width:130px; border-radius:4px;">
                <option value=""></option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
                @endforeach
            </select>
            @if(request()->anyFilled(['search', 'role']))
            <a href="{{ route('users.index') }}" class="action-btn reset-btn" title="{{ __('Clear') }}">
                <i data-lucide="rotate-ccw" style="width:14px;height:14px;"></i>
            </a>
            @endif
        </form>
    </x-slot>

    @forelse($users as $user)
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</span>
        </td>
        <td class="ps-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $user->display_image }}" alt="{{ $user->name }}"
                     class="rounded-circle border" style="width:38px;height:38px;object-fit:cover;border-color:#dee2e6 !important;flex-shrink:0;">
                <div>
                    <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $user->name }}</div>
                    <small class="text-muted" style="font-size:0.75rem;">{{ $user->email }}</small>
                </div>
            </div>
        </td>
        <td>
            <span class="small text-dark font-monospace">{{ $user->phone ?? '—' }}</span>
        </td>
        <td class="text-center">
            <span class="role-badge {{ $user->role && $user->role->slug == 'admin' ? 'admin' : 'staff' }}">
                {{ $user->role->name ?? '—' }}
            </span>
        </td>
        <td class="text-center">
            @if($user->deleted_at)
            <span class="status-badge inactive"><span class="status-dot"></span>{{ __('Inactive') }}</span>
            @else
            <span class="status-badge active"><span class="status-dot"></span>{{ __('Active') }}</span>
            @endif
        </td>
        <td class="text-end pe-4" style="width:100px;">
            <div class="d-flex justify-content-end gap-1">
                @can('edit-users')
                <a href="{{ route('users.edit', $user->id) }}" class="action-btn edit-btn" title="{{ __('Edit') }}">
                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                </a>
                @endcan
                @can('delete-users')
                <button type="button" class="action-btn delete-btn" title="{{ __('Deactivate') }}"
                        onclick="confirmDelete('delete-form-{{ $user->id }}', '{{ $user->name }}')">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                </button>
                <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-none">
                    @csrf @method('DELETE')
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
                <p class="text-muted mt-3 fw-semibold mb-1">{{ __('No staff members found') }}</p>
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
    .role-badge {
        display: inline-block; padding: 3px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600;
    }
    .role-badge.admin { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .role-badge.staff { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600;
    }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.active   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.active .status-dot   { background: #22c55e; }
    .status-badge.inactive { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge.inactive .status-dot { background: #ef4444; }
    .action-btn {
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid #e9ecef; background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .action-btn.edit-btn   { color: #3b82f6; }
    .action-btn.edit-btn:hover   { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .action-btn.delete-btn { color: #ef4444; }
    .action-btn.delete-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .action-btn.reset-btn  { color: #6c757d; }
    .action-btn.reset-btn:hover  { background: #6c757d; color: #fff; border-color: #6c757d; }
    .form-select-sm { font-size: 0.875rem; border-color: #ced4da; }
</style>
@endsection
