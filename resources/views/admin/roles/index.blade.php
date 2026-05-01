@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<x-master-table
    title="Security Roles"
    subtitle="Define access hierarchies and system permission profiles"
    :createRoute="route('roles.create')"
    createLabel="Create New Role"
    :headers="['#', 'Role Identity', 'Permissions Info', 'Active Staff', 'Actions']"
    :items="$roles">
    @foreach($roles as $role)
    <tr>
        <td class="text-center">
            <span class="text-muted fw-bold">{{ $loop->iteration }}</span>
        </td>
        <td class="ps-4">
            <div class="fw-black text-dark text-uppercase tracking-wider">{{ $role->name }}</div>
            <code class="extra-small text-muted">{{ $role->slug }}</code>
        </td>
        <td class="text-start pe-5" style="max-width: 350px;">
            <p class="mb-2 extra-small text-dark fw-black text-uppercase">{{ $role->description ?? 'No specific description provided.' }}</p>
            <div class="d-flex flex-wrap gap-1">
                @forelse($role->permissions->take(5) as $permission)
                <span class="badge bg-light text-muted border py-1 px-2 fw-bold" style="font-size: 0.55rem;">{{ strtoupper($permission->name) }}</span>
                @empty
                <span class="text-muted extra-small italic">No permissions assigned</span>
                @endforelse
                @if($role->permissions->count() > 5)
                <span class="badge bg-primary-subtle text-primary py-1 px-2 fw-bold" style="font-size: 0.55rem;">+{{ $role->permissions->count() - 5 }} MORE</span>
                @endif
            </div>
        </td>
        <td class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <i data-lucide="users" class="text-muted" style="width: 14px;"></i>
                <span class="fw-black h6 mb-0">{{ $role->users_count }}</span>
            </div>
        </td>

        <td class="text-end pe-4">
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
    .fw-black {
        font-weight: 900 !important;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    .tracking-wider {
        letter-spacing: 0.05em;
    }

    .badge {
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    tr:hover td {
        background-color: rgba(240, 137, 19, 0.02) !important;
    }

    .badge-light {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .badge-primary-subtle {
        background: rgba(240, 137, 19, 0.1);
        color: #f08913;
        border: 1px solid rgba(240, 137, 19, 0.2);
    }
</style>
@endsection