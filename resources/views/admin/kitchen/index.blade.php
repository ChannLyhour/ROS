@extends('layouts.app')

@section('title', __('Kitchen Display'))

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-black mb-0 text-dark">{{ __('Kitchen Display System') }}</h2>
        <p class="text-muted small fw-bold mb-0 text-uppercase tracking-wider">{{ __('Live Order Preparation Queue') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div id="countdown" class="badge bg-white border text-primary px-3 py-2 rounded-lg shadow-sm fw-bold me-2">
            {{ __('Refreshing in 2mn') }}
        </div>
        <button onclick="window.location.reload()" class="btn btn-white border shadow-sm rounded-lg p-2" title="{{ __('Manual Refresh') }}">
            <i data-lucide="rotate-ccw" style="width: 20px;"></i>
        </button>
    </div>
</div>

<!-- Status Tabs -->
<div class="kitchen-tabs-scroll-container mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('kitchen.index', ['status' => 'all']) }}" class="btn kitchen-tab {{ $status === 'all' ? 'active' : '' }}">
            <i data-lucide="layout-grid" class="me-2"></i>
            <span>{{ __('ALL ACTIVE') }}</span>
            <span class="badge {{ $status === 'all' ? 'bg-white text-dark' : 'bg-dark' }} ms-2">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('kitchen.index', ['status' => 'new']) }}" class="btn kitchen-tab {{ $status === 'new' ? 'active' : '' }}">
            <i data-lucide="sparkles" class="me-2"></i>
            <span>{{ __('NEW (0-15M)') }}</span>
            <span class="badge {{ $status === 'new' ? 'bg-white text-dark' : 'bg-dark' }} ms-2">{{ $counts['new'] }}</span>
        </a>
        <a href="{{ route('kitchen.index', ['status' => 'pending']) }}" class="btn kitchen-tab {{ $status === 'pending' ? 'active' : '' }}">
            <i data-lucide="clock" class="me-2"></i>
            <span>{{ __('PENDING') }}</span>
            <span class="badge {{ $status === 'pending' ? 'bg-white text-dark' : 'bg-dark' }} ms-2">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('kitchen.index', ['status' => 'preparing']) }}" class="btn kitchen-tab {{ $status === 'preparing' ? 'active' : '' }}">
            <i data-lucide="flame" class="me-2"></i>
            <span>{{ __('PREPARING') }}</span>
            <span class="badge {{ $status === 'preparing' ? 'bg-white text-dark' : 'bg-dark' }} ms-2">{{ $counts['preparing'] }}</span>
        </a>
        <a href="{{ route('kitchen.index', ['status' => 'ready']) }}" class="btn kitchen-tab {{ $status === 'ready' ? 'active' : '' }}">
            <i data-lucide="bell" class="me-2"></i>
            <span>{{ __('READY') }}</span>
            <span class="badge {{ $status === 'ready' ? 'bg-white text-dark' : 'bg-dark' }} ms-2">{{ $counts['ready'] }}</span>
        </a>
        <a href="{{ route('kitchen.index', ['status' => 'late']) }}" class="btn kitchen-tab tab-late {{ $status === 'late' ? 'active' : '' }}">
            <i data-lucide="alert-triangle" class="me-2"></i>
            <span>{{ __('DELAYED (30M-1H)') }}</span>
            <span class="badge {{ $status === 'late' ? 'bg-white text-dark' : 'bg-danger' }} ms-2">{{ $counts['late'] }}</span>
        </a>
    </div>
</div>

@if($orders->isEmpty())
<div class="empty-state-wrapper py-5">
    <div class="card border-0 shadow-sm rounded-lg p-5 text-center bg-white mx-auto" style="max-width: 600px;">
        <div class="empty-icon-container mb-4">
            <div class="bg-success bg-opacity-10 d-inline-flex p-4 rounded-circle">
                <i data-lucide="check-circle-2" class="text-success" style="width: 64px; height: 64px;"></i>
            </div>
        </div>
        <h3 class="fw-black text-dark">{{ __('Kitchen Clear!') }}</h3>
        <p class="text-muted mb-0">{{ __('No active orders currently pending. Great job!') }}</p>
    </div>
</div>
@else
<div class="row g-4" id="orderGrid">
    @foreach($orders as $order)
    <div class="col-12 col-md-6 col-lg-4 col-xl-3 animate__animated animate__fadeInUp">
        <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden order-card {{ $order->status }}">
            <!-- Card Header -->
            <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="h5 fw-black mb-0 text-white">
                        #{{ $order->order_no }}
                        @if($order->payment)
                            <span class="badge bg-success ms-1 small" style="font-size: 0.55rem; vertical-align: middle;">{{ __('PAID') }}</span>
                        @endif
                    </div>
                    <small class="text-white text-opacity-75 fw-bold text-uppercase" style="font-size: 0.65rem;">
                        {{ $order->diningTable->name ?? __('Takeaway') }} • {{ $order->created_at->diffForHumans() }}
                    </small>
                </div>
                @php
                $statusIcons = [
                'pending' => 'clock',
                'preparing' => 'flame',
                'ready' => 'bell'
                ];
                @endphp
                <div class="bg-white text-dark p-2 rounded-circle shadow-sm">
                    <i data-lucide="{{ $statusIcons[$order->status] ?? 'package' }}" style="width: 18px;"></i>
                </div>
            </div>

            <!-- Order Note (Top Callout) -->
            @if($order->notes)
            <div class="note-callout p-3 animate__animated animate__pulse animate__infinite">
                <div class="d-flex align-items-start gap-2">
                    <i data-lucide="info" class="text-warning-emphasis" style="width: 20px;"></i>
                    <div>
                        <div class="fw-black text-dark text-uppercase extra-small mb-1">{{ __('Customer Request:') }}</div>
                        <div class="fw-bold text-dark h6 mb-0 small">{{ $order->notes }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Items List -->
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($order->items as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="quantity-badge">
                                {{ $item->quantity }} x
                            </div>
                            <span class="fw-bold fs-6 text-dark">{{ $item->menuItem->name ?? '---' }}</span>
                        </div>
                        @if($order->status == 'pending')
                        <i data-lucide="dot" class="text-danger animate__animated animate__flash animate__infinite"></i>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Card Footer Actions -->
            <div class="card-footer bg-white border-top-0 p-3">
                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-grow-1 border-dashed" data-bs-toggle="modal" data-bs-target="#noteModal{{ $order->id }}">
                        <i data-lucide="message-square" style="width: 14px;" class="me-1"></i>
                        {{ $order->notes ? __('Edit Note') : __('Add Note') }}
                    </button>
                </div>

                @if($order->status == 'pending')
                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="preparing">
                    <button type="submit" class="btn btn-orange w-100 py-2 fw-black rounded-lg shadow-sm text-uppercase" style="font-size: 0.75rem;">
                        {{ __('Start Cooking') }}
                    </button>
                </form>
                @elseif($order->status == 'preparing')
                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ready">
                    <button type="submit" class="btn btn-success w-100 py-2 fw-black rounded-lg shadow-sm text-uppercase" style="font-size: 0.75rem;">
                        {{ __('Order Ready') }}
                    </button>
                </form>
                @else
                <div class="text-center py-2 text-success fw-black text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">
                    <i data-lucide="check-circle" class="me-1"></i> {{ __('Waiting for Pickup') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-lg">
                <form action="{{ route('kitchen.update-note', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="fw-black mb-0">{{ __('Order') }} #{{ $order->order_no }} {{ __('Note') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="extra-small fw-black text-muted text-uppercase mb-2 d-block">{{ __('Kitchen Instructions / Customer Notes') }}</label>
                        <textarea name="notes" class="form-control premium-field" rows="4" placeholder="{{ __('Type instructions here...') }}">{{ $order->notes }}</textarea>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-lg px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-orange rounded-lg px-4">{{ __('Save Note') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif



<style>
    .kitchen-kds-container {
        font-family: 'Kantumruy Pro', sans-serif;
        background: #f1f5f9;
        min-height: 100vh;
    }

    /* Navigation Tabs */
    .kitchen-tabs-scroll-container {
        overflow-x: auto;
        padding-bottom: 5px;
    }

    .kitchen-tabs-scroll-container::-webkit-scrollbar {
        height: 4px;
    }

    .kitchen-tabs-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .kitchen-tab {
        padding: 0.75rem 1.5rem;
        border: 2px solid transparent;
        background: white;
        color: #64748b;
        font-weight: 800;
        border-radius: 14px;
        transition: 0.3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    .kitchen-tab:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .kitchen-tab.active {
        background: #1e293b;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .kitchen-tab.tab-late:not(.active) {
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.2);
    }

    .kitchen-tab.tab-late.active {
        background: #ef4444;
    }

    .kitchen-tab .badge {
        font-family: sans-serif;
    }

    .fw-black {
        font-weight: 900;
    }

    .rounded-lg {
        border-radius: 16px !important;
    }

    /* Premium Shadows & Transitions */
    .order-card {
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.15) !important;
    }

    /* Status-Specific Header Gradients */
    .order-card.pending .card-header {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
    }

    .order-card.preparing .card-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .order-card.ready .card-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    /* The 'Sticky Note' Gourmet Style */
    .note-callout {
        background: #fefce8;
        border-left: 4px solid #facc15;
        position: relative;
        overflow: hidden;
    }

    .note-callout::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 20px 20px 0;
        border-color: transparent #fef08a transparent transparent;
        opacity: 0.5;
    }

    /* List Item Refinements */
    .list-group-item {
        padding: 1rem 1.25rem !important;
        background: transparent !important;
        border-bottom: 1px dashed #e2e8f0 !important;
    }

    .quantity-badge {
        width: 32px;
        height: 32px;
        background: #1e293b;
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-orange {
        background: #f08913;
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(240, 137, 19, 0.2);
    }

    .btn-orange:hover {
        background: #d87b11;
        color: white;
        transform: translateY(-1px);
    }

    .btn-success {
        background: #10b981;
        border: none;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
    }

    .premium-field {
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 12px 18px !important;
        font-weight: 500;
        transition: all 0.3s;
    }

    .premium-field:focus {
        border-color: #f08913 !important;
        background: white !important;
        box-shadow: 0 0 0 4px rgba(240, 137, 19, 0.1) !important;
    }

    .tracking-wider {
        letter-spacing: 0.08em;
    }
</style>

@push('js')
<script>
    // Live Countdown/Refresh
    let timeLeft = 120;
    const countdownEl = document.getElementById('countdown');

    setInterval(() => {
        if (timeLeft <= 0) {
            window.location.reload();
        } else {
            timeLeft--;
            let mins = Math.floor(timeLeft / 60);
            let secs = timeLeft % 60;
            let unit = mins > 0 ? "{{ __('m') }}" : "{{ __('s') }}";
            let display = mins > 0 ? `${mins}${unit} ${secs}{{ __('s') }}` : `${secs}{{ __('s') }}`;
            if (countdownEl) countdownEl.innerText = `{{ __('Refreshing in') }} ${display}`;
        }
    }, 1000);
</script>
@endpush
@endsection