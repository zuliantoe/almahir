@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    {{-- Welcome Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #4895ef 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h2 class="font-weight-bold mb-1">Assalamu'alaikum, {{ $siswa->nama }}!</h2>
                            <p class="mb-0 opacity-75">Sudahkah Anda bersyukur hari ini? Jangan lupa untuk melakukan absensi kehadiran.</p>
                        </div>
                        <div class="col-md-5 text-md-right mt-3 mt-md-0">
                            <div class="d-inline-block text-center mr-3 bg-white text-dark p-2 rounded-lg shadow-sm mb-2" style="min-width: 100px;">
                                <h3 class="mb-0 font-weight-bold" id="live-clock-siswa">00:00</h3>
                                <small class="text-uppercase font-weight-bold opacity-50" style="font-size: 0.6rem;">{{ now()->locale('id')->translatedFormat('d M Y') }}</small>
                            </div>
                            <div class="d-inline-block align-top">
                                <button type="button" class="btn btn-white px-4 mr-2 mb-2 shadow-sm text-primary font-weight-bold" style="border-radius: 50px; background: white;" data-toggle="modal" data-target="#qrScannerModal">
                                    <i class="fas fa-camera mr-2"></i> Scan QR
                                </button>
                                <button type="button" class="btn btn-outline-light px-4 mb-2" style="border-radius: 50px;" data-toggle="modal" data-target="#digitalCardModal">
                                    <i class="fas fa-qrcode mr-2"></i> Kartu Digital
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Today's Schedule --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-clock mr-2 text-primary"></i> Jadwal Pelajaran Hari Ini</h5>
                </div>
                <div class="card-body">
                    @forelse($jadwals as $jadwal)
                        @php
                            $isAbsent = isset($presensiHariIni[$jadwal->id]);
                            $presensi = $isAbsent ? $presensiHariIni[$jadwal->id] : null;
                        @endphp
                        <div class="jadwal-item p-3 mb-3 border d-flex align-items-center justify-content-between transition-all" style="border-radius: 15px; background: {{ $isAbsent ? 'rgba(40, 167, 69, 0.05)' : '#fff' }};">
                            <div class="d-flex align-items-center">
                                <div class="time-box p-2 text-center mr-3 shadow-sm" style="border-radius: 12px; min-width: 90px; background: {{ $isAbsent ? '#28a745' : '#4361ee' }}; color: white;">
                                    <span class="font-weight-bold d-block">{{ \Carbon\Carbon::parse($jadwal->jamawal)->format('H:i') }}</span>
                                    <small class="opacity-75" style="font-size: 0.7rem;">s/d {{ \Carbon\Carbon::parse($jadwal->jamakhir)->format('H:i') }}</small>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1 text-dark">{{ $jadwal->mataPelajaran->nama ?? 'Mata Pelajaran' }}</h6>
                                    <small class="text-muted"><i class="fas fa-user-tie mr-1"></i> {{ $jadwal->guru->nama ?? 'Guru Pengampu' }}</small>
                                </div>
                            </div>
                            <div>
                                @if($isAbsent)
                                    <div class="text-right">
                                        <span class="badge badge-success px-3 py-2" style="border-radius: 10px;">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Absen
                                        </span>
                                        <small class="d-block text-muted mt-1">Pukul: {{ \Carbon\Carbon::parse($presensi->jam)->format('H:i') }}</small>
                                    </div>
                                @else
                                    @php
                                        $isOver = date('H:i') > $jadwal->jamakhir;
                                    @endphp
                                    
                                    @if($isOver)
                                        <span class="badge badge-secondary px-3 py-2" style="border-radius: 10px; opacity: 0.7;">
                                            <i class="fas fa-hourglass-end mr-1"></i> Waktu Habis
                                        </span>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <form action="{{ route('penilaiandanpresensi.presensi.siswa.store') }}" method="POST" class="mr-2">
                                                @csrf
                                                <input type="hidden" name="jadwal_pelajaran_id" value="{{ $jadwal->id }}">
                                                <input type="hidden" name="guru_id" value="{{ $jadwal->guru_id }}">
                                                <input type="hidden" name="mapel_id" value="{{ $jadwal->mapel_id }}">
                                                <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px;">
                                                    <i class="fas fa-fingerprint mr-1"></i> Absen
                                                </button>
                                            </form>
                                            <a href="{{ route('penilaiandanpresensi.izinsakit.siswa.create', ['mapel_id' => $jadwal->mapel_id, 'tipe' => 'Per Matpel']) }}" class="btn btn-outline-info px-3 shadow-sm" style="border-radius: 10px;">
                                                <i class="fas fa-envelope mr-1"></i> Izin
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <img src="https://img.freepik.com/free-vector/no-data-concept-illustration_114360-536.jpg" alt="No Data" style="max-width: 150px; opacity: 0.6;">
                            <p class="text-muted mt-3">Tidak ada jadwal pelajaran untuk hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Attendance History --}}
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-history mr-2 text-primary"></i> Riwayat Presensi</h5>
                    <a href="#" class="btn btn-light btn-sm text-primary font-weight-bold" style="border-radius: 8px;">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4">Mata Pelajaran</th>
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPresensi as $item)
                                <tr>
                                    <td class="px-4">
                                        <div class="font-weight-bold text-dark">{{ $item->mataPelajaran->nama ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->guru->nama ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $item->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'Hadir' => 'badge-success',
                                                'Telat' => 'badge-warning',
                                                'Izin' => 'badge-info',
                                                'Sakit' => 'badge-primary',
                                                'Alpha' => 'badge-danger',
                                            ];
                                            $class = $statusClasses[$item->status] ?? 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $class }} px-3 py-2" style="border-radius: 8px; min-width: 70px;">{{ $item->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat presensi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($riwayatPresensi->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $riwayatPresensi->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Stats --}}
        <div class="col-lg-4">
            {{-- Advice Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: #1e1e2d; color: white;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-lightbulb fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-3">Tips Kehadiran</h5>
                    <p class="opacity-75 small mb-0">"Barangsiapa yang menempuh jalan untuk mencari ilmu, maka Allah akan mudahkan baginya jalan menuju surga." (HR. Muslim)</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-chart-line mr-2 text-primary"></i> Statistik Kehadiran</h5>
                    <small class="text-muted">Persentase kehadiran bulan ini</small>
                </div>
                <div class="card-body">
                    @forelse($attendanceStats as $stat)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="font-weight-bold text-dark">{{ $stat['nama_mapel'] }}</span>
                                <span class="font-weight-bold {{ $stat['percentage'] >= 80 ? 'text-success' : ($stat['percentage'] >= 60 ? 'text-warning' : 'text-danger') }}">
                                    {{ $stat['percentage'] }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 10px;">
                                <div class="progress-bar {{ $stat['percentage'] >= 80 ? 'bg-success' : ($stat['percentage'] >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $stat['percentage'] }}%; border-radius: 10px;" 
                                     aria-valuenow="{{ $stat['percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">{{ $stat['present'] }} Hadir dari {{ $stat['total'] }} pertemuan</small>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada data statistik.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Scan QR --}}
<div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="qrScannerModalLabel"><i class="fas fa-camera mr-2"></i> Scan QR Kehadiran</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted mb-4">Arahkan kamera ke QR Code atau unggah foto QR Code untuk melakukan absensi otomatis.</p>
                
                <div id="student-reader" style="width: 100%; border-radius: 15px; overflow: hidden; background: #f8f9fa;"></div>
                
                <div class="qr-content-display mt-3 d-none animate__animated animate__fadeIn">
                    <div class="bg-light p-3 rounded" style="border: 2px dashed #4361ee;">
                        <small class="text-primary font-weight-bold d-block mb-1">Konten Terdeteksi:</small>
                        <code id="decoded-qr-text" class="text-dark break-all" style="word-break: break-all;"></code>
                    </div>
                </div>

                <div id="scanner-status" class="mt-3 small font-weight-bold"></div>

                <div class="mt-4 pt-3 border-top">
                    <label for="qr-input-file" class="btn btn-outline-primary btn-block mb-0" style="border-radius: 10px; cursor: pointer;">
                        <i class="fas fa-image mr-2"></i> Scan dari Gambar
                    </label>
                    <input type="file" id="qr-input-file" accept="image/*" class="d-none">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Digital Card --}}
<div class="modal fade" id="digitalCardModal" tabindex="-1" role="dialog" aria-labelledby="digitalCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 25px; overflow: hidden; background: linear-gradient(135deg, #1e1e2d 0%, #2d2d44 100%); color: white;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="Logo" style="width: 60px; filter: brightness(0) invert(1);">
                    <h5 class="font-weight-bold mt-2">KARTU SANTRI DIGITAL</h5>
                    <p class="small opacity-50">Pesantren Digital Almahir</p>
                </div>
                
                <div class="bg-white p-3 d-inline-block shadow-lg mb-4" style="border-radius: 20px;">
                    <img id="my-qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $siswa->nis }}&margin=10" alt="QR Code" style="width: 200px; height: 200px;">
                </div>
                
                <h3 class="font-weight-bold mb-1">{{ $siswa->nama }}</h3>
                <h5 class="text-primary font-weight-bold mb-4">{{ $siswa->nis }}</h5>
                
                <div class="border-top border-white opacity-25 pt-4 mt-2">
                    <p class="small mb-0">Tunjukkan QR ini kepada petugas atau guru untuk melakukan presensi manual.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .jadwal-item {
        transition: all 0.3s ease;
    }
    .jadwal-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    $(document).ready(function() {
        let html5QrCode = null;

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                             now.getMinutes().toString().padStart(2, '0');
            $('#live-clock-siswa').text(timeString);
        }
        setInterval(updateClock, 1000);
        updateClock();
        
        $('#qrScannerModal').on('shown.bs.modal', function () {
            html5QrCode = new Html5Qrcode("student-reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            
            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                showDecodedText(decodedText);
                processStudentScan(decodedText);
            }).catch(err => {
                $('#scanner-status').html('<span class="text-danger">Kamera tidak aktif. Anda dapat menggunakan fitur "Scan dari Gambar".</span>');
            });
        });

        // File Scan Logic
        $('#qr-input-file').on('change', function(e) {
            if (e.target.files.length === 0) return;
            const imageFile = e.target.files[0];
            
            $('#scanner-status').html('<span class="text-primary"><i class="fas fa-spinner fa-spin mr-1"></i> Sedang membaca gambar...</span>');
            
            // Create a new instance for file scanning to avoid conflicts with camera
            const fileScanner = new Html5Qrcode("student-reader");
            
            fileScanner.scanFile(imageFile, true)
                .then(decodedText => {
                    showDecodedText(decodedText);
                    processStudentScan(decodedText);
                    $('#qr-input-file').val('');
                })
                .catch(err => {
                    console.warn("Scan file error:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Scan Gagal',
                        text: 'QR Code tidak terdeteksi. Pastikan gambar jelas dan berisi QR Code yang valid.',
                    });
                    $('#scanner-status').html('<span class="text-danger">Gagal membaca QR.</span>');
                    $('#qr-input-file').val('');
                });
        });

        function showDecodedText(text) {
            $('#decoded-qr-text').text(text);
            $('.qr-content-display').removeClass('d-none');
            $('#scanner-status').html('<span class="text-success"><i class="fas fa-check-circle mr-1"></i> QR Terdeteksi</span>');
        }

        $('#qrScannerModal').on('hidden.bs.modal', function () {
            $('.qr-content-display').addClass('d-none');
            $('#decoded-qr-text').text('');
            $('#scanner-status').html('');
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                });
            }
        });

        function processStudentScan(qrContent) {
            // Assume QR content is a JSON or specific string format like "jadwal_id|mapel_id|guru_id"
            // If it's just an ID, we might need to handle it differently.
            // For now, let's try to parse it.
            
            let data = {};
            try {
                // Try if it's JSON
                data = JSON.parse(qrContent);
            } catch(e) {
                // If not JSON, maybe pipe separated
                const parts = qrContent.split('|');
                if (parts.length >= 3) {
                    data = {
                        jadwal_pelajaran_id: parts[0],
                        mapel_id: parts[1],
                        guru_id: parts[2]
                    };
                } else {
                    // It might be just a NIS or other identifier
                    // We'll send it as is and let the server try auto-detection
                    data = { qr_content: qrContent };
                }
            }

            $.ajax({
                url: '{{ route("penilaiandanpresensi.presensi.siswa.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    $('#qrScannerModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Absensi Anda telah tercatat.',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res ? res.message : 'Terjadi kesalahan.',
                    });
                }
            });
        }
    });
</script>
@endpush
