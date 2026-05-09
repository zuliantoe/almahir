@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.aset.index') }}">Master Aset</a></li>
            <li class="breadcrumb-item active">Scan QR</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-qrcode mr-2"></i> Scanner Internal Aset
                    </h5>
                </div>
                <div class="card-body p-0 position-relative">
                    {{-- Area Scanner --}}
                    <div id="reader" style="width: 100%; min-height: 400px; background: #000;"></div>
                    
                    {{-- Overlay Loading/Status --}}
                    <div id="scanner-overlay" class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="top:0; left:0; background: rgba(0,0,0,0.7); z-index: 10; display: none !important;">
                        <div class="text-white">
                            <div class="spinner-border text-primary mb-3" role="status"></div>
                            <h6 class="font-weight-bold">Memproses Data...</h6>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-4">
                    <p class="text-muted small mb-3">
                        Arahkan kamera ke **QR Code Aset** yang ada di label. <br>
                        Hanya Admin terverifikasi yang dapat mengakses data ini.
                    </p>
                    <div class="d-flex justify-content-center">
                        <button id="btn-stop" class="btn btn-outline-danger btn-sm mr-2" style="display:none;">
                            <i class="fas fa-stop mr-1"></i> Berhenti
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-secondary btn-sm px-4">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Library Scanner --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        const html5QrCode = new Html5Qrcode("reader");
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            // Berhenti scan
            html5QrCode.stop().then((ignore) => {
                // Tampilkan overlay loading
                $('#scanner-overlay').attr('style', 'top:0; left:0; background: rgba(0,0,0,0.7); z-index: 10; display: flex !important;');
                
                // Cek apakah decodedText adalah URL yang valid ke aplikasi kita
                // Karena rute kita mengandung rute detail aset
                if (decodedText.includes('{{ url("/manajemenasetdanasrama/aset") }}')) {
                    // Langsung redirect ke halaman detail
                    window.location.href = decodedText;
                } else {
                    alert("QR Code tidak dikenali sebagai aset Siakad Almahir.");
                    location.reload(); // Reset scanner
                }
            }).catch((err) => {
                console.error("Stop error", err);
            });
        };

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        // Mulai Scanner
        html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
            .then(() => {
                $('#btn-stop').show();
            })
            .catch((err) => {
                alert("Gagal mengakses kamera. Pastikan izin kamera sudah diaktifkan.");
                console.error("Start error", err);
            });

        $('#btn-stop').on('click', function() {
            html5QrCode.stop().then(() => {
                $(this).hide();
                $('#reader').html('<div class="d-flex align-items-center justify-content-center h-100 text-white"><p>Scanner Berhenti</p></div>');
            });
        });
    });
</script>
@endpush

@push('css')
<style>
    #reader__scan_region img {
        display: none !important;
    }
    #reader__dashboard {
        padding: 10px !important;
        background: #f8f9fa !important;
        border-top: 1px solid #ddd !important;
    }
    #reader__dashboard_section_csr button {
        background: #007bff !important;
        color: white !important;
        border: none !important;
        padding: 5px 15px !important;
        border-radius: 5px !important;
        margin-bottom: 10px !important;
    }
</style>
@endpush
@endsection
