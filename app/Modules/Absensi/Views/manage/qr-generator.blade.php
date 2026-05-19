@extends('layouts.app')

@section('title', $title)

@section('content')
<style>
    .tv-screen {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        min-height: 80vh;
        border-radius: 30px;
        box-shadow: inset 0 0 50px rgba(0,0,0,0.5), 0 20px 40px rgba(0,0,0,0.4);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        padding: 40px;
        position: relative;
        overflow: hidden;
    }
    .glass-box {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        animation: float 6s ease-in-out infinite;
    }
    .qr-wrapper {
        background: white;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        display: inline-block;
        margin-bottom: 20px;
    }
    .clock-text {
        font-family: 'Inter', 'Roboto', sans-serif;
        font-size: 5rem;
        font-weight: 900;
        letter-spacing: 2px;
        text-shadow: 0 5px 15px rgba(0,0,0,0.4);
        background: -webkit-linear-gradient(#fff, #a1c4fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .date-text {
        font-size: 1.5rem;
        font-weight: 500;
        color: #e2e2e2;
        text-transform: uppercase;
        letter-spacing: 4px;
        margin-bottom: 30px;
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    .pulse-dot {
        height: 15px;
        width: 15px;
        background-color: #00ff88;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 10px #00ff88, 0 0 20px #00ff88;
        animation: pulse 1.5s infinite;
        margin-right: 10px;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(0, 255, 136, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0); }
    }
</style>

<div class="container-fluid py-4">
    <div class="tv-screen">
        <div style="position: absolute; top: 30px; left: 40px; display: flex; align-items: center;">
            <div class="pulse-dot"></div>
            <span style="font-weight: bold; letter-spacing: 2px; color: #00ff88;">ALMAHIRA SECURE SYSTEM</span>
        </div>
        
        <div class="text-center">
            <h1 class="display-4 font-weight-bold mb-2 text-white">Presensi Pegawai</h1>
            <p class="lead mb-5 text-light" style="opacity: 0.8;">Buka aplikasi Almahira di HP Anda, lalu arahkan kamera ke QR Code ini</p>
            
            <div class="glass-box text-center">
                <div class="qr-wrapper">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ $qrToken }}&margin=10" alt="QR Code Absensi" class="img-fluid" style="width: 350px; height: 350px; border-radius: 10px;">
                </div>
                
                <div class="date-text mt-4">{{ now()->translatedFormat('l, d F Y') }}</div>
                <div id="realtime-clock" class="clock-text">00:00:00</div>
            </div>
            
            <div class="mt-5" style="opacity: 0.7;">
                <p class="mb-0"><i class="fas fa-map-marker-alt text-danger"></i> <strong>Sistem Validasi Lokasi Aktif</strong></p>
                <small>Radius toleransi: {{ config('absensi.office_radius', 50) }} Meter dari pusat kantor</small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const display = now.toLocaleTimeString('id-ID', { hour12: false });
        const clockEl = document.getElementById('realtime-clock');
        if (clockEl) clockEl.textContent = display;
    }
    setInterval(updateClock, 1000);
    updateClock();
    
    // Auto refresh halaman jam 00:00 untuk mengganti token harian
    setTimeout(function() {
        window.location.reload();
    }, 1000 * 60 * 60 * 24); 
</script>
@endpush
@endsection
