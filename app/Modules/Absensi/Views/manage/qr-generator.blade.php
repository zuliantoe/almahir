@extends('layouts.app')

@section('title', $title)

@section('content')
<!-- Include html5-qrcode library for webcam scanning -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
    body {
        background: #0b132b !important;
    }
    .tv-container {
        background: linear-gradient(135deg, #0b132b, #1c2541);
        min-height: 85vh;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        color: white;
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    .glass-panel {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }
    .scanner-window {
        min-height: 350px;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        border: 3px solid rgba(255, 255, 255, 0.1);
        background: #000;
    }
    .scanner-active-border {
        border-color: #00b4d8 !important;
        box-shadow: 0 0 15px rgba(0, 180, 216, 0.5);
    }
    .scanner-laser {
        position: absolute;
        width: 100%;
        height: 3px;
        background-color: #00ff88;
        box-shadow: 0 0 10px #00ff88;
        top: 0;
        z-index: 10;
        animation: laserScan 3s linear infinite;
        display: none;
    }
    @keyframes laserScan {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }
    .clock-large {
        font-family: 'Outfit', sans-serif;
        font-size: 3.5rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-shadow: 0 0 10px rgba(0, 180, 216, 0.5);
        background: -webkit-linear-gradient(#fff, #90e0ef);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .pulse-dot {
        height: 12px;
        width: 12px;
        background-color: #00ff88;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 10px #00ff88, 0 0 20px #00ff88;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(0, 255, 136, 0); }
        100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0); }
    }
    .scan-log-item {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px;
        transition: all 0.3s ease;
    }
    .scan-log-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    .scan-log-item:last-child {
        border-bottom: none;
    }
    .badge-masuk {
        background-color: rgba(40, 167, 69, 0.2);
        color: #00ff88;
        border: 1px solid rgba(40, 167, 69, 0.4);
    }
    .badge-pulang {
        background-color: rgba(23, 162, 184, 0.2);
        color: #90e0ef;
        border: 1px solid rgba(23, 162, 184, 0.4);
    }
    .scan-feedback {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(11, 19, 43, 0.95);
        z-index: 20;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        border-radius: 12px;
    }
    .scan-feedback.active {
        opacity: 1;
    }
</style>

<div class="container-fluid py-4">
    <div class="tv-container">
        
        <!-- Header Info -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="pulse-dot mr-3"></div>
                <h5 class="mb-0 font-weight-bold tracking-wider" style="letter-spacing: 2px; color: #90e0ef;">ALMAHIRA LOBBY MONITOR</h5>
            </div>
            <div>
                <span class="badge badge-light px-3 py-2 text-dark font-weight-bold" style="border-radius: 20px;">
                    <i class="fas fa-calendar-alt text-primary mr-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        <div class="row">
            <!-- Left Pane: Scanner Webcam -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="glass-panel p-4 h-100 d-flex flex-column">
                    <h5 class="font-weight-bold mb-3"><i class="fas fa-qrcode text-info mr-2"></i> Scanner Webcam Lobi</h5>
                    <p class="small text-light opacity-75 mb-3">Tunjukkan QR Code di kartu pegawai Anda ke depan kamera untuk presensi.</p>

                    <!-- Camera Select Dropdown -->
                    <div class="form-group mb-3">
                        <label class="text-xs text-muted mb-1 d-block">PILIH KAMERA WEBCAM</label>
                        <select id="cameraSelect" class="form-control custom-select bg-dark text-white border-0" style="border-radius: 8px;">
                            <option value="">Mencari kamera...</option>
                        </select>
                    </div>

                    <!-- Webcam Box -->
                    <div class="scanner-window position-relative flex-fill d-flex align-items-center justify-content-center" id="scannerContainer">
                        <div class="scanner-laser" id="scannerLaser"></div>
                        <div id="reader" style="width: 100%; height: 100%; border: none;"></div>
                        
                        <!-- Overlay Feedback Screen -->
                        <div class="scan-feedback" id="scanFeedback">
                            <div class="text-center p-3">
                                <div id="feedbackIcon" class="mb-3">
                                    <i class="fas fa-check-circle text-success fa-4x"></i>
                                </div>
                                <img id="feedbackAvatar" src="" class="rounded-circle border mb-2" style="width: 80px; height: 80px; object-fit: cover; display: none;">
                                <h4 id="feedbackName" class="font-weight-bold mb-1">Nama Pegawai</h4>
                                <p id="feedbackTitle" class="text-muted small mb-2">Jabatan</p>
                                <div id="feedbackBadge" class="badge px-3 py-2 font-weight-bold mb-0">MASUK - 08:00:00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Scan Status -->
                    <div class="mt-3 text-center">
                        <span id="scanStatusText" class="text-muted small font-italic"><i class="fas fa-video-slash mr-1"></i> Kamera belum aktif. Silakan pilih kamera di atas.</span>
                    </div>
                </div>
            </div>

            <!-- Right Pane: Real-time clock & Last Scans Log -->
            <div class="col-lg-6">
                <div class="glass-panel p-4 h-100 d-flex flex-column justify-content-between">
                    
                    <!-- Time & Fallback QR -->
                    <div class="row align-items-center mb-4">
                        <div class="col-7">
                            <h2 class="mb-0 text-muted text-xs tracking-wider" style="letter-spacing: 1px;">WAKTU SEKARANG</h2>
                            <div id="lobbyClock" class="clock-large">00:00:00</div>
                        </div>
                        <div class="col-5 text-right">
                            <div class="d-inline-block bg-white p-2 rounded shadow-sm" style="cursor: pointer;" title="Tampilkan QR Harian (untuk scan HP)">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $qrToken }}&margin=2" alt="Daily QR" style="width: 80px; height: 80px;">
                                <div class="text-dark font-weight-bold text-center mt-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">QR HP HARIAN</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Scans Log -->
                    <div class="flex-fill">
                        <h5 class="font-weight-bold mb-3"><i class="fas fa-list-alt text-primary mr-2"></i> 5 Presensi Terakhir Hari Ini</h5>
                        
                        <div class="glass-panel bg-dark p-2" style="border-radius: 12px; min-height: 250px;">
                            <div id="logList">
                                @forelse($lastScans as $scan)
                                <div class="scan-log-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $scan->avatar }}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="font-weight-bold text-white small">{{ $scan->nama }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $scan->jabatan }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge {{ $scan->tipe == 'MASUK' ? 'badge-masuk' : 'badge-pulang' }} px-2 py-1 text-xs font-weight-bold">
                                            {{ $scan->tipe }}
                                        </span>
                                        <div class="text-muted mt-1" style="font-size: 0.7rem;">{{ $scan->jam }}</div>
                                    </div>
                                </div>
                                @empty
                                <div id="noLogsNotice" class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-2x mb-2 opacity-50"></i>
                                    <p class="small mb-0">Belum ada aktivitas presensi lobi hari ini.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Audio synthesis context to play beep sounds without files
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    
    function playBeep(type) {
        try {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            
            if (type === 'success') {
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note (high beep)
                gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                osc.start();
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
                osc.stop(audioCtx.currentTime + 0.15);
            } else {
                osc.frequency.setValueAtTime(220, audioCtx.currentTime); // Low frequency
                gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                osc.start();
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                osc.stop(audioCtx.currentTime + 0.3);
            }
        } catch (e) {
            console.error("Audio error: ", e);
        }
    }

    // Live Clock
    function updateClock() {
        const now = new Date();
        const display = now.toLocaleTimeString('id-ID', { hour12: false });
        const clockEl = document.getElementById('lobbyClock');
        if (clockEl) clockEl.textContent = display;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Webcam Scanner Logic
    let html5QrCode;
    let cameraSelect = document.getElementById('cameraSelect');
    let scanStatusText = document.getElementById('scanStatusText');
    let scannerContainer = document.getElementById('scannerContainer');
    let scannerLaser = document.getElementById('scannerLaser');
    let scanFeedback = document.getElementById('scanFeedback');
    
    let isProcessing = false;

    // Get list of cameras
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length > 0) {
            cameraSelect.innerHTML = '<option value="">-- Pilih Kamera Webcam --</option>';
            devices.forEach((device, index) => {
                let label = device.label || `Kamera ${index + 1}`;
                cameraSelect.innerHTML += `<option value="${device.id}">${label}</option>`;
            });
            
            // Auto-select first camera and start scanning immediately for unattended lobi TV
            cameraSelect.value = devices[0].id;
            startScanning(devices[0].id);
            
            // Listen for changes
            cameraSelect.addEventListener('change', function() {
                if (this.value) {
                    startScanning(this.value);
                } else {
                    stopScanning();
                }
            });
        } else {
            cameraSelect.innerHTML = '<option value="">Kamera tidak terdeteksi</option>';
            scanStatusText.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal mendeteksi webcam. Hubungkan kamera dan refresh halaman.</span>';
        }
    }).catch(err => {
        console.error("Gagal mendeteksi kamera: ", err);
        cameraSelect.innerHTML = '<option value="">Kamera gagal diakses</option>';
        scanStatusText.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Hubungkan webcam & berikan izin akses kamera.</span>`;
    });

    function startScanning(cameraId) {
        if (html5QrCode) {
            stopScanning();
        }

        html5QrCode = new Html5Qrcode("reader");
        scanStatusText.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin mr-1"></i> Menghubungkan ke kamera...</span>';

        html5QrCode.start(
            cameraId, 
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        ).then(() => {
            scanStatusText.innerHTML = '<span class="text-success"><i class="fas fa-video mr-1"></i> Kamera aktif. Silakan scan kartu Anda.</span>';
            scannerContainer.classList.add('scanner-active-border');
            scannerLaser.style.display = 'block';
        }).catch(err => {
            console.error("Gagal menjalankan kamera: ", err);
            scanStatusText.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Gagal membuka kamera: ${err}</span>`;
        });
    }

    function stopScanning() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                scanStatusText.innerHTML = '<span class="text-muted"><i class="fas fa-video-slash mr-1"></i> Kamera dinonaktifkan.</span>';
                scannerContainer.classList.remove('scanner-active-border');
                scannerLaser.style.display = 'none';
            }).catch(err => {
                console.error("Gagal menghentikan kamera: ", err);
            });
        }
    }

    // Triggered on successful QR detection
    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;

        // Perform attendance store via AJAX
        fetch("{{ route('absensi.manage.scan-card') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_token: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                playBeep('success');
                showFeedback(true, data);
                updateLogList(data);
            } else {
                playBeep('error');
                showFeedback(false, { message: data.message });
            }
        })
        .catch(err => {
            console.error("AJAX Error: ", err);
            playBeep('error');
            showFeedback(false, { message: 'Terjadi kesalahan jaringan/sistem.' });
        })
        .finally(() => {
            // Keep scan freeze for 3 seconds to let feedback overlay display, then resume
            setTimeout(() => {
                hideFeedback();
                isProcessing = false;
            }, 3000);
        });
    }

    function onScanFailure(error) {
        // Quietly fail scan attempts (helps ignore invalid scan frames)
    }

    function showFeedback(isSuccess, data) {
        let iconHtml = isSuccess 
            ? '<i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>' 
            : '<i class="fas fa-times-circle text-danger" style="font-size: 5rem;"></i>';
            
        document.getElementById('feedbackIcon').innerHTML = iconHtml;
        
        if (isSuccess) {
            document.getElementById('feedbackAvatar').src = data.employee.avatar;
            document.getElementById('feedbackAvatar').style.display = 'inline-block';
            document.getElementById('feedbackName').innerText = data.employee.nama;
            document.getElementById('feedbackTitle').innerText = data.employee.jabatan;
            
            let badgeEl = document.getElementById('feedbackBadge');
            badgeEl.className = 'badge px-3 py-2 font-weight-bold ' + (data.type === 'MASUK' ? 'badge-masuk' : 'badge-pulang');
            badgeEl.innerText = `${data.type} - ${data.time}`;
        } else {
            document.getElementById('feedbackAvatar').style.display = 'none';
            document.getElementById('feedbackName').innerText = 'Presensi Gagal';
            document.getElementById('feedbackTitle').innerText = data.message;
            document.getElementById('feedbackBadge').className = 'badge badge-danger px-3 py-2 font-weight-bold';
            document.getElementById('feedbackBadge').innerText = 'ERROR';
        }

        scanFeedback.classList.add('active');
    }

    function hideFeedback() {
        scanFeedback.classList.remove('active');
    }

    function updateLogList(data) {
        let logList = document.getElementById('logList');
        let noLogsNotice = document.getElementById('noLogsNotice');
        
        if (noLogsNotice) {
            noLogsNotice.remove();
        }

        // Create HTML for new log item
        let badgeClass = data.type === 'MASUK' ? 'badge-masuk' : 'badge-pulang';
        let newItemHtml = `
            <div class="scan-log-item d-flex align-items-center justify-content-between" style="opacity: 0; transform: translateY(-10px); transition: all 0.5s ease;">
                <div class="d-flex align-items-center">
                    <img src="${data.employee.avatar}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                    <div>
                        <div class="font-weight-bold text-white small">${data.employee.nama}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">${data.employee.jabatan}</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="badge ${badgeClass} px-2 py-1 text-xs font-weight-bold">
                        ${data.type}
                    </span>
                    <div class="text-muted mt-1" style="font-size: 0.7rem;">${data.time}</div>
                </div>
            </div>
        `;

        // Prepend to list
        logList.insertAdjacentHTML('afterbegin', newItemHtml);
        
        // Trigger animation
        let newEl = logList.querySelector('.scan-log-item');
        setTimeout(() => {
            newEl.style.opacity = '1';
            newEl.style.transform = 'translateY(0)';
        }, 100);

        // Keep list to maximum 5 items
        let items = logList.getElementsByClassName('scan-log-item');
        if (items.length > 5) {
            items[items.length - 1].remove();
        }
    }
</script>
@endpush
@endsection
