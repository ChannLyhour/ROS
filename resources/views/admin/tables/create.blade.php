@extends('layouts.app')

@section('title', __('New Table'))

@section('content')
<div class="p-1 p-md-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('New Dining Table') }}</h2>
            <p class="text-muted small mb-0">{{ __('Register a new seating area for your restaurant') }}</p>
        </div>
        <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>
            <span class="d-none d-sm-inline">{{ __('Back to Tables') }}</span>
            <span class="d-inline d-sm-none">{{ __('Back') }}</span>
        </a>
    </div>

    <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <!-- Left: Info + Status -->
                    <div class="col-lg-4">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Status') }}</p>
                        <div class="border rounded p-3 mb-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                            <label class="form-label fw-semibold small text-dark mb-2">{{ __('Initial Status') }}</label>
                            <select name="status" class="form-select form-select-sm select2" style="border-radius:4px;">
                                <option value="{{ __('Available') }}" selected>{{ __('Available') }}</option>
                                <option value="{{ __('Taken') }}">{{ __('Taken') }}</option>
                                <option value="{{ __('Reserved') }}">{{ __('Reserved') }}</option>
                            </select>
                        </div>
                        <div class="p-3 bg-light border rounded small text-muted" style="border-color:#dee2e6 !important; border-radius:6px;">
                            <div class="d-flex gap-2">
                                <i data-lucide="info" class="text-primary flex-shrink-0" style="width:15px;margin-top:2px;"></i>
                                <span>{{ __('New tables default to Available for immediate guest seating.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Fields -->
                    <div class="col-lg-8">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Table Details') }}</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">{{ __('Table Name / Number') }}</label>
                            <input type="text" name="name"
                                class="form-control form-control-sm @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="{{ __('e.g. VIP-01 or Table 12') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">{{ __('Seating Capacity') }}</label>
                            <div class="input-group" style="max-width:200px;">
                                <span class="input-group-text bg-white text-muted" style="border-color:#ced4da; border-radius:4px 0 0 4px;">
                                    <i data-lucide="users" style="width:14px;"></i>
                                </span>
                                <input type="number" name="capacity"
                                    class="form-control form-control-sm @error('capacity') is-invalid @enderror"
                                    value="{{ old('capacity', 2) }}" min="1" required
                                    style="border-radius:0;">
                                <span class="input-group-text bg-white text-muted small" style="border-color:#ced4da; border-radius:0 4px 4px 0;">
                                    {{ __('Guests') }}
                                </span>
                            </div>
                            @error('capacity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="plus-circle" style="width:15px;"></i>
                                {{ __('Create Table') }}
                            </button>
                            <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="x" style="width:15px;"></i>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    body {
        background-color: #f8fafc !important;
    }

    .extra-small {
        font-size: 0.72rem;
    }

    .tracking {
        letter-spacing: 0.05em;
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
</style>
@endsection