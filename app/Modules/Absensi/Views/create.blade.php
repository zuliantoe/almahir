@extends('layouts.app')

@section('title', $title)

@section('content')
<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
    }
    .profile-img-glow {
        border: 4px solid #fff;
        box-shadow: 0 0 20px rgba(13, 110, 253, 0.4);
        width: 80px;
        height: 80px;
        object-fit: cover;
    }
    .scanner-frame {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        background: #000;
        margin-bottom: 20px;
        border: 4px solid #fff;
    }
    .scanner-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        border: 2px solid rgba(255,255,255,0.1);
        z-index: 10;
        pointer-events: none;
    }
    .scanner-overlay::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: #00ff88;
        box-shadow: 0 0 15px #00ff88;
        animation: scanline 3s linear infinite;
    }
    @keyframes scanline {
        0% { top: 0; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
    .status-badge {
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 600;
        letter-spacing: 1px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-panel p-0 overflow-hidden">
                <div class="p-4" style="background: linear-gradient(135deg, #0d6efd, #0043a8); color: white; text-align: center;">
                    <h3 class="font-weight-bold mb-0">
                        <i class="fas fa-fingerprint mr-2"></i> Presensi Pintar
                    </h3>
                </div>
                
                <div class="p-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-block position-relative mb-3">
                            <img src="{{ Auth::user()->avatar_url }}" class="rounded-circle profile-img-glow">
                        </div>
                        <h5 class="font-weight-bold mb-0 text-dark">{{ $pegawai->nama }}</h5>
                        <p class="text-muted small">{{ $pegawai->typePegawai->nama_type ?? 'Pegawai' }}</p>
                    </div>

                    <div class="text-center mb-4">
                        <p class="text-muted mb-1 text-uppercase font-weight-bold small tracking-wide">{{ now()->translatedFormat('l, d F Y') }}</p>
                        <div id="realtime-clock" class="display-4 font-weight-bold" style="color: #0d6efd; letter-spacing: 2px;">00:00:00</div>
                    </div>

                    @if(!$currentAbsen || !$currentAbsen->jam_pulang)
                        
                        @if($currentAbsen && !$currentAbsen->jam_pulang)
                        {{-- SUDAH MASUK, BELUM PULANG --}}
                        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg" style="background: rgba(40, 167, 69, 0.1); border-left: 5px solid #28a745 !important;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <h6 class="font-weight-bold mb-0 text-success">Absen Masuk Tercatat</h6>
                                    <small class="text-muted">Pukul: <strong>{{ $currentAbsen->jam_masuk }}</strong> | <strong>{{ $currentAbsen->status }}</strong></small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            <span class="status-badge bg-danger text-white shadow-sm">
                                <i class="fas fa-sign-out-alt mr-1"></i> Waktunya Absen Pulang
                            </span>
                        </div>
                        @else
                        {{-- BELUM ABSEN MASUK --}}
                        <div class="alert alert-info border-0 shadow-sm mb-4 rounded-lg" style="background: rgba(13, 110, 253, 0.1); border-left: 5px solid #0d6efd !important;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x text-primary mr-3"></i>
                                <div class="text-left">
                                    <h6 class="font-weight-bold mb-0 text-primary">Selamat Pagi!</h6>
                                    <small class="text-muted">Silakan scan QR Code di lobi untuk presensi.</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- SCRIPT SCANNER -->
                        <div class="scanner-frame">
                            <div id="reader" style="width: 100%; border: none;"></div>
                            <div class="scanner-overlay"></div>
                        </div>
                        <div class="text-center mb-3">
                            <small class="text-muted"><i class="fas fa-map-marker-alt text-danger"></i> Menunggu kunci lokasi GPS aktif...</small>
                        </div>

                        <form action="{{ !$currentAbsen ? route('absensi.store') : route('absensi.update') }}" method="POST" id="form-absen">
                            @csrf
                            <input type="hidden" name="lat" id="lat">
                            <input type="hidden" name="long" id="long">
                            <input type="hidden" name="qr_token" id="qr_token">
                            
                            @if(!$currentAbsen)
                            <div class="form-group mt-3">
                                <label class="small font-weight-bold text-muted">Tambahkan Catatan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="2" style="border-radius: 10px; background: #f8f9fa;" placeholder="Keterangan kondisi Anda hari ini..."></textarea>
                            </div>
                            @endif
                        </form>

                    @else
                        {{-- SUDAH SELESAI HARI INI --}}
                        <div class="py-5 text-center">
                            <div class="mb-4 d-inline-block p-4 rounded-circle" style="background: rgba(40, 167, 69, 0.1);">
                                <i class="fas fa-check-double fa-4x text-success"></i>
                            </div>
                            <h3 class="font-weight-bold text-dark">Luar Biasa!</h3>
                            <p class="text-muted">Anda telah menyelesaikan tugas hari ini.</p>
                            
                            <div class="d-flex justify-content-center mt-4">
                                <div class="bg-light p-3 rounded-lg mr-2 border text-center" style="min-width: 100px;">
                                    <small class="d-block text-muted font-weight-bold">MASUK</small>
                                    <h5 class="mb-0 text-dark">{{ $currentAbsen->jam_masuk }}</h5>
                                </div>
                                <div class="bg-light p-3 rounded-lg border text-center" style="min-width: 100px;">
                                    <small class="d-block text-muted font-weight-bold">PULANG</small>
                                    <h5 class="mb-0 text-dark">{{ $currentAbsen->jam_pulang }}</h5>
                                </div>
                            </div>

                            <div class="mt-5">
                                <a href="{{ route('absensi.index') }}" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                    <i class="fas fa-history mr-2"></i> Lihat Riwayat Absen
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // Realtime Clock
    function updateClock() {
        const now = new Date();
        const display = now.toLocaleTimeString('id-ID', { hour12: false });
        const clockEl = document.getElementById('realtime-clock');
        if (clockEl) clockEl.textContent = display;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Geolocation Setup
    let currentLat = null;
    let currentLong = null;
    
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            currentLat = position.coords.latitude;
            currentLong = position.coords.longitude;
            document.getElementById('lat').value = currentLat;
            document.getElementById('long').value = currentLong;
            console.log("GPS Location Ready: " + currentLat + ", " + currentLong);
        }, function(error) {
            Swal.fire('GPS Error', 'Gagal mendapatkan lokasi: ' + error.message + '. Pastikan GPS aktif.', 'error');
        }, { enableHighAccuracy: true });
    }

    // QR Code Scanner Setup
    @if(!$currentAbsen || !$currentAbsen->jam_pulang)
    function onScanSuccess(decodedText, decodedResult) {
        if (!currentLat || !currentLong) {
            Swal.fire('Tunggu', 'Sedang mencari lokasi GPS Anda...', 'warning');
            return;
        }
        
        // Matikan scanner agar tidak scan dobel
        html5QrcodeScanner.clear();
        
        // Masukkan hasil token rahasia ke hidden input
        document.getElementById('qr_token').value = decodedText;
        
        Swal.fire({
            title: 'Memproses Absensi...',
            text: 'Mencocokkan koordinat Anda dengan pusat kantor...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        // Submit form otomatis
        document.getElementById('form-absen').submit();
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} });
    html5QrcodeScanner.render(onScanSuccess);
    @endif
</script>
@endpush
@endsection
