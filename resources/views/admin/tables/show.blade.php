@extends('layouts.app')

@section('title', __('Table Details'))

@section('content')
<div class="p-1 p-md-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('Table Details') }}</h2>
            <p class="text-muted small mb-0">{{ __('Viewing') }}: <strong>{{ $table->name }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tables.edit', $table->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3">
                <i data-lucide="pencil" style="width:15px;"></i>{{ __('Edit') }}
            </a>
            <a href="{{ route('tables.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
                <i data-lucide="arrow-left" style="width:15px;"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
        <div class="card-body p-3 p-md-4">
            <div class="row g-4">
                <!-- Left: Visual -->
                <div class="col-lg-4 text-center">
                    <div class="border rounded p-4 d-flex flex-column align-items-center justify-content-center"
                         style="min-height:200px; border-color:#dee2e6 !important; border-radius:6px; background:#f8f9fa;">
                        <div class="table-icon-lg mb-3">
                            <i data-lucide="layout-dashboard" style="width:32px;height:32px;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ $table->name }}</h4>
                        <small class="text-muted">{{ __('Table #') }}{{ $table->id }}</small>
                        <div class="mt-3">
                            @php
                            $s = match($table->status) {
                                'occupied'  => ['occupied',  __('Occupied')],
                                'reserved'  => ['reserved',  __('Reserved')],
                                default     => ['available', __('Available')],
                            };
                            @endphp
                            <span class="status-badge {{ $s[0] }}">
                                <span class="status-dot"></span> {{ $s[1] }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="col-lg-8">
                    <p class="extra-small text-muted text-uppercase fw-semibold tracking mb-3">{{ __('Table Information') }}</p>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="border rounded p-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                                <div class="extra-small text-muted text-uppercase fw-semibold tracking mb-1">{{ __('Capacity') }}</div>
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="users" class="text-primary" style="width:16px;"></i>
                                    <span class="fw-bold text-dark fs-5">{{ $table->capacity }}</span>
                                    <span class="text-muted small">{{ __('Guests') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                                <div class="extra-small text-muted text-uppercase fw-semibold tracking mb-1">{{ __('Created') }}</div>
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="calendar" class="text-primary" style="width:16px;"></i>
                                    <span class="fw-semibold text-dark small">{{ $table->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2 px-4 py-2"
                                onclick="confirmDelete('delete-form-{{ $table->id }}', '{{ $table->name }}')">
                            <i data-lucide="trash-2" style="width:14px;"></i>
                            {{ __('Delete Table') }}
                        </button>
                        <form id="delete-form-{{ $table->id }}" action="{{ route('tables.destroy', $table->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
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
    .table-icon-lg {
        width: 64px; height: 64px; border-radius: 12px;
        background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe;
        display: flex; align-items: center; justify-content: center;
    }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600;
    }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.available { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.available .status-dot { background: #22c55e; }
    .status-badge.occupied  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge.occupied .status-dot  { background: #ef4444; }
    .status-badge.reserved  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-badge.reserved .status-dot  { background: #f59e0b; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; color: #fff; }
    .btn-outline-secondary { border-radius: 4px; font-size: 0.875rem; }
    .btn-outline-danger { border-radius: 4px; font-size: 0.875rem; }
</style>
@endsection
