@extends('layouts.app')

@section('title', isset($role) ? __('Edit Role') : __('New Role'))

@section('content')
<div class="p-1 p-md-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">
                {{ isset($role) ? __('Edit Role') : __('New Role') }}
            </h2>
            <p class="text-muted small mb-0">{{ __('Configure access levels and permission groups') }}</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>{{ __('Back') }}
        </a>
    </div>

    <div class="row g-4">
        <!-- Left: Role Form -->
        <div class="col-lg-4">
            <div class="card border sticky-top" style="border-color:#dee2e6 !important; border-radius:6px; top:20px;">
                <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#dee2e6 !important;">
                    <span class="fw-semibold small text-dark">{{ __('Role Details') }}</span>
                </div>
                <div class="card-body p-4">
                    <form id="roleForm"
                          action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}"
                          method="POST">
                        @csrf
                        @if(isset($role)) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">{{ __('Role Title') }}</label>
                            <input type="text" name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   placeholder="{{ __('e.g. Admin, Chef, Cashier') }}"
                                   value="{{ old('name', $role->name ?? '') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">{{ __('Description') }}</label>
                            <textarea name="description" rows="4"
                                      class="form-control form-control-sm"
                                      placeholder="{{ __('Describe the access levels for this role...') }}">{{ old('description', $role->description ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="shield-check" style="width:15px;"></i>
                            {{ isset($role) ? __('Update Role') : __('Create Role') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Permissions -->
        <div class="col-lg-8">
            <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center"
                     style="border-color:#dee2e6 !important;">
                    <span class="fw-semibold small text-dark">{{ __('Permissions Matrix') }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="extra-small text-muted">{{ __('Select All') }}</span>
                        <input class="form-check-input m-0" type="checkbox" id="selectAll"
                               style="cursor:pointer; width:18px; height:18px;">
                    </div>
                </div>
                <div class="card-body p-4" style="background:#f8f9fa;">
                    <div class="row g-3">
                        @php
                        $groups = [
                            'order'       => ['icon' => 'shopping-cart', 'color' => '#6366f1'],
                            'menu'        => ['icon' => 'book-open',     'color' => '#10b981'],
                            'table'       => ['icon' => 'layout',        'color' => '#f59e0b'],
                            'payment'     => ['icon' => 'credit-card',   'color' => '#0ea5e9'],
                            'staff'       => ['icon' => 'users',         'color' => '#ec4899'],
                            'role'        => ['icon' => 'shield-check',  'color' => '#f43f5e'],
                            'setting'     => ['icon' => 'settings',      'color' => '#64748b'],
                            'report'      => ['icon' => 'bar-chart-3',   'color' => '#8b5cf6'],
                            'translation' => ['icon' => 'languages',     'color' => '#f59e0b'],
                        ];
                        $iconMap = ['view'=>'eye','create'=>'plus-circle','edit'=>'edit-3','delete'=>'trash-2','manage'=>'settings-2','void'=>'slash','refund'=>'rotate-ccw'];
                        @endphp

                        @foreach($groups as $prefix => $style)
                        <div class="col-md-6 col-xl-4">
                            <div class="perm-card border bg-white p-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color:#f1f3f5 !important;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="perm-icon" style="background:{{ $style['color'] }}18; color:{{ $style['color'] }};">
                                            <i data-lucide="{{ $style['icon'] }}" style="width:13px;height:13px;"></i>
                                        </div>
                                        <span class="fw-semibold small text-uppercase" style="color:{{ $style['color'] }}; letter-spacing:0.04em; font-size:0.72rem;">{{ $prefix }}s</span>
                                    </div>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none group-select-btn"
                                            data-prefix="{{ $prefix }}"
                                            style="font-size:0.7rem; font-weight:700; color:#6c757d;">
                                        ALL
                                    </button>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    @foreach($permissions->filter(fn($p) => str_contains($p->name, $prefix)) as $permission)
                                    @php
                                        $matchedIcon = 'circle';
                                        foreach($iconMap as $key => $icon) {
                                            if(str_contains(strtolower($permission->name), $key)) { $matchedIcon = $icon; break; }
                                        }
                                    @endphp
                                    <label class="perm-item d-flex align-items-center justify-content-between p-2 rounded cursor-pointer mb-0"
                                           style="border-radius:4px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i data-lucide="{{ $matchedIcon }}" class="text-muted" style="width:12px;height:12px;"></i>
                                            <span style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.03em;">
                                                {{ trim(str_replace(['-', $prefix.'s', $prefix], ' ', $permission->name)) }}
                                            </span>
                                        </div>
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                               class="perm-checkbox group-{{ $prefix }}" id="perm_{{ $permission->id }}" form="roleForm"
                                               style="width:15px;height:15px;accent-color:#0d6efd;cursor:pointer;"
                                               {{ (isset($rolePermissions) && in_array($permission->name, $rolePermissions)) ? 'checked' : '' }}>
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
    body { background-color: #f8fafc !important; }
    .extra-small { font-size: 0.72rem; }
    .form-control-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-control-sm:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
    .perm-icon {
        width: 26px; height: 26px; border-radius: 4px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .perm-item {
        cursor: pointer; transition: background 0.1s;
    }
    .perm-item:hover { background: #f8f9fa; }
    .perm-item:has(input:checked) { background: #eff6ff; }
</style>

@push('js')
<script>
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = this.checked);
    });
    document.querySelectorAll('.group-select-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const prefix = this.dataset.prefix;
            const boxes = document.querySelectorAll('.group-' + prefix);
            const allChecked = Array.from(boxes).every(cb => cb.checked);
            boxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = !allChecked ? 'NONE' : 'ALL';
        });
    });
</script>
@endpush
@endsection