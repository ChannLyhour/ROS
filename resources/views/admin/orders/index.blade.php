@extends('layouts.app')

@section('title', 'Order History')

@section('content')
<x-master-table
    title="{{ __('Order Management') }}"
    subtitle="{{ __('Track and manage live customer orders and sales') }}"
    :createRoute="route('orders.create')"
    createPermission="create-orders"
    createLabel="{{ __('New Order') }}"
    searchPlaceholder="{{ __('Search by Order ID...') }}"
    :headers="[
        ['text' => '#', 'align' => 'center'],
        ['text' => __('Order Details'), 'align' => 'ps-4'],
        ['text' => __('Type'), 'align' => 'center'],
        ['text' => __('Amount'), 'align' => 'center'],
        ['text' => __('Status'), 'align' => 'start'],
        ['text' => __('Date'), 'align' => 'center'],
        ['text' => __('Created By'), 'align' => 'center'],
        ['text' => __('Actions'), 'align' => 'end']
    ]"
    :items="$orders">
    <!-- Filter Slot -->
    <x-slot name="filters">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 m-0 align-items-center">
            <select name="type" class="select2" onchange="this.form.submit()">
                <option value="">All types</option>
                <option value="dine_in" {{ request('type') == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                <option value="takeaway" {{ request('type') == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
            </select>
            <select name="status" class="select2" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @if(request()->anyFilled(['search', 'type', 'status']))
            <a href="{{ route('orders.index') }}" class="btn btn-action reset" title="Clear Filters" style="width: 48px; height: 48px;">
                <i data-lucide="rotate-ccw" style="width: 20px;"></i>
            </a>
            @endif
        </form>
    </x-slot>

    @forelse($orders as $order)
    <tr>
        <td class="text-center">
            <span class="text-muted fw-bold">{{ $loop->iteration }}</span>
        </td>
        <td class="ps-4">
            <div class="d-flex align-items-center gap-3">
                <div class="order-icon-box bg-light text-primary">
                    <i data-lucide="hash" style="width: 18px;"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">{{ $order->order_no }}</div>
                    <small class="text-muted">Customer: {{ $order->customer->name ?? 'Guest' }}</small>
                </div>
            </div>
        </td>
        <td class="text-center">
            @php
            $typeIcons = [
            'dine_in' => ['icon' => 'utensils', 'label' => 'Dine In'],
            'takeaway' => ['icon' => 'shopping-bag', 'label' => 'Takeaway'],
            'delivery' => ['icon' => 'truck', 'label' => 'Delivery'],
            ];
            $type = $typeIcons[$order->order_type] ?? ['icon' => 'package', 'label' => 'Other'];
            @endphp
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-light rounded-3 border">
                <i data-lucide="{{ $type['icon'] }}" class="text-muted" style="width: 14px;"></i>
                <span class="extra-small fw-bold text-uppercase">{{ $type['label'] }}</span>
            </div>
        </td>
        <td class="text-center">
            <div class="fw-bold text-primary">${{ number_format($order->total_amount, 2) }}</div>
            <small class="extra-small text-muted">{{ $order->items->sum('quantity') }} items</small>
        </td>
        <td class="text-start">
            @php
            $statusConfig = [
                'pending'   => ['class' => 'status-badge pending', 'icon' => 'clock'],
                'preparing' => ['class' => 'status-badge preparing', 'icon' => 'flame'],
                'ready'     => ['class' => 'status-badge ready', 'icon' => 'bell'],
                'completed' => ['class' => 'status-badge completed', 'icon' => 'check-circle'],
                'cancelled' => ['class' => 'status-badge cancelled', 'icon' => 'x-circle'],
            ];
            $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
            @endphp
            <span class="badge {{ $config['class'] }} px-2 rounded-3 d-inline-flex align-items-center gap-2">
                <i data-lucide="{{ $config['icon'] }}" style="width: 14px;"></i>
                {{ strtoupper($order->status) }}
            </span>
        </td>
        <td class="text-center">
            <div class="extra-small text-dark fw-bold">{{ $order->created_at->format('d M, Y') }}</div>
            <div class="extra-small text-muted">{{ $order->created_at->format('h:i A') }}</div>
        </td>
        <td class="text-center">
            <div class="fw-bold text-dark">{{ $order->createdBy->name ?? 'System' }}</div>
        </td>
        <td class="text-end pe-4">
            <x-table-actions 
                :editRoute="route('orders.edit', $order->id)" 
                editPermission="edit-orders"
                :viewRoute="route('orders.show', $order->id)" 
                viewPermission="view-orders"
                :deleteRoute="route('orders.destroy', $order->id)" 
                deletePermission="delete-orders"
                :id="$order->id" 
                :name="$order->order_no" 
            />
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center py-5">
            <i data-lucide="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
            <p class="text-muted">{{ __('No orders found.') }}</p>
        </td>
    </tr>
    @endforelse
</x-master-table>

<style>
    .order-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    /* Premium Status Badges */
    .status-badge {
        font-weight: 800;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
    }

    .status-badge.pending { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .status-badge.preparing { background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
    .status-badge.ready { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .status-badge.completed { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.cancelled { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
</style>
@endsection