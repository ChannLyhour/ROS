@extends('layouts.app')

@section('title', __('Edit Currency'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size: 1.25rem; color: #212529;">{{ __('Edit Currency') }}: {{ $currency->name }}</h2>
            <p class="text-muted small mb-0">{{ __('Modifying parameters for') }} <strong>{{ $currency->name }}</strong></p>
        </div>
        <a href="{{ route('currencies.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>
            <span class="d-none d-sm-inline">{{ __('Back to List') }}</span>
            <span class="d-inline d-sm-none">{{ __('Back') }}</span>
        </a>
    </div>

    <!-- Card -->
    <div class="card border" style="border-color: #dee2e6 !important; border-radius: 6px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('currencies.update', $currency->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-4">

                    <!-- Left: Form Fields -->
                    <div class="col-lg-8">
                        <p class="text-muted extra-small text-uppercase fw-semibold mb-3 tracking">{{ __('Currency Details') }}</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">{{ __('Currency Name') }}</label>
                            <input type="text" name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name', $currency->name) }}"
                                   placeholder="{{ __('e.g. US Dollar, Cambodian Riel') }}"
                                   required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">{{ __('Display Symbol') }}</label>
                            <div class="input-group" style="max-width: 200px;">
                                <span class="input-group-text bg-white" style="border-color: #ced4da; font-size: 0.875rem;">
                                    <i data-lucide="type" style="width:14px; color:#6c757d;"></i>
                                </span>
                                <input type="text" name="symbol"
                                       class="form-control form-control-sm @error('symbol') is-invalid @enderror"
                                       value="{{ old('symbol', $currency->symbol) }}"
                                       placeholder="{{ __('e.g. $, ៛') }}"
                                       required>
                                @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="save" style="width:15px;"></i>
                                {{ __('Update Currency') }}
                            </button>
                            <a href="{{ route('currencies.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="x" style="width:15px;"></i>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>

                    <!-- Right: Status + Danger -->
                    <div class="col-lg-4">
                        <p class="text-muted extra-small text-uppercase fw-semibold mb-3 tracking">{{ __('Status') }}</p>

                        <div class="border rounded p-3 mb-3" style="border-color: #dee2e6 !important; border-radius: 6px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-semibold text-dark">{{ __('Active Symbol') }}</span>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="statusSwitch" {{ $currency->is_active ? 'checked' : '' }}
                                           style="width:2.4rem; height:1.2rem; cursor:pointer;">
                                </div>
                            </div>
                            @if($currency->is_active)
                            <span id="statusBadge" class="badge bg-success-subtle text-success d-flex align-items-center gap-1 justify-content-center py-2">
                                <span class="d-inline-block rounded-circle bg-success" style="width:7px;height:7px;"></span> {{ __('Active') }}
                            </span>
                            @else
                            <span id="statusBadge" class="badge bg-danger-subtle text-danger d-flex align-items-center gap-1 justify-content-center py-2">
                                <span class="d-inline-block rounded-circle bg-danger" style="width:7px;height:7px;"></span> {{ __('Inactive') }}
                            </span>
                            @endif
                        </div>

                        <div class="p-3 bg-light border rounded small text-muted mb-3" style="border-color: #dee2e6 !important; border-radius: 6px;">
                            <div class="d-flex gap-2">
                                <i data-lucide="shield" class="text-secondary flex-shrink-0" style="width:15px; margin-top:2px;"></i>
                                <span>{{ __('All symbol changes are tracked in the audit log.') }}</span>
                            </div>
                        </div>

                        <button type="button"
                                class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                                onclick="confirmDelete('delete-form-{{ $currency->id }}', '{{ $currency->name }}')">
                            <i data-lucide="trash-2" style="width:14px;"></i>
                            {{ __('Delete Currency') }}
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-form-{{ $currency->id }}" action="{{ route('currencies.destroy', $currency->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .extra-small { font-size: 0.72rem; }
    .tracking { letter-spacing: 0.05em; }
    .form-control-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-control-sm:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .input-group .input-group-text { border-radius: 4px 0 0 4px; }
    .input-group .form-control-sm { border-radius: 0 4px 4px 0; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
    .btn-outline-danger { border-radius: 4px; font-size: 0.875rem; }
</style>

<script>
    document.getElementById('statusSwitch').onchange = function () {
        const badge = document.getElementById('statusBadge');
        if (this.checked) {
            badge.className = 'badge bg-success-subtle text-success d-flex align-items-center gap-1 justify-content-center py-2';
            badge.innerHTML = '<span class="d-inline-block rounded-circle bg-success" style="width:7px;height:7px;"></span> {{ __("Active") }}';
        } else {
            badge.className = 'badge bg-danger-subtle text-danger d-flex align-items-center gap-1 justify-content-center py-2';
            badge.innerHTML = '<span class="d-inline-block rounded-circle bg-danger" style="width:7px;height:7px;"></span> {{ __("Inactive") }}';
        }
    };
</script>
@endsection
