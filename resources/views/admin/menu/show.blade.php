@extends('layouts.app')

@section('title', __('Menu Item Details'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size: 1.25rem; color: #212529;">{{ __('Menu Item Details') }}</h2>
            <p class="text-muted small mb-0">{{ __('Viewing') }}: <strong>{{ $menuItem->name }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('menu.edit', $menuItem->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3">
                <i data-lucide="pencil" style="width:15px;"></i>
                <span class="d-none d-sm-inline">{{ __('Edit Item') }}</span>
            </a>
            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
                <i data-lucide="arrow-left" style="width:15px;"></i>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Card -->
    <div class="card border" style="border-color: #dee2e6 !important; border-radius: 6px;">
        <div class="card-body p-3 p-md-4">
            <div class="row g-4">
                <!-- Left: Image + Stats -->
                <div class="col-lg-4">
                    <img src="{{ $menuItem->display_image }}" alt="{{ $menuItem->name }}"
                         class="w-100 rounded border mb-3"
                         style="object-fit:cover; height:260px; border-color:#dee2e6 !important;">

                    <div class="border rounded p-3" style="border-color: #dee2e6 !important; border-radius: 6px;">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom mb-2">
                            <span class="small text-muted fw-semibold">{{ __('Status') }}</span>
                            @if($menuItem->status == 'available')
                            <span class="status-badge available"><span class="status-dot"></span> {{ __('Available') }}</span>
                            @else
                            <span class="status-badge unavailable"><span class="status-dot"></span> {{ __('Unavailable') }}</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="small text-muted fw-semibold">{{ __('Price') }}</span>
                            <span class="fw-bold text-dark" style="font-size:1.2rem;">
                                {{ $appSettings['currency'] }}{{ number_format($menuItem->price, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="col-lg-8">
                    <p class="text-muted extra-small text-uppercase fw-semibold mb-1 tracking">{{ __('Item Information') }}</p>
                    <h3 class="fw-bold text-dark mb-4" style="font-size: 1.5rem;">{{ $menuItem->name }}</h3>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small text-muted">{{ __('Category') }}</label>
                            <div class="border rounded px-3 py-2 d-flex align-items-center gap-2 bg-light"
                                 style="border-color:#dee2e6 !important; border-radius:4px;">
                                <i data-lucide="tag" class="text-secondary" style="width:15px;"></i>
                                <span class="fw-semibold small text-dark">{{ $menuItem->category->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-muted">{{ __('Item Reference') }}</label>
                            <div class="border rounded px-3 py-2 bg-light"
                                 style="border-color:#dee2e6 !important; border-radius:4px;">
                                <span class="fw-bold text-secondary"># {{ $menuItem->id }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">{{ __('Description') }}</label>
                        <div class="border rounded p-3 bg-light small text-dark"
                             style="border-color:#dee2e6 !important; border-radius:4px; min-height:80px;">
                            {{ $menuItem->description ?: __('No description available.') }}
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="metric-card created">
                                <div class="extra-small text-muted tracking text-uppercase fw-semibold mb-1">{{ __('Created') }}</div>
                                <div class="fw-semibold small text-dark">{{ $menuItem->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="metric-card updated">
                                <div class="extra-small text-muted tracking text-uppercase fw-semibold mb-1">{{ __('Last Updated') }}</div>
                                <div class="fw-semibold small text-dark">{{ $menuItem->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="metric-card status">
                                <div class="extra-small text-muted tracking text-uppercase fw-semibold mb-1">{{ __('Category Status') }}</div>
                                <div class="fw-semibold small text-success d-flex align-items-center gap-1">
                                    <i data-lucide="check" style="width:13px;"></i> {{ __('Active') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .extra-small { font-size: 0.72rem; }
    .tracking { letter-spacing: 0.05em; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; color: #fff; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600;
    }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.available   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.available .status-dot   { background: #22c55e; }
    .status-badge.unavailable { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge.unavailable .status-dot { background: #ef4444; }

    .metric-card {
        padding: 12px 16px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        background: #fff;
        height: 100%;
    }
</style>
@endsection