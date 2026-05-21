@extends('layouts.app')

@section('title', __('New Staff Member'))

@section('content')
<div class="p-1 p-md-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('New Staff Member') }}</h2>
            <p class="text-muted small mb-0">{{ __('Create a new system user account') }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>
            <span class="d-none d-sm-inline">{{ __('Back to Staff') }}</span>
            <span class="d-inline d-sm-none">{{ __('Back') }}</span>
        </a>
    </div>

    <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- Left: Photo -->
                    <div class="col-lg-3 text-center">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Profile Photo') }}</p>
                        <img src="{{ asset('images/placeholder.jpg') }}" id="preview"
                             class="rounded-circle border mb-3"
                             style="width:110px;height:110px;object-fit:cover;border-color:#dee2e6 !important;">
                        <br>
                        <input type="file" name="image_file" id="imageInput" class="d-none" onchange="previewImage(event)">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                                onclick="document.getElementById('imageInput').click()">
                            <i data-lucide="camera" style="width:13px;" class="me-1"></i>
                            {{ __('Upload Photo') }}
                        </button>
                        <p class="text-muted mt-2 mb-0" style="font-size:0.72rem;">{{ __('Max 2MB') }}</p>
                    </div>

                    <!-- Right: Fields -->
                    <div class="col-lg-9">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Account Details') }}</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Full Name') }}</label>
                                <input type="text" name="name"
                                       class="form-control form-control-sm @error('name') is-invalid @enderror"
                                       placeholder="{{ __('Enter full name') }}"
                                       value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Email Address') }}</label>
                                <input type="email" name="email"
                                       class="form-control form-control-sm @error('email') is-invalid @enderror"
                                       placeholder="{{ __('email@restaurant.com') }}"
                                       value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Role') }}</label>
                                <select name="role_id" class="form-select form-select-sm select2 @error('role_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>{{ __('Select a role') }}</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Phone Number') }}</label>
                                <input type="text" name="phone"
                                       class="form-control form-control-sm"
                                       placeholder="{{ __('+000 000 000') }}"
                                       value="{{ old('phone') }}">
                            </div>

                            <div class="col-12"><hr class="my-2" style="border-color:#f1f3f5;"></div>
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i data-lucide="shield-check" class="text-primary" style="width:16px;"></i>
                                    <span class="fw-semibold small text-dark">{{ __('Security Credentials') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Password') }}</label>
                                <input type="password" name="password"
                                       class="form-control form-control-sm @error('password') is-invalid @enderror"
                                       placeholder="{{ __('Minimum 8 characters') }}">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control form-control-sm"
                                       placeholder="{{ __('Repeat password') }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 mt-2 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="user-plus" style="width:15px;"></i>
                                {{ __('Create Account') }}
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-4 py-2">
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
    .form-select-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
</style>
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = () => { document.getElementById('preview').src = reader.result; };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
