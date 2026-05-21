@extends('layouts.app')

@section('title', __('New Currency'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size: 1.25rem; color: #212529;">{{ __('New Currency') }}</h2>
            <p class="text-muted small mb-0">{{ __('Define a new financial symbol for your platform') }}</p>
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
            <form action="{{ route('currencies.store') }}" method="POST">
                @csrf
                <div class="row g-4">

                    <!-- Left: Form Fields -->
                    <div class="col-lg-8">
                        <p class="text-muted extra-small text-uppercase fw-semibold mb-3 tracking">{{ __('Currency Details') }}</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">{{ __('Currency Name') }}</label>
                            <input type="text" name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
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
                                       value="{{ old('symbol') }}"
                                       placeholder="{{ __('e.g. $, ៛') }}"
                                       required>
                                @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="plus-circle" style="width:15px;"></i>
                                {{ __('Save Currency') }}
                            </button>
                            <a href="{{ route('currencies.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="x" style="width:15px;"></i>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>

                    <!-- Right: Status -->
                    <div class="col-lg-4">
                        <p class="text-muted extra-small text-uppercase fw-semibold mb-3 tracking">{{ __('Status') }}</p>

                        <div class="border rounded p-3 mb-3" style="border-color: #dee2e6 !important; border-radius: 6px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-semibold text-dark">{{ __('Active Symbol') }}</span>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="statusSwitch" checked style="width:2.4rem; height:1.2rem; cursor:pointer;">
                                </div>
                            </div>
                            <span id="statusBadge" class="badge bg-success-subtle text-success d-flex align-items-center gap-1 justify-content-center py-2">
                                <span class="status-dot bg-success"></span> {{ __('Active') }}
                            </span>
                        </div>

                        <div class="p-3 bg-light border rounded small text-muted" style="border-color: #dee2e6 !important; border-radius: 6px;">
                            <div class="d-flex gap-2">
                                <i data-lucide="info" class="text-primary flex-shrink-0" style="width:15px; margin-top:2px;"></i>
                                <span>{{ __('Ensure symbols are correctly formatted for invoices and receipts.') }}</span>
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
    .extra-small { font-size: 0.72rem; }
    .tracking { letter-spacing: 0.05em; }
    .form-control-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-control-sm:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .input-group .input-group-text { border-radius: 4px 0 0 4px; }
    .input-group .form-control-sm { border-radius: 0 4px 4px 0; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
    .status-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; }
</style>

<script>
    document.getElementById('statusSwitch').onchange = function () {
        const badge = document.getElementById('statusBadge');
        if (this.checked) {
            badge.className = 'badge bg-success-subtle text-success d-flex align-items-center gap-1 justify-content-center py-2';
            badge.innerHTML = '<span class="status-dot bg-success d-inline-block" style="width:7px;height:7px;border-radius:50%;"></span> {{ __("Active") }}';
        } else {
            badge.className = 'badge bg-danger-subtle text-danger d-flex align-items-center gap-1 justify-content-center py-2';
            badge.innerHTML = '<span class="status-dot bg-danger d-inline-block" style="width:7px;height:7px;border-radius:50%;"></span> {{ __("Inactive") }}';
        }
    };
</script>
@endsection
