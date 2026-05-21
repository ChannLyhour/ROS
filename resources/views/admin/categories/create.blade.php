@extends('layouts.app')

@section('title', __('New Category'))

@section('content')
<div class="category-page p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="flex-grow-1">
            <h2 class="fw-semibold mb-0" style="font-size: 1.25rem; color: #212529;">{{ __('New Category') }}</h2>
            <p class="text-muted small mb-0">{{ __('Define a new structural group for your restaurant menu') }}</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3 py-2">
                <i data-lucide="arrow-left" style="width: 15px;"></i>
                <span class="d-none d-sm-inline">{{ __('Back to List') }}</span>
                <span class="d-inline d-sm-none">{{ __('Back') }}</span>
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border rounded" style="border-color: #dee2e6 !important;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <!-- Left: Form Fields -->
                    <div class="col-lg-8">
                        <p class="text-muted extra-small text-uppercase fw-semibold mb-3" style="letter-spacing: 0.05em;">{{ __('Category Details') }}</p>

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">{{ __('Category Name') }}</label>
                            <input type="text" name="name"
                                class="form-control form-control-sm @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="{{ __('e.g. Italian Specialties') }}"
                                required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">{{ __('Description') }}</label>
                            <textarea name="description"
                                class="form-control form-control-sm @error('description') is-invalid @enderror"
                                rows="4"
                                placeholder="{{ __('Briefly describe what items fall under this category...') }}">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="plus-circle" style="width: 15px;"></i>
                                {{ __('Create Category') }}
                            </button>
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="x" style="width: 15px;"></i>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>

                    <!-- Right: Visibility -->
                    <div class="col-lg-4">
                        <p class="text-muted extra-small text-uppercase fw-semibold mb-3" style="letter-spacing: 0.05em;">{{ __('Visibility') }}</p>

                        <div class="border rounded p-3" style="border-color: #dee2e6 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-semibold text-dark">{{ __('Visible on Menu') }}</span>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" checked style="width: 2.4rem; height: 1.2rem; cursor: pointer;">
                                    <input type="hidden" name="status" id="statusHidden" value="0" disabled>
                                </div>
                            </div>
                            <div id="statusBadgeWrap">
                                <span id="statusBadge" class="badge bg-success-subtle text-success d-flex align-items-center gap-1 justify-content-center py-2">
                                    <i data-lucide="eye" style="width: 13px;"></i> {{ __('Active & Visible') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-light border rounded small text-muted" style="border-color: #dee2e6 !important;">
                            <div class="d-flex gap-2">
                                <i data-lucide="info" class="text-primary flex-shrink-0" style="width: 15px; margin-top: 2px;"></i>
                                <span>{{ __('Items in a disabled category will be hidden from the menu grid.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .category-page { font-family: inherit; }
    .extra-small { font-size: 0.72rem; }
    .form-control-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-control-sm:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15); }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
    .card { border-radius: 6px; }
</style>

<script>
    document.getElementById('statusSwitch').onchange = function () {
        const badge = document.getElementById('statusBadge');
        const hidden = document.getElementById('statusHidden');
        if (this.checked) {
            badge.className = 'badge bg-success-subtle text-success d-flex align-items-center gap-1 justify-content-center py-2';
            badge.innerHTML = '<i data-lucide="eye" style="width:13px;"></i> {{ __("Active & Visible") }}';
            hidden.disabled = true;
        } else {
            badge.className = 'badge bg-secondary-subtle text-secondary d-flex align-items-center gap-1 justify-content-center py-2';
            badge.innerHTML = '<i data-lucide="eye-off" style="width:13px;"></i> {{ __("Hidden / Disabled") }}';
            hidden.disabled = false;
        }
        lucide.createIcons();
    };
</script>
@endsection
