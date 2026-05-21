@extends('layouts.app')

@section('title', __('Edit Customer'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size: 1.25rem; color: #212529;">{{ __('Edit Customer') }}</h2>
            <p class="text-muted small mb-0">{{ __('Updating records for') }} <strong>{{ $customer->name }}</strong></p>
        </div>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>
            <span class="d-none d-sm-inline">{{ __('Back to List') }}</span>
            <span class="d-inline d-sm-none">{{ __('Back') }}</span>
        </a>
    </div>

    <!-- Card -->
    <div class="card border" style="border-color: #dee2e6 !important; border-radius: 6px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-4">

                    <!-- Left: Photo -->
                    <div class="col-lg-3 text-center">
                        <div class="pb-3 pb-lg-0 pe-lg-4">
                            <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Profile Photo') }}</p>
                            <img src="{{ $customer->display_image }}"
                                 id="preview"
                                 class="rounded-circle border mb-3"
                                 style="width:120px; height:120px; object-fit:cover; border-color:#dee2e6 !important;">
                            <br>
                            <input type="file" name="image" id="imageInput" class="d-none" onchange="previewImage(event)">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                                    onclick="document.getElementById('imageInput').click()">
                                <i data-lucide="camera" style="width:13px;" class="me-1"></i>
                                {{ __('Change Photo') }}
                            </button>
                            <p class="text-muted mt-2 mb-0" style="font-size:0.72rem;">{{ __('Max 2MB') }}</p>
                        </div>
                    </div>

                    <!-- Right: Fields -->
                    <div class="col-lg-9">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Customer Details') }}</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Full Name') }}</label>
                                <input type="text" name="name"
                                       class="form-control form-control-sm @error('name') is-invalid @enderror"
                                       placeholder="{{ __('Enter customer name') }}"
                                       value="{{ old('name', $customer->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Email Address') }}</label>
                                <input type="email" name="email"
                                       class="form-control form-control-sm @error('email') is-invalid @enderror"
                                       placeholder="{{ __('customer@email.com') }}"
                                       value="{{ old('email', $customer->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Phone Number') }}</label>
                                <input type="text" name="phone"
                                       class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                       placeholder="{{ __('+000 000 000') }}"
                                       value="{{ old('phone', $customer->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('City') }}</label>
                                <input type="text" name="city"
                                       class="form-control form-control-sm @error('city') is-invalid @enderror"
                                       placeholder="{{ __('Enter city') }}"
                                       value="{{ old('city', $customer->city) }}">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">{{ __('Address') }}</label>
                                <textarea name="address" rows="3"
                                          class="form-control form-control-sm @error('address') is-invalid @enderror"
                                          placeholder="{{ __('Full residential address') }}">{{ old('address', $customer->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 mt-2 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="save" style="width:15px;"></i>
                                {{ __('Update Customer') }}
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-4 py-2">
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
    body { background-color: #f8fafc !important; }
    .extra-small { font-size: 0.72rem; }
    .tracking { letter-spacing: 0.05em; }
    .form-control-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-control-sm:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
</style>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        document.getElementById('preview').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection