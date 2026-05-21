@extends('layouts.app')

@section('title', __('Categories'))

@section('content')

{{-- Stats Summary Bar --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle text-primary">
                <i data-lucide="layers" style="width:18px;height:18px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $categories->total() }}</div>
                <div class="stat-label">{{ __('Total Categories') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle text-success">
                <i data-lucide="check-circle" style="width:18px;height:18px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $categories->where('status', 1)->count() }}</div>
                <div class="stat-label">{{ __('Active') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-secondary-subtle text-secondary">
                <i data-lucide="eye-off" style="width:18px;height:18px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $categories->where('status', 0)->count() }}</div>
                <div class="stat-label">{{ __('Disabled') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle text-warning">
                <i data-lucide="package" style="width:18px;height:18px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ \App\Models\MenuItem::count() }}</div>
                <div class="stat-label">{{ __('Total Items') }}</div>
            </div>
        </div>
    </div>
</div>

<x-master-table
    title="{{ __('Category Management') }}"
    subtitle="{{ __('Organize your menu items into structured groups') }}"
    :createRoute="route('categories.create')"
    createPermission="create-menu"
    createLabel="{{ __('Add Category') }}"
    searchPlaceholder="{{ __('Search category name...') }}"
    :headers="[__('#'), __('Category'), __('Items'), __('Status'), __('Actions')]"
    :items="$categories">

    @forelse($categories as $category)
    @php $count = \App\Models\MenuItem::where('category_id', $category->id)->count(); @endphp
    <tr class="cat-row">
        {{-- # --}}
        <td class="text-center" style="width: 50px;">
            <span class="row-num">{{ $loop->iteration }}</span>
        </td>

        {{-- Category Name + Description --}}
        <td class="ps-3" style="min-width: 240px;">
            <div class="d-flex align-items-center gap-3">
                <div class="cat-icon-box">
                    <i data-lucide="tag" style="width:15px;height:15px;"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="fw-semibold text-dark lh-1 mb-1">{{ $category->name }}</div>
                    <div class="text-muted small text-truncate" style="max-width: 260px; font-size: 0.78rem;">
                        {{ $category->description ?? __('No description') }}
                    </div>
                </div>
            </div>
        </td>

        {{-- Item Count --}}
        <td class="text-center">
            <span class="item-count-badge {{ $count > 0 ? 'has-items' : 'no-items' }}">
                <i data-lucide="package" style="width:12px;height:12px;"></i>
                {{ $count }}
            </span>
        </td>

        {{-- Status --}}
        <td class="text-center">
            @if($category->status)
            <span class="status-badge active">
                <span class="status-dot"></span>
                {{ __('Active') }}
            </span>
            @else
            <span class="status-badge disabled">
                <span class="status-dot"></span>
                {{ __('Disabled') }}
            </span>
            @endif
        </td>

        {{-- Actions --}}
        <td class="text-end pe-4" style="width: 120px;">
            <div class="d-flex justify-content-end gap-1">
                @can('edit-menu')
                <a href="{{ route('categories.edit', $category->id) }}"
                   class="action-btn edit-btn"
                   title="{{ __('Edit Category') }}">
                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                </a>
                @endcan
                @can('delete-menu')
                <button type="button"
                        class="action-btn delete-btn"
                        title="{{ __('Delete Category') }}"
                        onclick="confirmDelete('delete-form-{{ $category->id }}', '{{ $category->name }}')">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                </button>
                @endcan
                <form id="delete-form-{{ $category->id }}" action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-none">
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
                <div class="mb-3">
                    <i data-lucide="folder-open" style="width:48px;height:48px;color:#ced4da;"></i>
                </div>
                <p class="text-muted mb-2 fw-semibold">{{ __('No categories found') }}</p>
                <small class="text-muted">{{ __('Create your first category to start organizing the menu.') }}</small>
            </div>
        </td>
    </tr>
    @endforelse
</x-master-table>

<style>
    body { background-color: #f8fafc !important; }

    /* ── Stat Cards ─────────────────────────────── */
    .stat-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: box-shadow 0.2s;
    }
    .stat-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
    .stat-icon {
        width: 42px; height: 42px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-value { font-size: 1.35rem; font-weight: 700; color: #212529; line-height: 1; }
    .stat-label { font-size: 0.72rem; color: #6c757d; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.04em; }

    /* ── Table Row ───────────────────────────────── */
    .cat-row { border-bottom: 1px solid #f1f3f5; transition: background 0.15s; }
    .cat-row:hover { background-color: #f8f9fa !important; }
    .cat-row td { padding-top: 14px; padding-bottom: 14px; vertical-align: middle; }

    /* Row number */
    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px;
        background: #f1f3f5;
        border-radius: 50%;
        font-size: 0.75rem; font-weight: 600; color: #6c757d;
    }

    /* Category icon box */
    .cat-icon-box {
        width: 36px; height: 36px;
        border-radius: 8px;
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #e0e7ff;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* Item Count badge */
    .item-count-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .item-count-badge.has-items {
        background: #e0f2fe; color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .item-count-badge.no-items {
        background: #f8fafc; color: #94a3b8;
        border: 1px solid #e2e8f0;
    }

    /* Status badge */
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .status-badge .status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
    }
    .status-badge.active { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-badge.active .status-dot { background: #22c55e; }
    .status-badge.disabled { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .status-badge.disabled .status-dot { background: #94a3b8; }

    /* Action Buttons */
    .action-btn {
        width: 32px; height: 32px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.18s;
        text-decoration: none;
    }
    .action-btn.edit-btn { color: #3b82f6; }
    .action-btn.edit-btn:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .action-btn.delete-btn { color: #ef4444; }
    .action-btn.delete-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
</style>

@endsection