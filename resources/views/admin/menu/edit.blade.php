@extends('layouts.app')

@section('title', __('Edit Menu Item'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size: 1.25rem; color: #212529;">{{ __('Edit Menu Item') }}</h2>
            <p class="text-muted small mb-0">{{ __('Editing') }}: <strong>{{ $menuItem->name }}</strong></p>
        </div>
        <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>
            <span class="d-none d-sm-inline">{{ __('Back to Menu') }}</span>
            <span class="d-inline d-sm-none">{{ __('Back') }}</span>
        </a>
    </div>

    <!-- Card -->
    <div class="card border" style="border-color: #dee2e6 !important; border-radius: 6px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('menu.update', $menuItem->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-4">

                    <!-- Left: Image + Status -->
                    <div class="col-lg-4">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Item Photo') }}</p>

                        <!-- Image Upload -->
                        <div id="imagePreviewContainer"
                             class="border rounded d-flex align-items-center justify-content-center mb-3 position-relative"
                             style="height:220px; border-color:#dee2e6 !important; border-radius:6px; overflow:hidden; cursor:pointer; background:#f8f9fa;"
                             onclick="document.getElementById('imageInput').click()">
                            @if($menuItem->image)
                            <img src="{{ asset('storage/' . $menuItem->image) }}" id="imagePreview" alt="Preview"
                                 class="w-100 h-100" style="object-fit:cover; position:absolute; inset:0;">
                            <div id="placeholderOverlay" class="text-center p-3 d-none">
                                <i data-lucide="image-plus" style="width:36px;height:36px;color:#ced4da;" class="mb-2"></i>
                                <p class="small text-muted mb-0 fw-semibold">{{ __('Click to change image') }}</p>
                            </div>
                            @else
                            <div id="placeholderOverlay" class="text-center p-3">
                                <i data-lucide="image-plus" style="width:36px;height:36px;color:#ced4da;" class="mb-2"></i>
                                <p class="small text-muted mb-0 fw-semibold">{{ __('Click to upload image') }}</p>
                                <p class="text-muted mb-0" style="font-size:0.72rem;">{{ __('Recommended 800×800px') }}</p>
                            </div>
                            <img src="" id="imagePreview" alt="Preview"
                                 class="d-none w-100 h-100" style="object-fit:cover; position:absolute; inset:0;">
                            @endif
                        </div>
                        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">
                        @error('image') <div class="text-danger small mb-3"><i data-lucide="alert-circle" style="width:13px;"></i> {{ $message }}</div> @enderror

                        <hr class="my-3" style="border-color:#f1f3f5;">

                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Availability') }}</p>
                        @php $isAvailable = old('status', $menuItem->status) == 'available'; @endphp
                        <div class="border rounded p-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-semibold text-dark">{{ __('Available for Order') }}</span>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input" type="checkbox" name="status" value="available"
                                           id="statusSwitch" {{ $isAvailable ? 'checked' : '' }}
                                           style="width:2.4rem;height:1.2rem;cursor:pointer;">
                                    <input type="hidden" name="status" id="statusHidden" value="unavailable"
                                           {{ $isAvailable ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <span id="statusBadge" class="status-badge {{ $isAvailable ? 'available' : 'unavailable' }} d-flex align-items-center justify-content-center gap-2 py-2">
                                <span class="status-dot"></span>
                                {{ $isAvailable ? __('Available') : __('Unavailable') }}
                            </span>
                        </div>
                    </div>

                    <!-- Right: Fields -->
                    <div class="col-lg-8">
                        <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Item Details') }}</p>

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">{{ __('Item Name') }}</label>
                            <input type="text" name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name', $menuItem->name) }}"
                                   placeholder="{{ __('e.g. Grilled Salmon') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Category + Price -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold small text-dark">{{ __('Category') }}</label>
                                <select name="category_id"
                                        class="form-select form-select-sm select2 @error('category_id') is-invalid @enderror"
                                        required>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $menuItem->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small text-dark">{{ __('Price') }} ({{ $appSettings['currency'] }})</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white small text-muted fw-semibold"
                                          style="border-color:#ced4da; border-radius:4px 0 0 4px;">
                                        {{ $appSettings['currency'] }}
                                    </span>
                                    <input type="number" step="0.01" name="price"
                                           class="form-control form-control-sm @error('price') is-invalid @enderror"
                                           value="{{ old('price', $menuItem->price) }}"
                                           placeholder="0.00" required
                                           style="border-radius:0 4px 4px 0;">
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">{{ __('Description') }}</label>
                            <textarea name="description" rows="6"
                                      class="form-control form-control-sm @error('description') is-invalid @enderror"
                                      placeholder="{{ __('Describe ingredients, taste, and presentation...') }}">{{ old('description', $menuItem->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="save" style="width:15px;"></i>
                                {{ __('Update Item') }}
                            </button>
                            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-4 py-2">
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
    .form-control-sm  { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-control-sm:focus  { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .form-select-sm  { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-select-sm:focus  { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
    .status-badge { display: inline-flex; padding: 5px 0; border-radius: 20px; font-size: 0.75rem; font-weight: 600; width: 100%; justify-content: center; }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.available { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.available .status-dot { background: #22c55e; }
    .status-badge.unavailable { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge.unavailable .status-dot { background: #ef4444; }
</style>

<script>
    document.getElementById('imageInput').onchange = function () {
        const [file] = this.files;
        if (file) {
            const preview = document.getElementById('imagePreview');
            const overlay = document.getElementById('placeholderOverlay');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            if (overlay) overlay.classList.add('d-none');
        }
    };

    document.getElementById('statusSwitch').onchange = function () {
        const badge  = document.getElementById('statusBadge');
        const hidden = document.getElementById('statusHidden');
        if (this.checked) {
            badge.className = 'status-badge available d-flex align-items-center justify-content-center gap-2 py-2';
            badge.innerHTML = '<span class="status-dot"></span> {{ __("Available") }}';
            hidden.disabled = true;
        } else {
            badge.className = 'status-badge unavailable d-flex align-items-center justify-content-center gap-2 py-2';
            badge.innerHTML = '<span class="status-dot"></span> {{ __("Unavailable") }}';
            hidden.disabled = false;
        }
    };
</script>
@endsection
