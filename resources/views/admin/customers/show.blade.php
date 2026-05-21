@extends('layouts.app')

@section('title', __('Customer Profile'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size: 1.25rem; color: #212529;">{{ __('Customer Profile') }}</h2>
            <p class="text-muted small mb-0">{{ __('Viewing details for') }} <strong>{{ $customer->name }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            @can('edit-customers')
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3">
                <i data-lucide="pencil" style="width:15px;"></i>
                {{ __('Edit') }}
            </a>
            @endcan
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3">
                <i data-lucide="arrow-left" style="width:15px;"></i>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card border h-100" style="border-color:#dee2e6 !important; border-radius:6px;">
                <div class="card-body p-4 text-center">
                    <img src="{{ $customer->display_image }}" alt="{{ $customer->name }}"
                        class="rounded-circle border mb-3"
                        style="width:100px; height:100px; object-fit:cover; border-color:#dee2e6 !important;">
                    <h5 class="fw-semibold text-dark mb-0">{{ $customer->name }}</h5>
                    <small class="text-muted">{{ __('Client') }} #{{ $customer->id }}</small>

                    <hr class="my-3" style="border-color:#f1f3f5;">

                    <div class="text-start">
                        @foreach([
                        ['mail', $customer->email, __('Email')],
                        ['phone', $customer->phone, __('Phone')],
                        ['map-pin', $customer->city, __('City')],
                        ['calendar', $customer->created_at->format('M d, Y'), __('Joined')],
                        ] as [$icon, $value, $label])
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="info-icon">
                                <i data-lucide="{{ $icon }}" style="width:14px;height:14px;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; color:#6c757d; text-transform:uppercase; font-weight:600; letter-spacing:0.04em;">{{ $label }}</div>
                                <div class="small fw-semibold text-dark">{{ $value ?? '—' }}</div>
                            </div>
                        </div>
                        @endforeach

                        @if($customer->address)
                        <div class="d-flex align-items-start gap-3">
                            <div class="info-icon">
                                <i data-lucide="home" style="width:14px;height:14px;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; color:#6c757d; text-transform:uppercase; font-weight:600; letter-spacing:0.04em;">{{ __('Address') }}</div>
                                <div class="small text-dark">{{ $customer->address }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="card border text-center py-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                        <div class="fw-bold text-dark" style="font-size:1.5rem;">{{ $customer->orders->count() }}</div>
                        <div style="font-size:0.75rem; color:#6c757d; text-transform:uppercase; font-weight:600;">{{ __('Total Orders') }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border text-center py-3" style="border-color:#dee2e6 !important; border-radius:6px;">
                        <div class="fw-bold text-dark" style="font-size:1.5rem;">${{ number_format($customer->orders->sum('total_amount'), 2) }}</div>
                        <div style="font-size:0.75rem; color:#6c757d; text-transform:uppercase; font-weight:600;">{{ __('Total Spent') }}</div>
                    </div>
                </div>
            </div>

            <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4"
                    style="border-color:#dee2e6 !important;">
                    <span class="fw-semibold small text-dark">{{ __('Recent Orders') }}</span>
                    <i data-lucide="receipt" class="text-muted" style="width:16px;"></i>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th class="px-4 py-3 border-0 text-muted" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Order No') }}</th>
                                <th class="px-4 py-3 border-0 text-muted" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Date') }}</th>
                                <th class="px-4 py-3 border-0 text-muted text-center" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Type') }}</th>
                                <th class="px-4 py-3 border-0 text-muted text-end" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Amount') }}</th>
                                <th class="px-4 py-3 border-0 text-muted text-center" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders()->latest()->take(5)->get() as $order)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-primary">#{{ $order->order_no }}</td>
                                <td class="px-4 py-3 text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge bg-light text-dark border" style="font-size:0.72rem;">{{ $order->order_type }}</span>
                                </td>
                                <td class="px-4 py-3 text-end fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                    $cls = match($order->status) {
                                    'completed' => 'bg-success-subtle text-success',
                                    'pending' => 'bg-warning-subtle text-warning',
                                    'cooking' => 'bg-primary-subtle text-primary',
                                    'cancelled' => 'bg-danger-subtle text-danger',
                                    default => 'bg-secondary-subtle text-secondary'
                                    };
                                    @endphp
                                    <span class="badge {{ $cls }}" style="font-size:0.72rem; text-transform:uppercase;">{{ $order->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">{{ __('No orders yet.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8fafc !important;
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        color: #fff;
    }

    .btn-outline-secondary {
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .info-icon {
        width: 28px;
        height: 28px;
        border-radius: 4px;
        background: #f1f3f5;
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        flex-shrink: 0;
    }
</style>
@endsection