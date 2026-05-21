@extends('layouts.app')

@section('title', __('System Settings'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('System Settings') }}</h2>
            <p class="text-muted small mb-0">{{ __("Configure your restaurant's global parameters") }}</p>
        </div>
        <button type="submit" form="settingsForm" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
            <i data-lucide="save" style="width:15px;"></i>{{ __('Save All Settings') }}
        </button>
    </div>

    <form id="settingsForm" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Business Information -->
            <div class="col-lg-6">
                <div class="card border h-100" style="border-color:#dee2e6 !important; border-radius:6px;">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#dee2e6 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="building-2" class="text-primary" style="width:16px;"></i>
                            <span class="fw-semibold small text-dark text-uppercase">{{ __('Business Information') }}</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">{{ __('Restaurant Name') }}</label>
                                <input type="text" name="business_name"
                                    class="form-control form-control-sm"
                                    placeholder="{{ __('e.g. Gourmet Palace') }}"
                                    value="{{ old('business_name', $settings['business_name'] ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">{{ __('Legal Address') }}</label>
                                <textarea name="business_address" rows="3"
                                    class="form-control form-control-sm"
                                    placeholder="{{ __('Full address...') }}">{{ old('business_address', $settings['business_address'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Phone') }}</label>
                                <input type="text" name="business_phone"
                                    class="form-control form-control-sm"
                                    value="{{ old('business_phone', $settings['business_phone'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Email') }}</label>
                                <input type="email" name="business_email"
                                    class="form-control form-control-sm"
                                    value="{{ old('business_email', $settings['business_email'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Settings -->
            <div class="col-lg-6">
                <div class="card border h-100" style="border-color:#dee2e6 !important; border-radius:6px;">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#dee2e6 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="settings-2" class="text-primary" style="width:16px;"></i>
                            <span class="fw-semibold small text-dark text-uppercase">{{ __('Financial & Service') }}</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Currency Symbol') }}</label>
                                <select name="currency_symbol" class="form-select form-select-sm select2">
                                    <option value="" disabled>{{ __('Select Currency') }}</option>
                                    @foreach($currencies as $currency)
                                    <option value="{{ $currency->symbol }}"
                                        {{ old('currency_symbol', $settings['currency_symbol'] ?? '$') == $currency->symbol ? 'selected' : '' }}>
                                        {{ $currency->name }} ({{ $currency->symbol }})
                                    </option>
                                    @endforeach
                                    @if($currencies->isEmpty())
                                    <option value="$" selected>$ (US Dollar)</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Tax Percentage (%)') }}</label>
                                <div class="input-group" style="max-width:150px;">
                                    <input type="number" step="0.01" name="tax_percentage"
                                        class="form-control form-control-sm"
                                        placeholder="0.00"
                                        value="{{ old('tax_percentage', $settings['tax_percentage'] ?? '0') }}"
                                        style="border-radius:4px 0 0 4px;">
                                    <span class="input-group-text bg-white text-muted small" style="border-color:#ced4da; border-radius:0 4px 4px 0;">%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Exchange Rate (1$ = ? ៛)') }}</label>
                                <input type="number" step="1" name="currency_exchange_rate"
                                    class="form-control form-control-sm"
                                    placeholder="4100"
                                    value="{{ old('currency_exchange_rate', $settings['currency_exchange_rate'] ?? '4100') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="col-12">
                <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#dee2e6 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="palette" class="text-primary" style="width:16px;"></i>
                            <span class="fw-semibold small text-dark text-uppercase">{{ __('Branding & Aesthetics') }}</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Logo -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">{{ __('System Logo') }}</label>
                                <div class="border rounded p-3 text-center bg-white mb-3"
                                    style="height:130px; display:flex; align-items:center; justify-content:center; border-color:#dee2e6 !important; border-radius:6px;">
                                    <img src="{{ $appSettings['logo'] }}" id="logoPreview" style="max-height:90px;width:auto;">
                                </div>
                                <input type="file" name="business_logo" id="logoInput" class="d-none" onchange="previewImage(event,'logoPreview')">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                    onclick="document.getElementById('logoInput').click()">
                                    <i data-lucide="image" style="width:13px;" class="me-1"></i>{{ __('Change Logo') }}
                                </button>
                                <div class="text-muted mt-2" style="font-size:0.72rem;">{{ __('Used in invoices and dashboard') }}</div>
                            </div>
                            <!-- Favicon -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">{{ __('Browser Favicon') }}</label>
                                <div class="border rounded p-3 text-center bg-white mb-3"
                                    style="height:130px; display:flex; align-items:center; justify-content:center; border-color:#dee2e6 !important; border-radius:6px;">
                                    <img src="{{ $appSettings['favicon'] }}" id="faviconPreview" style="max-height:64px;width:auto;">
                                </div>
                                <input type="file" name="business_favicon" id="faviconInput" class="d-none" onchange="previewImage(event,'faviconPreview')">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                    onclick="document.getElementById('faviconInput').click()">
                                    <i data-lucide="framer" style="width:13px;" class="me-1"></i>{{ __('Change Favicon') }}
                                </button>
                                <div class="text-muted mt-2" style="font-size:0.72rem;">{{ __('Displayed in browser tabs (PNG or ICO)') }}</div>
                            </div>
                            <!-- Tip -->
                            <div class="col-md-4">
                                <div class="p-4 bg-light border rounded h-100 d-flex flex-column justify-content-center"
                                    style="border-color:#dee2e6 !important; border-radius:6px;">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i data-lucide="info" class="text-primary" style="width:15px;"></i>
                                        <span class="fw-semibold small text-dark">{{ __('Visual Identity') }}</span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        {{ __('High-resolution transparent PNGs are recommended for the best look across light and dark modes.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    body {
        background-color: #f8fafc !important;
    }

    .extra-small {
        font-size: 0.72rem;
    }

    .form-control-sm {
        border-radius: 4px;
        border-color: #ced4da;
        font-size: 0.9rem;
    }

    .form-control-sm:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .form-select-sm {
        border-radius: 4px;
        border-color: #ced4da;
        font-size: 0.9rem;
    }

    .form-select-sm:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .btn-outline-secondary {
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>

@push('js')
<script>
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = () => {
            document.getElementById(previewId).src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
@endsection