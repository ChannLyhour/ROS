@extends('layouts.app')

@section('title', __('Edit Translation'))

@section('content')
<div class="p-1 p-md-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('Edit Translation') }}</h2>
            <p class="text-muted small mb-0">{{ __('Key') }}: <code class="text-primary">{{ $translation->key }}</code></p>
        </div>
        <a href="{{ route('translations.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
            <i data-lucide="arrow-left" style="width:15px;"></i>{{ __('Back') }}
        </a>
    </div>

    <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('translations.update', $translation->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-dark">{{ __('Group') }} <span class="text-danger">*</span></label>
                        <select name="group" class="form-select form-select-sm select2 @error('group') is-invalid @enderror">
                            <option value="general" {{ old('group',$translation->group) == 'general' ? 'selected' : '' }}>General</option>
                            <option value="pos"      {{ old('group',$translation->group) == 'pos'      ? 'selected' : '' }}>POS</option>
                            <option value="menu"     {{ old('group',$translation->group) == 'menu'     ? 'selected' : '' }}>Menu</option>
                            <option value="customer" {{ old('group',$translation->group) == 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                        @error('group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-dark">{{ __('Translation Key') }} <span class="text-danger">*</span></label>
                        <input type="text" name="key"
                               class="form-control form-control-sm @error('key') is-invalid @enderror"
                               value="{{ old('key', $translation->key) }}" required>
                        @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">
                            <img src="https://flagcdn.com/16x12/us.png" alt="EN" class="me-1">
                            {{ __('English Text') }}
                        </label>
                        <textarea name="en" rows="3"
                                  class="form-control form-control-sm @error('en') is-invalid @enderror">{{ old('en', $translation->en) }}</textarea>
                        @error('en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">
                            <img src="https://flagcdn.com/16x12/kh.png" alt="KH" class="me-1">
                            {{ __('Khmer Text') }} <span class="text-muted text-khmer">(ភាសាខ្មែរ)</span>
                        </label>
                        <textarea name="kh" rows="3"
                                  class="form-control form-control-sm text-khmer @error('kh') is-invalid @enderror">{{ old('kh', $translation->kh) }}</textarea>
                        @error('kh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2 pt-2 border-top">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2">
                                <i data-lucide="save" style="width:15px;"></i>
                                {{ __('Update Translation') }}
                            </button>
                            <a href="{{ route('translations.index') }}" class="btn btn-outline-secondary btn-sm px-4 py-2">
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
    .form-control-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .form-control-sm:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
    .form-select-sm { border-radius: 4px; border-color: #ced4da; font-size: 0.9rem; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
    .text-khmer { font-family: 'Kantumruy Pro', sans-serif; }
</style>
@endsection
