@extends('layouts.app')

@section('content')
<div class="role-form-page p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black mb-0 text-dark">{{ isset($role) ? 'Edit Security Role' : 'Define New Role' }}</h2>
            <p class="text-muted small fw-bold mb-0 text-uppercase tracking-wider">Configure access levels and permission groups</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-white border shadow-sm px-4 rounded-lg fw-bold">
            <i data-lucide="arrow-left" class="me-2" style="width: 18px;"></i> Back to List
        </a>
    </div>

    <div class="row g-4 transition-all">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-black text-dark mb-0">Role Details</h5>
                </div>
                <div class="card-body p-4 pt-3">
                    <form id="roleForm" action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST">
                        @csrf
                        @if(isset($role)) @method('PUT') @endif

                        <div class="mb-4">
                            <label class="info-label mb-2">Role Title</label>
                            <input type="text" name="name" class="form-control premium-field @error('name') is-invalid @enderror"
                                placeholder="e.g. Master Admin, Senior Chef" value="{{ old('name', $role->name ?? '') }}" required>
                            @error('name') <div class="invalid-feedback text-danger small mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="info-label mb-2">Access Description</label>
                            <textarea name="description" class="form-control premium-field pt-3" rows="4"
                                placeholder="Describe the levels of access assigned to this role...">{{ old('description', $role->description ?? '') }}</textarea>
                            @error('description') <div class="invalid-feedback text-danger small mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-black rounded-lg shadow-sm transform-active text-uppercase">
                                <i data-lucide="shield-check" class="me-2"></i> {{ isset($role) ? 'Update Role' : 'Create Role' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-black text-dark mb-0">Permissions Matrix</h5>
                        <p class="text-muted extra-small mb-0 fw-bold">SELECT ALL ABILITIES ASSOCIATED WITH THIS PROFILE</p>
                    </div>
                    <div class="form-check form-switch p-0">
                        <label class="extra-small fw-black text-muted me-2" for="selectAll">SELECT ALL</label>
                        <input class="form-check-input ms-0 mt-0" type="checkbox" id="selectAll" style="cursor: pointer; width: 40px; height: 20px;">
                    </div>
                </div>
                <div class="card-body p-4 bg-light bg-opacity-50">
                    <div class="row g-3">
                        @php
                        $groups = [
                            'order' => ['icon' => 'shopping-cart', 'color' => '#6366f1'],
                            'menu' => ['icon' => 'book-open', 'color' => '#10b981'],
                            'table' => ['icon' => 'layout', 'color' => '#f59e0b'],
                            'payment' => ['icon' => 'credit-card', 'color' => '#0ea5e9'],
                            'staff' => ['icon' => 'users', 'color' => '#ec4899'],
                            'role' => ['icon' => 'shield-check', 'color' => '#f43f5e'],
                            'setting' => ['icon' => 'settings', 'color' => '#64748b'],
                            'report' => ['icon' => 'bar-chart-3', 'color' => '#8b5cf6'],
                            'translation' => ['icon' => 'languages', 'color' => '#f59e0b'],
                        ];
                        @endphp

                        @foreach($groups as $prefix => $style)
                        <div class="col-md-6 col-xl-4 permission-segment" data-prefix="{{ $prefix }}">
                            <div class="permission-group-card bg-white border-0 shadow-sm rounded-lg p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-lg" style="background-color: {{ $style['color'] }}15; color: {{ $style['color'] }}">
                                            <i data-lucide="{{ $style['icon'] }}" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <h6 class="mb-0 fw-black text-uppercase small" style="color: {{ $style['color'] }}; letter-spacing: 0.5px;">{{ $prefix }}s</h6>
                                    </div>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none extra-small fw-black text-primary group-select-btn" data-prefix="{{ $prefix }}">
                                        ALL
                                    </button>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @php
                                        $iconMap = [
                                            'view' => 'eye',
                                            'create' => 'plus-circle',
                                            'edit' => 'edit-3',
                                            'delete' => 'trash-2',
                                            'manage' => 'settings-2',
                                            'void' => 'slash',
                                            'refund' => 'rotate-ccw',
                                        ];
                                    @endphp
                                    @foreach($permissions->filter(fn($p) => str_contains($p->name, $prefix)) as $permission)
                                    @php
                                        $pName = strtolower($permission->name);
                                        $matchedIcon = 'circle';
                                        foreach($iconMap as $key => $icon) {
                                            if(str_contains($pName, $key)) {
                                                $matchedIcon = $icon;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <label class="permission-item p-2 rounded-lg border-dashed d-flex align-items-center justify-content-between cursor-pointer transition-all mb-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-1 rounded bg-light text-muted opacity-50">
                                                <i data-lucide="{{ $matchedIcon }}" style="width: 12px; height: 12px;"></i>
                                            </div>
                                            <span class="extra-small fw-bold text-dark text-uppercase">
                                                {{ trim(str_replace(['-', $prefix . 's', $prefix], ' ', $permission->name)) }}
                                            </span>
                                        </div>
                                        <div class="checkbox-wrapper-primary">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                class="perm-checkbox group-{{ $prefix }}" id="perm_{{ $permission->id }}" form="roleForm"
                                                {{ (isset($rolePermissions) && in_array($permission->name, $rolePermissions)) ? 'checked' : '' }}>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .role-form-page {
        font-family: 'Kantumruy Pro', sans-serif;
        background-color: #f1f5f9;
        background-image: radial-gradient(at 0% 0%, rgba(240, 137, 19, 0.05) 0, transparent 50%), 
                          radial-gradient(at 50% 0%, rgba(99, 102, 241, 0.05) 0, transparent 50%);
    }

    .fw-black {
        font-weight: 900 !important;
    }

    .info-label {
        font-weight: 800;
        font-size: 0.7rem;
        color: #f08913;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .premium-field {
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-weight: 600;
        padding: 14px 20px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.8);
    }

    .premium-field:focus {
        border-color: #f08913;
        background: white;
        box-shadow: 0 0 0 5px rgba(240, 137, 19, 0.15);
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #f08913 0%, #d97706 100%);
        border: none;
        letter-spacing: 1px;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(240, 137, 19, 0.5);
        filter: brightness(1.1);
    }

    .card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }

    .permission-group-card {
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .permission-group-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
    }

    .permission-item {
        border: 1px solid #f1f5f9;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .permission-item:hover {
        background-color: #ffffff;
        border-color: #cbd5e1;
        transform: translateX(3px);
    }

    .permission-item:has(input:checked) {
        background-color: #fff7ed;
        border-color: #f08913;
        border-style: solid;
        box-shadow: 0 4px 6px -1px rgba(240, 137, 19, 0.1);
    }

    .checkbox-wrapper-primary input {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        accent-color: #f08913;
    }

    @media (max-width: 768px) {
        .role-form-page {
            padding: 1rem !important;
        }

        .card-header.d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }

        .form-check.form-switch {
            padding-left: 0 !important;
        }
    }
</style>

@push('js')
<script>
    // Global Select All
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Group Select All
    document.querySelectorAll('.group-select-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const prefix = this.dataset.prefix;
            const groupCheckboxes = document.querySelectorAll('.group-' + prefix);

            // Check if all are already checked
            const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);

            // Toggle accordingly
            groupCheckboxes.forEach(cb => cb.checked = !allChecked);

            // Update button text
            this.innerText = !allChecked ? 'NONE' : 'ALL';
        });
    });
</script>
@endpush
@endsection