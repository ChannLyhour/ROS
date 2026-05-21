@extends('layouts.app')

@section('title', __('Kitchen Display'))

@section('content')

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <div id="countdown" class="badge bg-white border text-primary px-3 py-2 rounded-lg shadow-sm fw-bold">
            {{ __('Refreshing in 2mn') }}
        </div>
        <button onclick="window.location.reload()" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
            <i data-lucide="rotate-ccw" style="width: 16px;"></i>
            {{ __('Refresh') }}
        </button>
    </div>
</div>

<x-master-table 
    title="{{ __('Kitchen Display System') }}" 
    subtitle="{{ __('Live Order Preparation Queue') }}"
    searchPlaceholder="{{ __('Search orders...') }}"
    :headers="[
        ['text' => __('Order No'), 'align' => 'start'],
        ['text' => __('Type'), 'align' => 'start'],
        ['text' => __('Time'), 'align' => 'start'],
        ['text' => __('Items'), 'align' => 'start'],
        ['text' => __('Notes'), 'align' => 'start'],
        ['text' => __('Status'), 'align' => 'center'],
        ['text' => __('Actions'), 'align' => 'end']
    ]"
    :items="$orders">
    
    <x-slot name="filters">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('kitchen.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ __('All Active') }} ({{ $counts['all'] }})
            </a>
            <a href="{{ route('kitchen.index', ['status' => 'new']) }}" class="btn btn-sm {{ $status === 'new' ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ __('New') }} ({{ $counts['new'] }})
            </a>
            <a href="{{ route('kitchen.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ __('Pending') }} ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('kitchen.index', ['status' => 'preparing']) }}" class="btn btn-sm {{ $status === 'preparing' ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ __('Preparing') }} ({{ $counts['preparing'] }})
            </a>
            <a href="{{ route('kitchen.index', ['status' => 'ready']) }}" class="btn btn-sm {{ $status === 'ready' ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ __('Ready') }} ({{ $counts['ready'] }})
            </a>
            <a href="{{ route('kitchen.index', ['status' => 'late']) }}" class="btn btn-sm {{ $status === 'late' ? 'btn-danger' : 'btn-outline-danger' }}">
                {{ __('Delayed') }} ({{ $counts['late'] }})
            </a>
        </div>
    </x-slot>

    @forelse($orders as $order)
    <tr>
        <td class="fw-bold text-primary">#{{ $order->order_no }}</td>
        <td>
            <span class="badge bg-light text-dark border">
                {{ $order->diningTable->name ?? __('Takeaway') }}
            </span>
        </td>
        <td class="text-muted small">
            {{ $order->created_at->diffForHumans() }}
        </td>
        <td>
            <ul class="list-unstyled mb-0 small">
                @foreach($order->items as $item)
                <li>
                    <span class="fw-bold">{{ $item->quantity }}x</span> {{ $item->menuItem->name ?? '---' }}
                </li>
                @endforeach
            </ul>
        </td>
        <td style="max-width: 200px;">
            @if($order->notes)
            <div class="text-truncate small text-warning-emphasis fw-bold" title="{{ $order->notes }}">
                <i data-lucide="info" style="width: 14px;"></i> {{ $order->notes }}
            </div>
            @else
            <span class="text-muted small">--</span>
            @endif
        </td>
        <td class="text-center">
            @php
                $statusClass = match($order->status) {
                    'pending' => 'bg-warning text-dark',
                    'preparing' => 'bg-primary text-white',
                    'ready' => 'bg-success text-white',
                    default => 'bg-secondary text-white'
                };
            @endphp
            <span class="badge {{ $statusClass }} text-uppercase">
                {{ $order->status }}
            </span>
        </td>
        <td class="text-end pe-4">
            <div class="d-flex gap-2 justify-content-end align-items-center">
                <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#noteModal{{ $order->id }}" title="{{ __('Edit Note') }}">
                    <i data-lucide="message-square" style="width: 14px;"></i>
                </button>

                @if($order->status == 'pending')
                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="m-0">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="preparing">
                    <button type="submit" class="btn btn-sm btn-primary fw-bold text-uppercase" style="font-size: 0.75rem;">
                        {{ __('Start') }}
                    </button>
                </form>
                @elseif($order->status == 'preparing')
                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="m-0">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ready">
                    <button type="submit" class="btn btn-sm btn-success fw-bold text-uppercase" style="font-size: 0.75rem;">
                        {{ __('Ready') }}
                    </button>
                </form>
                @else
                <span class="text-success small fw-bold">
                    <i data-lucide="check" style="width: 14px;"></i> {{ __('Waiting') }}
                </span>
                @endif
            </div>
        </td>
    </tr>

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <form action="{{ route('kitchen.update-note', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">{{ __('Order') }} #{{ $order->order_no }} {{ __('Note') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label small fw-bold text-muted">{{ __('Kitchen Instructions / Customer Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="{{ __('Type instructions here...') }}">{{ $order->notes }}</textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Note') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <tr>
        <td colspan="7" class="text-center py-5">
            <div class="text-muted mb-2">
                <i data-lucide="check-circle" style="width: 48px; height: 48px; opacity: 0.5;"></i>
            </div>
            <h5 class="fw-bold mb-1">{{ __('Kitchen Clear!') }}</h5>
            <p class="small mb-0">{{ __('No active orders currently pending.') }}</p>
        </td>
    </tr>
    @endforelse
</x-master-table>

@push('js')
<script>
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