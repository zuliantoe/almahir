@extends('layouts.app')

@section('title', $title)

@section('content')
<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        border-radius: 24px;
        transition: all 0.3s ease;
    }
    .profile-img-glow {
        border: 4px solid #fff;
        box-shadow: 0 0 20px rgba(67, 97, 238, 0.3);
        width: 90px;
        height: 90px;
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
        transition: all 0.3s ease;
    }
    .scanner-active-border {
        border-color: #4cc9f0 !important;
        box-shadow: 0 0 20px rgba(76, 201, 240, 0.6);
    }
    .scanner-laser {
        position: absolute;
        width: 100%;
        height: 3px;
        background-color: #00ff88;
        box-shadow: 0 0 15px #00ff88;
        top: 0;
        z-index: 10;
        animation: laserScan 2.5s linear infinite;
    }
    @keyframes laserScan {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }
    .status-badge {
        padding: 8px 24px;
        border-radius: 30px;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-panel p-0 overflow-hidden">
                <!-- Header -->
                <div class="p-4" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; text-align: center;">
                    <h3 class="font-weight-bold mb-0">
                        <i class="fas fa-fingerprint mr-2"></i> Presensi Harian
                    </h3>
                </div>
                
                <div class="p-4">
                    <!-- Profile Info -->
                    <div class="text-center mb-4">
                        <div class="d-inline-block position-relative mb-3 animate__animated animate__fadeIn">
                            <img src="{{ Auth::user()->avatar_url }}" class="rounded-circle profile-img-glow">
                        </div>
                        <h5 class="font-weight-bold mb-0 text-dark">{{ $pegawai->nama }}</h5>
                        <p class="text-muted small mb-0">{{ $pegawai->typePegawai->nama_type ?? 'Pegawai' }}</p>
                    </div>

                    <!-- Clock & Date -->
                    <div class="text-center mb-4 bg-light p-3 rounded-lg border" style="border-radius: 16px;">
                        <p class="text-muted mb-1 text-uppercase font-weight-bold small tracking-wide">{{ now()->translatedFormat('l, d F Y') }}</p>
                        <div id="realtime-clock" class="display-4 font-weight-bold" style="color: var(--primary-color); letter-spacing: 2px; font-family: 'Outfit', sans-serif;">00:00:00</div>
                    </div>

                    @if(!$currentAbsen || !$currentAbsen->jam_pulang)
                        
                        @if($currentAbsen && !$currentAbsen->jam_pulang)
                        {{-- SUDAH MASUK, BELUM PULANG --}}
                        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg" style="background: rgba(40, 167, 69, 0.08); border-left: 5px solid #28a745 !important; border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <h6 class="font-weight-bold mb-0 text-success">Absen Masuk Tercatat</h6>
                                    <small class="text-muted">Pukul: <strong>{{ \Carbon\Carbon::parse($currentAbsen->jam_masuk)->format('H:i') }}</strong> | <strong>{{ $currentAbsen->status }}</strong></small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-4">
                            <span class="status-badge bg-danger text-white shadow-sm animate__animated animate__pulse animate__infinite">
                                <i class="fas fa-sign-out-alt mr-1"></i> Waktunya Absen Pulang
                            </span>
                        </div>
                        @else
                        {{-- BELUM ABSEN MASUK --}}
                        <div class="alert alert-info border-0 shadow-sm mb-4 rounded-lg" style="background: rgba(67, 97, 238, 0.08); border-left: 5px solid var(--primary-color) !important; border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x text-primary mr-3"></i>
                                <div class="text-left">
                                    <h6 class="font-weight-bold mb-0 text-primary">Selamat Pagi!</h6>
                                    <small class="text-muted">Silakan scan QR Code di lobi untuk presensi masuk.</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Camera Select Dropdown Container -->
                        <div id="camera-select-container" class="form-group mb-3 text-left animate__animated animate__fadeIn" style="display: none;">
                            <label class="text-xs text-muted mb-1 d-block font-weight-bold text-uppercase"><i class="fas fa-video mr-1"></i> Pilih Kamera Scanner</label>
                            <select id="cameraSelect" class="form-control custom-select bg-white text-dark border" style="border-radius: 10px; font-weight: 500;">
                                <option value="">Mencari kamera...</option>
                            </select>
                        </div>

                        <!-- CUSTOM SCANNER WINDOW -->
                        <div class="scanner-frame" id="scannerContainer">
                            <div id="reader" style="width: 100%; min-height: 250px; background: #000; border-radius: 16px; overflow: hidden;"></div>
                            <div class="scanner-laser" id="scannerLaser" style="display: none;"></div>
                            
                            <!-- Scanner Placeholder Overlay -->
                            <div id="scannerPlaceholder" class="position-absolute d-flex flex-column align-items-center justify-content-center" style="top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); z-index: 5; border-radius: 16px;">
                                <div class="text-center p-3 text-white">
                                    <i class="fas fa-camera fa-3x mb-3 text-info animate__animated animate__pulse animate__infinite"></i>
                                    <h6 class="font-weight-bold">Kamera Belum Aktif</h6>
                                    <p class="small text-muted mb-3" style="max-width: 250px; margin: 0 auto;">Aktifkan kamera untuk memindai QR Code Presensi.</p>
                                    <button type="button" class="btn btn-info btn-sm rounded-pill px-4 font-weight-bold btn-animate" id="btnStartCamera">
                                        <i class="fas fa-power-off mr-1"></i> Aktifkan Kamera
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PREMIUM GPS STATUS CARD -->
                        <div class="card border mb-3 shadow-sm bg-light" style="border-radius: 12px; transition: all 0.3s ease;">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div id="gps-icon-container" class="bg-danger-light text-danger rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: rgba(220, 53, 69, 0.1); transition: all 0.3s;">
                                        <i id="gps-icon" class="fas fa-map-marker-alt animate__animated animate__bounce animate__infinite"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-weight-bold mb-0 text-dark" id="gps-status-title">Status GPS</h6>
                                        <small class="text-muted" id="gps-status-text">Menunggu kunci lokasi GPS aktif...</small>
                                    </div>
                                </div>
                                <div id="gps-spinner" class="spinner-border spinner-border-sm text-secondary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>

                        <form action="{{ !$currentAbsen ? route('absensi.store') : route('absensi.update') }}" method="POST" id="form-absen">
                            @csrf
                            <input type="hidden" name="lat" id="lat">
                            <input type="hidden" name="long" id="long">
                            <input type="hidden" name="qr_token" id="qr_token">
                            
                            @if(!$currentAbsen)
                            <div class="form-group mt-3 text-left">
                                <label class="small font-weight-bold text-muted"><i class="fas fa-sticky-note mr-1 text-warning"></i> Tambahkan Catatan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="2" style="border-radius: 10px; background: #f8f9fa; border: 1px solid #e1e5ef;" placeholder="Keterangan kondisi Anda hari ini..."></textarea>
                            </div>
                            @endif
                        </form>

                        <!-- FALLBACK EMERGENCY LINK -->
                        <div class="text-center mt-3 animate__animated animate__fadeIn">
                            <button type="button" class="btn btn-link btn-sm text-secondary font-weight-bold" data-toggle="modal" data-target="#modalManualDarurat">
                                <i class="fas fa-exclamation-triangle mr-1 text-warning"></i> Ada kendala scanner/GPS? Absen Manual Darurat
                            </button>
                        </div>

                    @else
                        {{-- SUDAH SELESAI HARI INI --}}
                        <div class="py-5 text-center">
                            <div class="mb-4 d-inline-block p-4 rounded-circle animate__animated animate__zoomIn" style="background: rgba(40, 167, 69, 0.1);">
                                <i class="fas fa-check-double fa-4x text-success"></i>
                            </div>
                            <h3 class="font-weight-bold text-dark">Presensi Hari Ini Selesai</h3>
                            <p class="text-muted px-3">Terima kasih atas dedikasi Anda. Sampai jumpa di hari kerja berikutnya!</p>
                            
                            <div class="d-flex justify-content-center mt-4">
                                <div class="bg-light p-3 rounded-lg mr-3 border text-center" style="min-width: 110px; border-radius: 12px;">
                                    <small class="d-block text-muted font-weight-bold text-xs">JAM MASUK</small>
                                    <h5 class="mb-0 text-dark font-weight-bold">{{ \Carbon\Carbon::parse($currentAbsen->jam_masuk)->format('H:i') }}</h5>
                                </div>
                                <div class="bg-light p-3 rounded-lg border text-center" style="min-width: 110px; border-radius: 12px;">
                                    <small class="d-block text-muted font-weight-bold text-xs">JAM PULANG</small>
                                    <h5 class="mb-0 text-dark font-weight-bold">{{ \Carbon\Carbon::parse($currentAbsen->jam_pulang)->format('H:i') }}</h5>
                                </div>
                            </div>

                            <div class="mt-5">
                                <a href="{{ route('absensi.index') }}" class="btn btn-primary rounded-pill px-5 shadow-sm btn-animate">
                                    <i class="fas fa-history mr-2"></i> Lihat Riwayat Absen
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div><!-- Modal Absen Manual Darurat -->
<div class="modal fade" id="modalManualDarurat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-warning text-white border-0 py-3 d-flex align-items-center">
                <h5 class="modal-title font-weight-bold mb-0 text-dark"><i class="fas fa-exclamation-triangle mr-2"></i> Presensi Manual Darurat</h5>
                <button type="button" class="close text-dark opacity-75 ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ !$currentAbsen ? route('absensi.store-self-manual') : route('absensi.update-self-manual') }}" method="POST" onsubmit="showOverlayManual()">
                @csrf
                <div class="modal-body p-4 bg-light text-left">
                    <div class="alert alert-warning border-0 small mb-3 text-dark" style="border-radius: 10px; background: rgba(255, 193, 7, 0.15); border-left: 4px solid #ffc107 !important;">
                        <i class="fas fa-info-circle mr-1"></i> <strong>PENTING:</strong> Presensi ini akan merekam waktu kehadiran Anda saat ini (secara real-time) di server. Anda wajib mengisi alasan kendala yang valid.
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small text-uppercase mb-2"><i class="fas fa-sticky-note mr-1 text-warning"></i> Tulis Alasan Kendala Presensi</label>
                        <textarea name="keterangan" class="form-control border-premium" rows="3" style="border-radius: 10px; padding: 10px;" placeholder="Contoh: Kamera webcam laptop bermasalah / GPS tidak bisa mengunci lokasi..." required minlength="5"></textarea>
                        <small class="text-muted mt-1 d-block">Minimal 5 karakter alasan agar dapat diproses.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark font-weight-bold rounded-pill px-4 shadow-sm btn-animate">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Kehadiran (Jam Sekarang)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FULL SCREEN PROCESS OVERLAY -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
    <div class="text-center p-4">
        <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Processing...</span>
        </div>
        <h4 class="font-weight-bold text-dark mb-1 animate__animated animate__pulse animate__infinite">Memproses Absensi Anda...</h4>
        <p class="text-muted">Mencocokkan koordinat GPS dan memverifikasi QR Code...</p>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
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
            
            // Update GPS Card UI to Success State
            const gpsIconContainer = document.getElementById('gps-icon-container');
            const gpsIcon = document.getElementById('gps-icon');
            const gpsStatusText = document.getElementById('gps-status-text');
            const gpsSpinner = document.getElementById('gps-spinner');
            
            if (gpsIconContainer) {
                gpsIconContainer.style.background = 'rgba(40, 167, 69, 0.1)';
                gpsIconContainer.className = gpsIconContainer.className.replace('text-danger', 'text-success');
                gpsIcon.className = 'fas fa-check-circle animate__animated animate__zoomIn text-success';
                gpsStatusText.innerText = 'Lokasi berhasil didapatkan secara akurat.';
                gpsStatusText.className = 'text-success font-weight-bold';
                gpsSpinner.style.display = 'none';
            }
            console.log("GPS Location Ready: " + currentLat + ", " + currentLong);
        }, function(error) {
            // Update GPS Card UI to Error State
            const gpsIconContainer = document.getElementById('gps-icon-container');
            const gpsIcon = document.getElementById('gps-icon');
            const gpsStatusText = document.getElementById('gps-status-text');
            const gpsSpinner = document.getElementById('gps-spinner');
            
            if (gpsIconContainer) {
                gpsIconContainer.style.background = 'rgba(220, 53, 69, 0.1)';
                gpsIcon.className = 'fas fa-exclamation-triangle text-danger';
                gpsStatusText.innerText = 'Gagal memuat GPS: ' + error.message;
                gpsStatusText.className = 'text-danger font-weight-bold';
                gpsSpinner.style.display = 'none';
            }
            Swal.fire('GPS Error', 'Gagal mendapatkan lokasi: ' + error.message + '. Pastikan GPS aktif di browser Anda.', 'error');
        }, { enableHighAccuracy: true });
    } else {
        const gpsStatusText = document.getElementById('gps-status-text');
        const gpsSpinner = document.getElementById('gps-spinner');
        if (gpsStatusText) {
            gpsStatusText.innerText = 'Browser tidak mendukung GPS/Geolokasi.';
            gpsStatusText.className = 'text-danger font-weight-bold';
            gpsSpinner.style.display = 'none';
        }
    }

    // QR Code Scanner Custom Logic
    @if(!$currentAbsen || !$currentAbsen->jam_pulang)
    let html5QrCode;
    let cameraSelect = document.getElementById('cameraSelect');
    let cameraSelectContainer = document.getElementById('camera-select-container');
    let scannerContainer = document.getElementById('scannerContainer');
    let scannerLaser = document.getElementById('scannerLaser');
    let scannerPlaceholder = document.getElementById('scannerPlaceholder');
    let btnStartCamera = document.getElementById('btnStartCamera');
    
    // Get list of cameras on page load
    console.log("Mulai mendeteksi kamera...");
    Html5Qrcode.getCameras().then(devices => {
        console.log("Kamera terdeteksi:", devices);
        if (devices && devices.length > 0) {
            cameraSelect.innerHTML = '<option value="">-- Pilih Kamera Scanner --</option>';
            devices.forEach((device, index) => {
                let label = device.label || `Kamera ${index + 1}`;
                cameraSelect.innerHTML += `<option value="${device.id}">${label}</option>`;
            });

            // If only one device, auto-select it in background
            if (devices.length === 1) {
                console.log("Hanya ada 1 kamera, auto-select:", devices[0].id);
                cameraSelect.value = devices[0].id;
            } else {
                console.log("Ada beberapa kamera, tampilkan dropdown pilihan.");
                cameraSelectContainer.style.display = 'block';
            }
            
            // Listen to selection changes
            cameraSelect.addEventListener('change', function() {
                console.log("Pilihan kamera berubah ke:", this.value);
                if (this.value) {
                    startScanning(this.value);
                } else {
                    stopScanning();
                }
            });

            // If there's an auto-selected camera, click start starts it
            btnStartCamera.addEventListener('click', function() {
                console.log("Tombol Aktifkan Kamera diklik.");
                let selectedCameraId = cameraSelect.value;
                if (!selectedCameraId) {
                    console.log("Tidak ada kamera terpilih di dropdown, pilih kamera pertama:", devices[0].id);
                    selectedCameraId = devices[0].id;
                    cameraSelect.value = selectedCameraId;
                }
                startScanning(selectedCameraId);
            });
        } else {
            console.warn("Tidak ada kamera terdeteksi.");
            cameraSelect.innerHTML = '<option value="">Kamera tidak terdeteksi</option>';
            btnStartCamera.disabled = true;
            btnStartCamera.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Kamera Tidak Ada';
            Swal.fire('Scanner Error', 'Tidak ada perangkat kamera/webcam yang terdeteksi untuk melakukan scan.', 'warning');
        }
    }).catch(err => {
        console.error("Gagal mendeteksi kamera: ", err);
        cameraSelect.innerHTML = '<option value="">Kamera gagal diakses</option>';
    });

    function startScanning(cameraId) {
        console.log("startScanning dipanggil untuk cameraId:", cameraId);
        
        if (html5QrCode) {
            console.log("Menghapus instance scanner sebelumnya sebelum membuat baru...");
            html5QrCode.stop().then(() => {
                console.log("Scanner sebelumnya berhasil dihentikan. Memulai instance baru...");
                html5QrCode = null;
                runScannerInstance(cameraId);
            }).catch(err => {
                console.error("Gagal menghentikan scanner sebelumnya:", err);
                html5QrCode = null;
                runScannerInstance(cameraId);
            });
        } else {
            runScannerInstance(cameraId);
        }
    }

    function runScannerInstance(cameraId) {
        console.log("Menjalankan instance scanner baru untuk:", cameraId);
        html5QrCode = new Html5Qrcode("reader");
        
        // Sembunyikan placeholder dan tampilkan laser scan
        // Karena ada class Bootstrap 'd-flex' (yang memiliki display: flex !important),
        // kita harus menghapus class tersebut agar display: none bisa diterapkan.
        scannerPlaceholder.classList.remove('d-flex');
        scannerPlaceholder.style.display = 'none';
        scannerLaser.style.display = 'block';
        scannerContainer.classList.add('scanner-active-border');

        html5QrCode.start(
            cameraId, 
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        ).then(() => {
            console.log("Kamera berhasil menyala dan mulai memindai!");
        }).catch(err => {
            console.error("Gagal menjalankan kamera: ", err);
            // Kembalikan tampilan placeholder jika gagal menyala
            scannerPlaceholder.classList.add('d-flex');
            scannerPlaceholder.style.display = 'flex';
            scannerLaser.style.display = 'none';
            scannerContainer.classList.remove('scanner-active-border');
            Swal.fire('Kamera Error', 'Gagal menyalakan kamera: ' + err, 'error');
        });
    }

    function stopScanning() {
        if (html5QrCode) {
            console.log("Menghentikan scanner...");
            html5QrCode.stop().then(() => {
                console.log("Scanner berhasil dihentikan.");
                scannerPlaceholder.classList.add('d-flex');
                scannerPlaceholder.style.display = 'flex';
                scannerLaser.style.display = 'none';
                scannerContainer.classList.remove('scanner-active-border');
                html5QrCode = null;
            }).catch(err => {
                console.error("Gagal menghentikan kamera: ", err);
            });
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        console.log("QR Code berhasil dideteksi:", decodedText);
        if (!currentLat || !currentLong) {
            Swal.fire('Tunggu', 'Sedang mencari lokasi GPS Anda...', 'warning');
            return;
        }
        
        // Stop scanning immediately to prevent multiple scans
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode = null;
                console.log("Scanner dihentikan setelah scan sukses.");
                submitScanResult(decodedText);
            }).catch(err => {
                console.error("Gagal menghentikan scanner setelah sukses:", err);
                html5QrCode = null;
                submitScanResult(decodedText);
            });
        } else {
            submitScanResult(decodedText);
        }
    }

    function submitScanResult(decodedText) {
        // Show Process Overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Put decoded text into input
        document.getElementById('qr_token').value = decodedText;
        
        // Submit Form via AJAX
        const form = document.getElementById('form-absen');
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            return response.json().then(data => {
                return { status: response.status, data: data };
            });
        })
        .then(res => {
            // Hide Process Overlay
            document.getElementById('loadingOverlay').style.display = 'none';
            
            if (res.status === 200 && res.data.success) {
                // Success SweetAlert (Uniform with layouts/app.blade.php)
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.data.message,
                    confirmButtonColor: 'var(--primary-color)',
                    timer: 2500,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                }).then(() => {
                    // Redirect to history index page
                    window.location.href = "{{ route('absensi.index') }}";
                });
            } else {
                // Error SweetAlert (Uniform with layouts/app.blade.php)
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: res.data.message || 'Terjadi kesalahan saat memproses absensi.',
                    confirmButtonColor: '#ef233c',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                }).then(() => {
                    // Resume/restart camera scan
                    let selectedCameraId = cameraSelect.value;
                    if (selectedCameraId) {
                        startScanning(selectedCameraId);
                    } else {
                        window.location.reload();
                    }
                });
            }
        })
        .catch(err => {
            console.error("AJAX Error:", err);
            document.getElementById('loadingOverlay').style.display = 'none';
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal terhubung ke server. Silakan periksa koneksi internet Anda.',
                confirmButtonColor: '#ef233c',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then(() => {
                window.location.reload();
            });
        });
    }

    function onScanFailure(error) {
        // Quiet failure to avoid flooding console during camera frame capture
    }

    function showOverlayManual() {
        $('#modalManualDarurat').modal('hide');
        document.getElementById('loadingOverlay').style.display = 'flex';
    }
    @endif
</script>
@endpush
@endsection
