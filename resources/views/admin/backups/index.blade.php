@extends('layouts.app')

@section('title', 'System Backups')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-black mb-1">System Backups</h4>
                            <p class="text-muted small mb-0">Manage and create database backups for your system.</p>
                        </div>
                        <form action="{{ route('backups.create') }}" method="POST" id="backupForm" onsubmit="showProcessing()">
                            @csrf
                            <button type="submit" class="btn btn-primary px-4 fw-bold" id="backupBtn">
                                <i data-lucide="plus-circle" class="me-2" style="width: 18px;"></i>
                                <span id="btnText">Create New Backup</span>
                                <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0">#</th>
                                    <th class="py-3 border-0">File Name</th>
                                    <th class="py-3 border-0 text-center">Size</th>
                                    <th class="py-3 border-0">Created At</th>
                                    <th class="py-3 border-0">Disk</th>
                                    <th class="pe-4 py-3 border-0 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $index => $backup)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i data-lucide="file-archive" class="text-primary" style="width: 18px;"></i>
                                            <span class="fw-bold">{{ $backup['file_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark fw-bold border">{{ $backup['file_size'] }}</span>
                                    </td>
                                    <td class="text-muted small">{{ $backup['last_modified'] }}</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border-info border-opacity-25 px-3">
                                            <i data-lucide="database" class="me-1" style="width: 12px;"></i>
                                            {{ strtoupper($backup['disk']) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ $backup['download_link'] }}" class="btn btn-sm btn-light-primary fw-bold" title="Download">
                                                <i data-lucide="download" style="width: 16px;"></i>
                                            </a>
                                            <form action="{{ route('backups.destroy') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this backup?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                                                <input type="hidden" name="file" value="{{ $backup['file_name'] }}">
                                                <button type="submit" class="btn btn-sm btn-light-danger fw-bold" title="Delete">
                                                    <i data-lucide="trash-2" style="width: 16px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="opacity-50 mb-3">
                                            <i data-lucide="database-zap" style="width: 48px; height: 48px;"></i>
                                        </div>
                                        <h5 class="fw-bold text-muted">No backups found</h5>
                                        <p class="text-muted small">Create your first backup by clicking the button above.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-light-primary {
        background: #f0f7ff;
        color: #007bff;
        border: none;
    }

    .btn-light-primary:hover {
        background: #007bff;
        color: #fff;
    }

    .btn-light-danger {
        background: #fff5f5;
        color: #dc3545;
        border: none;
    }

    .btn-light-danger:hover {
        background: #dc3545;
        color: #fff;
    }
</style>
@endsection

@push('js')
<script>
    function showProcessing() {
        const btn = document.getElementById('backupBtn');
        const text = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');

        btn.disabled = true;
        text.innerText = 'Backing up...';
        spinner.classList.remove('d-none');

        showToast('Backup process started. Please wait...', 'info');
    }
</script>
@endpush