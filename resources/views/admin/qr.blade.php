@extends('layouts.app')

@section('content')
<div class="p-1 p-md-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="fw-black mb-1">QR Code</h2>
            <p class="text-muted mb-0">Scan the QR code below or open the image directly.</p>
        </div>
        <a href="{{ asset('images/myqr.jpg') }}" target="_blank" class="btn btn-primary px-4 py-2">
            View full-size QR
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-lg p-4 text-center">
        <div class="mb-4">
            <img src="{{ asset('images/myqr.jpg') }}" alt="My QR Code" class="img-fluid" style="max-width: 320px;" />
        </div>
        <div>
            <p class="small text-muted mb-1">QR image file stored at:</p>
            <code>/public/images/myqr.jpg</code>
        </div>
    </div>
</div>
@endsection
