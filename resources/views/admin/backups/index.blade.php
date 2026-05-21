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

    <!-- Backup Task Scheduler Section -->
    <div class="row mt-4 g-4">
        <!-- Settings Column -->
        <div class="col-lg-5">
            <div class="card border h-100" style="border-color:#dee2e6 !important; border-radius:6px; background:#fff;">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2" style="border-color:#dee2e6 !important;">
                    <i data-lucide="clock" class="text-primary" style="width:18px; height:18px;"></i>
                    <h5 class="mb-0 fw-semibold text-dark" style="font-size:1rem;">{{ __('Automated Backup Scheduler') }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('backups.schedule.update') }}" method="POST" id="scheduleForm">
                        @csrf
                        
                        <!-- Toggle Switch -->
                        <div class="form-check form-switch mb-4 p-0 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-semibold text-dark mb-1" for="backup_schedule_enabled" style="font-size:0.9rem; cursor:pointer;">
                                    {{ __('Enable Automated Backups') }}
                                </label>
                                <p class="text-muted small mb-0">{{ __('Run daily database backup automatically') }}</p>
                            </div>
                            <div class="form-check form-switch-lg">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="backup_schedule_enabled" name="backup_schedule_enabled" value="1" {{ $scheduleEnabled == '1' ? 'checked' : '' }} style="width: 2.8em; height: 1.5em; cursor: pointer;">
                            </div>
                        </div>

                        <!-- Time Selector -->
                        <div class="mb-4" id="time_selector_wrapper" style="transition: all 0.3s ease;">
                            <label for="backup_schedule_time" class="form-label fw-semibold text-dark mb-2" style="font-size:0.9rem;">
                                {{ __('Daily Trigger Time') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i data-lucide="calendar-clock" class="text-muted" style="width:16px;"></i>
                                </span>
                                <input type="time" class="form-control bg-white border-start-0" id="backup_schedule_time" name="backup_schedule_time" value="{{ $scheduleTime }}" required>
                            </div>
                            <small class="text-muted d-block mt-2">
                                {{ __('Runs daily at the specified time using Laravel\'s schedule command.') }}
                            </small>
                        </div>

                        <!-- Backup Folder Path Destination -->
                        <div class="mb-4" id="folder_selector_wrapper">
                            <label for="backup_disk_path" class="form-label fw-semibold text-dark mb-2" style="font-size:0.9rem;">
                                {{ __('Backup Destination Folder Path') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i data-lucide="folder" class="text-muted" style="width:16px;"></i>
                                </span>
                                <input type="text" class="form-control bg-white border-x-0" id="backup_disk_path" name="backup_disk_path" value="{{ $backupDiskPath }}" placeholder="e.g. D:\Backups">
                                <button class="btn btn-outline-secondary d-flex align-items-center gap-1 border-start-0 px-3" type="button" id="btnBrowseFolder" style="border-top-left-radius:0; border-bottom-left-radius:0; border-color: #ced4da;">
                                    <i data-lucide="folder-search" style="width:15px; height:15px;"></i>
                                    {{ __('Browse...') }}
                                </button>
                            </div>
                            @error('backup_disk_path')
                                <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                {{ __('Absolute folder path where backups will be saved. Click Browse... to pick or create a folder, or leave empty to use default storage folder.') }}
                            </small>
                        </div>

                        <!-- Status Alert/Badge -->
                        <div class="d-flex align-items-center justify-content-between p-3 rounded mb-4" style="background: {{ $scheduleEnabled == '1' ? '#f0fdf4; border: 1px solid #bbf7d0;' : '#f8fafc; border: 1px solid #e2e8f0;' }}; transition: all 0.3s ease;">
                            <div class="d-flex align-items-center gap-2">
                                @if($scheduleEnabled == '1')
                                    <span class="d-inline-block rounded-circle bg-success pulse-dot" style="width:8px; height:8px;"></span>
                                    <span class="fw-semibold text-success small">{{ __('Active Schedule') }}</span>
                                @else
                                    <span class="d-inline-block rounded-circle bg-secondary" style="width:8px; height:8px;"></span>
                                    <span class="fw-semibold text-muted small">{{ __('Disabled') }}</span>
                                @endif
                            </div>
                            <span class="badge text-dark font-monospace" style="background: {{ $scheduleEnabled == '1' ? '#dcfce7; border: 1px solid #86efac;' : '#f1f5f9; border: 1px solid #cbd5e1;' }}; font-size:0.75rem; padding:4px 8px;">
                                @if($scheduleEnabled == '1')
                                    {{ __('Daily @ ') }}{{ $scheduleTime }}
                                @else
                                    {{ __('N/A') }}
                                @endif
                            </span>
                        </div>

                        <!-- Action Button -->
                        @can('manage-backups')
                        <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold">
                            <i data-lucide="settings" style="width:16px;"></i>
                            {{ __('Save Scheduler Settings') }}
                        </button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>

        <!-- Windows Task Scheduler Guide Column -->
        <div class="col-lg-7">
            <div class="card border h-100" style="border-color:#dee2e6 !important; border-radius:6px; background:#fff;">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2" style="border-color:#dee2e6 !important;">
                    <i data-lucide="terminal" class="text-primary" style="width:18px; height:18px;"></i>
                    <h5 class="mb-0 fw-semibold text-dark" style="font-size:1rem;">{{ __('Windows Task Scheduler Integration') }}</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">
                        {{ __('To make this scheduler work automatically in the background on your Windows Server or PC, you must add a single task to the Windows Task Scheduler that executes Laravel\'s command scheduler every minute.') }}
                    </p>

                    <!-- Instructions List -->
                    <div class="d-flex flex-column gap-3">
                        <!-- Step 1 -->
                        <div class="d-flex gap-3">
                            <div class="step-num">1</div>
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold text-dark mb-1" style="font-size:0.875rem;">{{ __('Create a New Basic Task') }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ __('Open Windows Task Scheduler, click') }} <strong class="text-dark">{{ __('Create Basic Task...') }}</strong>{{ __(', name it ') }}<code class="px-2 py-0.5 rounded font-monospace" style="background:#f1f5f9; color:#0f172a; font-size:0.8rem;">ROS_Backup_Scheduler</code>{{ __(' and set Trigger to ') }}<strong class="text-dark">{{ __('Daily') }}</strong>{{ __(' or ') }}<strong class="text-dark">{{ __('When I log on') }}</strong>.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex gap-3">
                            <div class="step-num">2</div>
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold text-dark mb-1" style="font-size:0.875rem;">{{ __('Configure Repeating Interval') }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ __('Once created, edit the task properties. Under Triggers, select the trigger, click Edit, tick ') }}<strong class="text-dark">{{ __('Repeat task every:') }}</strong> <code class="px-2 py-0.5 rounded font-monospace" style="background:#f1f5f9; color:#0f172a; font-size:0.8rem;">1 minute</code>{{ __(' and set duration to ') }}<strong class="text-dark">{{ __('Indefinitely') }}</strong>.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex gap-3">
                            <div class="step-num">3</div>
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold text-dark mb-1" style="font-size:0.875rem;">{{ __('Configure Action Settings') }}</h6>
                                <p class="text-muted small mb-3">
                                    {{ __('Under Actions tab, select ') }}<strong class="text-dark">{{ __('Start a Program') }}</strong>{{ __(' and copy these values:') }}
                                </p>

                                <div class="bg-light border rounded p-3 font-monospace small position-relative d-flex flex-column gap-3 text-secondary" style="background-color: #f8fafc !important;">
                                    <!-- Program/script -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted fw-semibold" style="font-size:0.75rem; text-transform:uppercase;">
                                            <span>{{ __('Program/Script') }}</span>
                                            <button class="btn btn-link btn-xs p-0 text-decoration-none copy-btn d-flex align-items-center gap-1" data-copy="php" style="font-size:0.75rem;">
                                                <i data-lucide="copy" style="width:12px;"></i> {{ __('Copy') }}
                                            </button>
                                        </div>
                                        <div class="bg-white border rounded px-2 py-1.5 text-dark select-all">php</div>
                                    </div>

                                    <!-- Arguments -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted fw-semibold" style="font-size:0.75rem; text-transform:uppercase;">
                                            <span>{{ __('Add Arguments (Optional)') }}</span>
                                            <button class="btn btn-link btn-xs p-0 text-decoration-none copy-btn d-flex align-items-center gap-1" data-copy="artisan schedule:run" style="font-size:0.75rem;">
                                                <i data-lucide="copy" style="width:12px;"></i> {{ __('Copy') }}
                                            </button>
                                        </div>
                                        <div class="bg-white border rounded px-2 py-1.5 text-dark select-all">artisan schedule:run</div>
                                    </div>

                                    <!-- Start in -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted fw-semibold" style="font-size:0.75rem; text-transform:uppercase;">
                                            <span>{{ __('Start In (Optional / Working Dir)') }}</span>
                                            <button class="btn btn-link btn-xs p-0 text-decoration-none copy-btn d-flex align-items-center gap-1" data-copy="{{ base_path() }}" style="font-size:0.75rem;">
                                                <i data-lucide="copy" style="width:12px;"></i> {{ __('Copy') }}
                                            </button>
                                        </div>
                                        <div class="bg-white border rounded px-2 py-1.5 text-dark select-all">{{ base_path() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Folder Browser Modal -->
<div class="modal fade" id="folderBrowserModal" tabindex="-1" aria-labelledby="folderBrowserModalLabel" aria-hidden="true" style="backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
            <div class="modal-header border-bottom py-3 px-4" style="background:#f8fafc; border-top-left-radius:12px; border-top-right-radius:12px;">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="folder-open" class="text-primary" style="width:20px; height:20px;"></i>
                    <h5 class="modal-title fw-semibold text-dark mb-0" id="folderBrowserModalLabel" style="font-size:1.05rem;">
                        {{ __('Select Backup Destination Folder') }}
                    </h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background:#fff;">
                <!-- Breadcrumbs & Path Input -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <button type="button" class="btn btn-light border d-flex align-items-center justify-content-center p-2 shadow-sm" id="fbGoUp" title="{{ __('Go Up') }}" style="width:38px; height:38px; border-radius:6px;">
                        <i data-lucide="arrow-up" style="width:16px; height:16px;"></i>
                    </button>
                    <div class="flex-grow-1">
                        <input type="text" class="form-control bg-light text-dark fw-semibold" id="fbCurrentPath" readonly style="font-size:0.875rem; padding-left:12px; border-radius:6px; border-color:#dee2e6;">
                    </div>
                </div>

                <!-- Create Folder Subform (Inline) -->
                <div class="d-flex gap-2 mb-3 p-3 rounded border" style="background-color: #f8fafc !important; border-color: #e2e8f0 !important;">
                    <div class="flex-grow-1">
                        <input type="text" class="form-control form-control-sm" id="newFolderName" placeholder="{{ __('New folder name...') }}" style="border-radius:4px; border-color:#cbd5e1;">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 px-3" id="btnCreateFolder" style="font-size:0.82rem; border-radius:4px;">
                        <i data-lucide="folder-plus" style="width:14px; height:14px;"></i>
                        {{ __('New Folder') }}
                    </button>
                </div>

                <!-- Folders Listing Container -->
                <div class="border rounded overflow-hidden shadow-sm" style="border-color:#e2e8f0 !important;">
                    <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center" style="font-size:0.75rem; text-transform:uppercase; font-weight:600; color:#64748b; letter-spacing:0.05em;">
                        <span>{{ __('Folders') }}</span>
                        <span id="foldersCount" class="badge bg-secondary text-white" style="font-size:0.7rem; font-weight:700; border-radius:10px; padding:3px 6px;">0</span>
                    </div>
                    <div class="list-group list-group-flush" id="foldersList" style="max-height: 280px; overflow-y: auto; min-height: 150px;">
                        <!-- JS Dynamically populates this -->
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-3 px-4" style="border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
                <div class="w-100 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="text-muted small fw-semibold text-truncate" style="max-width: 50%;">
                        {{ __('Selected: ') }}<span id="selectedPathDisplay" class="text-dark font-monospace fw-bold"></span>
                    </span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border px-4 py-2 small fw-semibold text-secondary" data-bs-dismiss="modal" style="font-size:0.875rem; border-radius:6px;">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary px-4 py-2 small fw-semibold" id="btnConfirmSelectFolder" style="font-size:0.875rem; border-radius:6px;">
                            <i data-lucide="check" class="me-1" style="width:14px; height:14px; display:inline-block; vertical-align:text-bottom;"></i>
                            {{ __('Select Folder') }}
                        </button>
                    </div>
                </div>
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
    
    .step-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 24px; height: 24px; background: #eff6ff; color: #3b82f6;
        border: 1px solid #dbeafe; border-radius: 50%;
        font-size: 0.78rem; font-weight: 700; flex-shrink: 0;
    }
    .pulse-dot {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
        animation: pulse 1.6s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(40, 167, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
    .copy-btn {
        transition: all 0.2s;
    }
    .copy-btn:hover {
        color: #0d6efd !important;
    }
    .select-all {
        user-select: all;
    }
    .form-switch-lg .form-check-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
    }
    .form-switch-lg .form-check-input:checked {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
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

document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const text = this.getAttribute('data-copy');
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i data-lucide="check" style="width:12px;"></i> Copied!';
            if (window.lucide) {
                window.lucide.createIcons();
            }
            setTimeout(() => {
                this.innerHTML = originalHTML;
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    });
});

// Folder Browser Functionality
let currentFolderPath = '';
let parentFolderPath = null;
let folderBrowserModalObj = null;

document.getElementById('btnBrowseFolder').addEventListener('click', function() {
    const modalEl = document.getElementById('folderBrowserModal');
    if (!folderBrowserModalObj) {
        folderBrowserModalObj = new bootstrap.Modal(modalEl);
    }
    
    // Start with whatever path is in the input field, or default to empty
    let startingPath = document.getElementById('backup_disk_path').value.trim();
    loadFolderPath(startingPath);
    folderBrowserModalObj.show();
});

function loadFolderPath(path) {
    const foldersList = document.getElementById('foldersList');
    foldersList.innerHTML = `
        <div class="d-flex align-items-center justify-content-center p-5 text-muted">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            <span>{{ __('Loading folders...') }}</span>
        </div>
    `;

    fetch(`{{ route('backups.browse-folders') }}?path=${encodeURIComponent(path)}`)
        .then(response => response.json())
        .then(data => {
            currentFolderPath = data.current_path;
            parentFolderPath = data.parent_path;
            
            document.getElementById('fbCurrentPath').value = currentFolderPath;
            document.getElementById('selectedPathDisplay').innerText = currentFolderPath;
            
            const goUpBtn = document.getElementById('fbGoUp');
            if (parentFolderPath) {
                goUpBtn.disabled = false;
                goUpBtn.onclick = () => loadFolderPath(parentFolderPath);
            } else {
                goUpBtn.disabled = true;
                goUpBtn.onclick = null;
            }

            foldersList.innerHTML = '';

            const folders = data.folders || [];
            document.getElementById('foldersCount').innerText = folders.length;

            if (folders.length === 0) {
                foldersList.innerHTML = `
                    <div class="text-center py-5 text-muted small">
                        <i data-lucide="folder-x" style="width:32px; height:32px; color:#cbd5e1; display:block; margin:0 auto 8px;"></i>
                        <p class="mb-0">{{ __('No subfolders found') }}</p>
                    </div>
                `;
            } else {
                folders.forEach(folder => {
                    const isDrive = data.is_windows && currentFolderPath === 'DRIVES';
                    const iconName = isDrive ? 'hard-drive' : 'folder';
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2.5 px-3 border-0 border-bottom text-dark';
                    item.style.fontSize = '0.85rem';
                    item.innerHTML = `
                        <div class="d-flex align-items-center gap-2 text-truncate">
                            <i data-lucide="${iconName}" class="text-primary-emphasis" style="width:16px; height:16px; flex-shrink:0;"></i>
                            <span class="fw-semibold text-truncate">${folder.name}</span>
                        </div>
                        <i data-lucide="chevron-right" class="text-muted" style="width:14px; height:14px; flex-shrink:0;"></i>
                    `;
                    item.addEventListener('click', () => {
                        loadFolderPath(folder.path);
                    });
                    foldersList.appendChild(item);
                });
            }
            if (window.lucide) {
                window.lucide.createIcons();
            }
        })
        .catch(err => {
            console.error('Error browsing folders:', err);
            foldersList.innerHTML = `
                <div class="text-center py-5 text-danger small">
                    <i data-lucide="alert-circle" style="width:32px; height:32px; display:block; margin:0 auto 8px;"></i>
                    <p class="mb-0">{{ __('Failed to load directories.') }}</p>
                </div>
            `;
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
}

// Confirm Selection
document.getElementById('btnConfirmSelectFolder').addEventListener('click', function() {
    if (currentFolderPath) {
        if (currentFolderPath === 'DRIVES') {
            if (typeof showToast === 'function') showToast('{{ __("Please select a specific folder, not the drives list.") }}', 'warning');
            return;
        }
        document.getElementById('backup_disk_path').value = currentFolderPath;
        if (folderBrowserModalObj) {
            folderBrowserModalObj.hide();
        }
        if (typeof showToast === 'function') {
            showToast('{{ __("Backup folder selected.") }}', 'success');
        }
    }
});

// Create Folder Handler
document.getElementById('btnCreateFolder').addEventListener('click', function() {
    const newNameInput = document.getElementById('newFolderName');
    const folderName = newNameInput.value.trim();
    if (!folderName) {
        if (typeof showToast === 'function') showToast('{{ __("Please enter a folder name.") }}', 'warning');
        return;
    }

    if (currentFolderPath === 'DRIVES') {
        if (typeof showToast === 'function') showToast('{{ __("Cannot create folders directly in drive root list.") }}', 'warning');
        return;
    }

    const btn = this;
    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span>`;

    fetch(`{{ route('backups.create-folder') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            parent_path: currentFolderPath,
            folder_name: folderName
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        if (data.success) {
            newNameInput.value = '';
            if (typeof showToast === 'function') showToast(data.message, 'success');
            loadFolderPath(currentFolderPath);
        } else {
            if (typeof showToast === 'function') showToast(data.message || 'Failed to create folder.', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        console.error('Error creating folder:', err);
        if (typeof showToast === 'function') showToast('{{ __("Error creating folder.") }}', 'error');
    });
});
</script>
@endpush
@endsection