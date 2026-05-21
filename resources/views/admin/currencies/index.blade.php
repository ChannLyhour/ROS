@extends('layouts.app')

@section('title', __('Currencies'))

@section('content')
<x-master-table
    title="{{ __('Currency Management') }}"
    subtitle="{{ __('Configure financial symbols and operational status for your platform') }}"
    :createRoute="route('currencies.create')"
    createLabel="{{ __('Add Currency') }}"
    searchPlaceholder="{{ __('Search by name or symbol...') }}"
    :headers="[__('#'), __('Name'), __('Symbol'), __('Status'), __('Actions')]"
    :items="$currencies">

    @forelse($currencies as $currency)
    <tr>
        <td class="text-center" style="width:50px;">
            <span class="row-num">{{ ($currencies->currentPage() - 1) * $currencies->perPage() + $loop->iteration }}</span>
        </td>
        <td class="ps-3">
            <div class="fw-semibold text-dark">{{ $currency->name }}</div>
            <small class="text-muted" style="font-size:0.78rem;">{{ __('Financial Token') }}</small>
        </td>
        <td class="text-center">
            <span class="symbol-badge">{{ $currency->symbol }}</span>
        </td>
        <td class="text-center">
            @if($currency->is_active)
            <span class="status-badge active">
                <span class="status-dot"></span> {{ __('Active') }}
            </span>
            @else
            <span class="status-badge inactive">
                <span class="status-dot"></span> {{ __('Inactive') }}
            </span>
            @endif
        </td>
        <td class="text-end pe-4" style="width:100px;">
            <div class="d-flex justify-content-end gap-1">
                <a href="{{ route('currencies.edit', $currency->id) }}" class="action-btn edit-btn" title="{{ __('Edit') }}">
                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                </a>
                <button type="button" class="action-btn delete-btn" title="{{ __('Delete') }}"
                        onclick="confirmDelete('delete-form-{{ $currency->id }}', '{{ $currency->name }}')">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                </button>
                <form id="delete-form-{{ $currency->id }}" action="{{ route('currencies.destroy', $currency->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="py-5">
            <div class="text-center">
                <i data-lucide="coins" style="width:48px;height:48px;color:#ced4da;"></i>
                <p class="text-muted mt-3 fw-semibold mb-1">{{ __('No currencies found') }}</p>
                <small class="text-muted">{{ __('Add your first currency to get started.') }}</small>
            </div>
        </td>
    </tr>
    @endforelse
</x-master-table>

<style>
    body { background-color: #f8fafc !important; }

    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px;
        background: #f1f3f5; border-radius: 50%;
        font-size: 0.75rem; font-weight: 600; color: #6c757d;
    }

    .symbol-badge {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 36px; padding: 4px 12px;
        background: #e9ecef; border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 1rem; font-weight: 700; color: #212529;
    }

    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;
    }
    .status-badge .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.active  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.active .status-dot  { background: #22c55e; }
    .status-badge.inactive { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-badge.inactive .status-dot { background: #ef4444; }

    .action-btn {
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid #e9ecef; background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .action-btn.edit-btn  { color: #3b82f6; }
    .action-btn.edit-btn:hover  { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .action-btn.delete-btn { color: #ef4444; }
    .action-btn.delete-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
</style>
@endsection
