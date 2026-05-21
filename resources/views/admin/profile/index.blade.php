@extends('layouts.app')

@section('title', __('My Profile'))

@section('content')
<div class="p-1 p-md-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('My Profile') }}</h2>
            <p class="text-muted small mb-0">{{ __('Manage your personal credentials and contact info') }}</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>{{ __('Dashboard') }}
        </a>
    </div>

    <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Left: Photo -->
                    <div class="col-lg-3 text-center">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Profile Photo') }}</p>
                        <img src="{{ $user->display_image }}" id="preview"
                             class="rounded-circle border mb-3"
                             style="width:120px;height:120px;object-fit:cover;border-color:#dee2e6 !important;">
                        <br>
                        <input type="file" name="image" id="imageInput" class="d-none" onchange="previewImage(event)">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                                onclick="document.getElementById('imageInput').click()">
                            <i data-lucide="camera" style="width:13px;" class="me-1"></i>
                            {{ __('Change Photo') }}
                        </button>
                        <p class="text-muted mt-2 mb-0" style="font-size:0.72rem;">{{ __('Shown across the admin console') }}</p>

                        <hr class="my-3" style="border-color:#f1f3f5;">

                        <div class="text-start">
                            <div class="extra-small text-muted text-uppercase fw-semibold tracking mb-1">{{ __('Administrative Role') }}</div>
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 border rounded bg-light" style="border-color:#dee2e6 !important; border-radius:4px;">
                                <i data-lucide="shield-check" class="text-primary" style="width:14px;"></i>
                                <span class="fw-semibold small text-dark">{{ $user->role->name ?? 'Administrator' }}</span>
                            </div>
                            <p class="text-muted mt-2 mb-0" style="font-size:0.72rem;">{{ __('Roles are managed by system owners') }}</p>
                        </div>
                    </div>

                    <!-- Right: Fields -->
                    <div class="col-lg-9">
                        <!-- Personal Info -->
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Personal Information') }}</p>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Display Name') }}</label>
                                <input type="text" name="name"
                                       class="form-control form-control-sm @error('name') is-invalid @enderror"
                                       placeholder="{{ __('Enter your name') }}"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Email Address') }}</label>
                                <input type="email" name="email"
                                       class="form-control form-control-sm @error('email') is-invalid @enderror"
                                       placeholder="{{ __('your@email.com') }}"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Phone Number') }}</label>
                                <input type="text" name="phone"
                                       class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                       placeholder="{{ __('+000 000 000') }}"
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-3" style="border-color:#f1f3f5;">

                        <!-- Security -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i data-lucide="lock" class="text-primary" style="width:15px;"></i>
                            <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-0">{{ __('Security & Password') }}</p>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('New Password') }}</label>
                                <input type="password" name="password"
                                       class="form-control form-control-sm @error('password') is-invalid @enderror"
                                       placeholder="{{ __('Leave blank to keep current') }}">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control form-control-sm"
                                       placeholder="{{ __('Repeat new password') }}">
                            </div>
                        </div>

                        <hr class="my-3" style="border-color:#f1f3f5;">

                        <!-- Location -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i data-lucide="map-pin" class="text-primary" style="width:15px;"></i>
                            <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-0">{{ __('Location') }}</p>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">{{ __('Street Address') }}</label>
                                <input type="text" name="address"
                                       class="form-control form-control-sm @error('address') is-invalid @enderror"
                                       placeholder="{{ __('Enter your street address') }}"
                                       value="{{ old('address', $user->address) }}">
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('City') }}</label>
                                <input type="text" name="city"
                                       class="form-control form-control-sm @error('city') is-invalid @enderror"
                                       placeholder="{{ __('Enter city') }}"
                                       value="{{ old('city', $user->city) }}">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">{{ __('State / Region') }}</label>
                                <input type="text" name="state"
                                       class="form-control form-control-sm @error('state') is-invalid @enderror"
                                       placeholder="{{ __('Enter state') }}"
                                       value="{{ old('state', $user->state) }}">
                                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="save" style="width:15px;"></i>
                                {{ __('Save Changes') }}
                            </button>
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
    reader.onload = () => { document.getElementById('preview').src = reader.result; };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection