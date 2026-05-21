@extends('layouts.app')

@section('title', __('System Backups'))

@section('content')
<div class="p-1 p-md-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-semibold mb-0" style="font-size:1.25rem; color:#212529;">{{ __('System Backups') }}</h2>
            <p class="text-muted small mb-0">{{ __('Manage and download database backups') }}</p>
        </div>
        @can('manage-backups')
        <form action="{{ route('backups.create') }}" method="POST" id="backupForm" onsubmit="showProcessing()">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 py-2" id="backupBtn">
                <i data-lucide="database" style="width:15px;" id="btnIcon"></i>
                <span id="btnText">{{ __('Create Backup') }}</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </form>
        @endcan
    </div>

    <div class="card border" style="border-color:#dee2e6 !important; border-radius:6px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fa; border-bottom:1px solid #dee2e6;">
                        <tr>
                            <th class="ps-4 py-3 small text-muted fw-semibold text-uppercase" style="font-size:0.72rem; width:50px;">#</th>
                            <th class="py-3 small text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">{{ __('File Name') }}</th>
                            <th class="py-3 small text-muted fw-semibold text-uppercase text-center" style="font-size:0.72rem;">{{ __('Size') }}</th>
                            <th class="py-3 small text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">{{ __('Created') }}</th>
                            <th class="py-3 small text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">{{ __('Disk') }}</th>
                            <th class="pe-4 py-3 small text-muted fw-semibold text-uppercase text-end" style="font-size:0.72rem; width:100px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $index => $backup)
                        <tr>
                            <td class="ps-4">
                                <span class="row-num">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="backup-icon">
                                        <i data-lucide="file-archive" style="width:14px;height:14px;"></i>
                                    </div>
                                    <span class="fw-semibold text-dark small font-monospace">{{ $backup['file_name'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="size-badge">{{ $backup['file_size'] }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $backup['last_modified'] }}</span>
                            </td>
                            <td>
                                <span class="disk-badge">
                                    <i data-lucide="database" style="width:11px;height:11px;"></i>
                                    {{ strtoupper($backup['disk']) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ $backup['download_link'] }}" class="action-btn dl-btn" title="{{ __('Download') }}">
                                        <i data-lucide="download" style="width:14px;height:14px;"></i>
                                    </a>
                                    @can('manage-backups')
                                    <form action="{{ route('backups.destroy') }}" method="POST"
                                          onsubmit="return confirm('{{ __('Delete this backup?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                                        <input type="hidden" name="file" value="{{ $backup['file_name'] }}">
                                        <button type="submit" class="action-btn del-btn" title="{{ __('Delete') }}">
                                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5">
                                <div class="text-center">
                                    <i data-lucide="database-zap" style="width:48px;height:48px;color:#ced4da;"></i>
                                    <p class="text-muted fw-semibold mt-3 mb-1">{{ __('No backups found') }}</p>
                                    <small class="text-muted">{{ __('Click "Create Backup" to get started.') }}</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; background: #f1f3f5; border-radius: 50%;
        font-size: 0.75rem; font-weight: 600; color: #6c757d;
    }
    .backup-icon {
        width: 32px; height: 32px; border-radius: 6px; flex-shrink: 0;
        background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe;
        display: flex; align-items: center; justify-content: center;
    }
    .size-badge {
        display: inline-block; padding: 2px 8px; border-radius: 4px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        font-size: 0.78rem; font-weight: 600; color: #475569;
    }
    .disk-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 4px;
        background: #e0f2fe; border: 1px solid #bae6fd;
        font-size: 0.72rem; font-weight: 700; color: #0369a1;
    }
    .action-btn {
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid #e9ecef; background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .action-btn.dl-btn  { color: #3b82f6; }
    .action-btn.dl-btn:hover  { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .action-btn.del-btn { color: #ef4444; }
    .action-btn.del-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-size: 0.875rem; }
    .btn-primary:hover { background-color: #0b5ed7; }
    .table-hover tbody tr:hover td { background-color: #f8f9fb; }
</style>

@push('js')
<script>
function showProcessing() {
    const btn = document.getElementById('backupBtn');
    const text = document.getElementById('btnText');
    const spinner = document.getElementById('btnSpinner');
    btn.disabled = true;
    text.innerText = '{{ __("Backing up...") }}';
    spinner.classList.remove('d-none');
    if (typeof showToast === 'function') showToast('{{ __("Backup process started. Please wait...") }}', 'info');
}
</script>
@endpush
@endsection