@extends('layouts.app')

@section('title', 'Dashboard Pegawai — SIAKAD ALMAHIR')

@section('content')
@php
    $hariIni = \Carbon\Carbon::now()->translatedFormat('l');
    $tanggalIni = \Carbon\Carbon::now()->translatedFormat('d F Y');
@endphp

<div class="container-fluid pb-4">

    {{-- ═══ WELCOME CARD ═══ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); color: #fff; overflow: hidden; position: relative;">
                <div style="position: absolute; top: -30px; right: -30px; width: 200px; height: 200px; background: rgba(255,255,255,0.04); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -50px; right: 80px; width: 150px; height: 150px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto d-none d-md-block">
                            <div style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); overflow: hidden; background: rgba(255,255,255,0.1);">
                                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=3a6073&color=fff&size=80' }}"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col">
                            <p class="mb-1 small" style="color: rgba(255,255,255,0.6); letter-spacing: 1px; text-transform: uppercase; font-size: 0.7rem;">Ahlan Wa Sahlan</p>
                            <h3 class="font-weight-bold mb-1" style="color: #fff;">{{ Auth::user()->name }}</h3>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                <span class="badge" style="background: rgba(0, 206, 201, 0.25); color: #00cec9; border: 1px solid rgba(0,206,201,0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-id-card mr-1"></i> NIP: {{ $pegawai->nip ?? '-' }}
                                </span>
                                <span class="badge" style="background: rgba(9, 132, 227, 0.25); color: #74b9ff; border: 1px solid rgba(9,132,227,0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-briefcase mr-1"></i> {{ $pegawai->typePegawai->nama_type ?? 'Pegawai' }}
                                </span>
                                <span class="badge" style="background: rgba(46, 204, 113, 0.25); color: #2ecc71; border: 1px solid rgba(46,204,113,0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-check-circle mr-1"></i> {{ ucfirst($pegawai->status ?? 'Aktif') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto text-right d-none d-sm-block">
                            <div class="small mb-1" style="color: rgba(255,255,255,0.5); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">TA: {{ $tahunAjaranAktif }}</div>
                            <div class="font-weight-bold" style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">{{ $hariIni }}, {{ $tanggalIni }}</div>
                            <div id="dashboard-clock" class="mt-1" style="color: #00cec9; font-size: 1.3rem; font-weight: 700; letter-spacing: 2px; font-family: 'Courier New', monospace;">00:00:00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ QUICK STATS ROW ═══ --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #2ecc71;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #2ecc71;">{{ $hadirCount }}</div>
                    <div class="text-muted small font-weight-bold">Kehadiran Bulan Ini</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Hari Kerja Efektif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #e74c3c;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #e74c3c;">{{ $terlambatCount }}</div>
                    <div class="text-muted small font-weight-bold">Total Terlambat</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Bulan Berjalan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #f1c40f;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #f1c40f;">{{ $izinCount }}</div>
                    <div class="text-muted small font-weight-bold">Izin/Cuti Disetujui</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #3498db;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #3498db;">{{ $sisaCuti }}</div>
                    <div class="text-muted small font-weight-bold">Sisa Jatah Cuti</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Kuota Tahunan Tersedia</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ═══ KOLOM KIRI ═══ --}}
        <div class="col-lg-8">
            {{-- ─── STATUS PRESENSI HARI INI ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3c72, #2a5298); color: #fff;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-fingerprint fa-lg mr-3"></i>
                        <div>
                            <h5 class="font-weight-bold mb-0">Status Presensi Hari Ini</h5>
                            <small class="opacity-75">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</small>
                        </div>
                    </div>
                    <a href="{{ route('absensi.create') }}" class="btn btn-sm btn-light text-primary font-weight-bold rounded-pill px-3">
                        <i class="fas fa-qrcode mr-1"></i> Scan QR
                    </a>
                </div>
                <div class="card-body p-4 text-center">
                    @if($todayAbsen)
                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0 border-right">
                                <div class="p-3 bg-light rounded" style="border-left: 4px solid #2ecc71;">
                                    <div class="text-muted small mb-1"><i class="fas fa-sign-in-alt text-success mr-1"></i>Jam Masuk</div>
                                    <h4 class="font-weight-bold mb-1 text-dark">
                                        {{ $todayAbsen->jam_masuk ? \Carbon\Carbon::parse($todayAbsen->jam_masuk)->format('H:i:s') : '-' }}
                                    </h4>
                                    <span class="badge badge-success px-3 py-1 rounded-pill">{{ $todayAbsen->status }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded" style="border-left: 4px solid {{ $todayAbsen->jam_pulang ? '#3498db' : '#f39c12' }};">
                                    <div class="text-muted small mb-1"><i class="fas fa-sign-out-alt text-primary mr-1"></i>Jam Pulang</div>
                                    @if($todayAbsen->jam_pulang)
                                        <h4 class="font-weight-bold mb-1 text-dark">
                                            {{ \Carbon\Carbon::parse($todayAbsen->jam_pulang)->format('H:i:s') }}
                                        </h4>
                                        <span class="badge badge-primary px-3 py-1 rounded-pill">Selesai Kerja</span>
                                    @else
                                        <h4 class="font-weight-bold mb-1 text-warning">- : - : -</h4>
                                        <a href="{{ route('absensi.create') }}" class="btn btn-xs btn-outline-warning font-weight-bold rounded-pill px-3 mt-1">
                                            <i class="fas fa-fingerprint"></i> Scan Keluar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-4">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 80px; height: 80px;">
                                <i class="fas fa-fingerprint fa-3x opacity-50"></i>
                            </div>
                            <h5 class="font-weight-bold text-dark mb-1">Anda Belum Absen Hari Ini</h5>
                            <p class="text-muted small mb-3">Silakan gunakan tombol scan QR di bawah ini saat datang di lokasi sekolah.</p>
                            <a href="{{ route('absensi.create') }}" class="btn btn-primary font-weight-bold rounded-pill px-4" style="background: linear-gradient(135deg, #1e3c72, #2a5298); border: none;">
                                <i class="fas fa-qrcode mr-1"></i> Presensi Masuk Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── RIWAYAT PERIZINAN TERBARU ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-envelope-open-text fa-lg mr-3"></i>
                        <div>
                            <h5 class="font-weight-bold mb-0">Riwayat Pengajuan Cuti & Izin</h5>
                            <small class="opacity-75">Daftar pengajuan izin/cuti terbaru</small>
                        </div>
                    </div>
                    <a href="{{ route('perizinan.index') }}" class="btn btn-sm btn-light text-success font-weight-bold rounded-pill px-3">
                        <i class="fas fa-external-link-alt mr-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($riwayatIzin->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="vertical-align: middle;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 py-3 px-4 text-muted small text-uppercase" style="font-size: 0.72rem;">Jenis Izin</th>
                                        <th class="border-0 py-3 text-muted small text-uppercase" style="font-size: 0.72rem;">Tanggal Pelaksanaan</th>
                                        <th class="border-0 py-3 text-muted small text-uppercase" style="font-size: 0.72rem;">Alasan</th>
                                        <th class="border-0 py-3 text-muted small text-uppercase text-center" style="font-size: 0.72rem;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatIzin as $izin)
                                    <tr>
                                        <td class="px-4 py-3 align-middle">
                                            @php
                                                $badgeClass = match($izin->jenis_izin) {
                                                    'izin' => 'badge-info',
                                                    'sakit' => 'badge-danger',
                                                    'cuti' => 'badge-primary',
                                                    'dinas luar' => 'badge-warning',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-2 py-1 rounded" style="font-size: 0.72rem; text-transform: uppercase;">{{ $izin->jenis_izin }}</span>
                                        </td>
                                        <td class="py-3 align-middle text-muted small text-nowrap">
                                            {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }}
                                            @if($izin->tanggal_mulai !== $izin->tanggal_selesai)
                                                s/d {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                                            @endif
                                            <br>
                                            <small class="text-secondary">({{ $izin->total_hari }} hari)</small>
                                        </td>
                                        <td class="py-3 align-middle text-dark font-weight-500" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $izin->alasan }}
                                        </td>
                                        <td class="py-3 align-middle text-center">
                                            @php
                                                $statusColor = match($izin->status) {
                                                    'disetujui' => 'badge-success',
                                                    'ditolak' => 'badge-danger',
                                                    'menunggu' => 'badge-warning',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusColor }} px-3 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                {{ ucfirst($izin->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">Belum ada pengajuan izin atau cuti.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══ KOLOM KANAN ═══ --}}
        <div class="col-lg-4">
            {{-- ─── QR CODE ABSENSI DIGITAL ─── --}}
            <div class="card border-0 shadow-sm mb-4 text-center" style="border-radius: 16px; overflow: hidden; border-top: 5px solid var(--primary-color);">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-qrcode text-primary mr-1"></i> Kartu Presensi Digital</h5>
                    <p class="text-muted small mb-3">Tunjukkan QR Code ini ke webcam lobi untuk melakukan presensi masuk/pulang.</p>
                    
                    @if($pegawai && $pegawai->qr_token)
                        <div class="d-inline-block p-3 bg-white rounded shadow-sm border mb-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $pegawai->qr_token }}&margin=2" alt="QR Presensi" style="width: 150px; height: 150px;">
                        </div>
                        <div class="text-uppercase font-weight-bold text-secondary" style="font-size: 0.8rem; letter-spacing: 1px;">
                            ID: {{ substr($pegawai->qr_token, 0, 8) }}...
                        </div>
                    @else
                        <div class="alert alert-warning py-2 small mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i> QR Code belum digenerate. Silakan hubungi Admin.
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── MENU AKSI CEPAT ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex align-items-center" style="background: linear-gradient(135deg, #4f3b78, #3b2a56); color: #fff;">
                    <i class="fas fa-rocket mr-2"></i>
                    <h6 class="font-weight-bold mb-0">Aksi Cepat Pegawai</h6>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('absensi.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center py-3 px-2 rounded mb-2" style="background: #faf8ff; transition: all 0.2s;">
                            <div class="mr-3 bg-purple text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #6f42c1;">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Presensi Masuk/Pulang</h6>
                                <small class="text-muted">Scan barcode absensi di lobi</small>
                            </div>
                        </a>
                        <a href="{{ route('absensi.index') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center py-3 px-2 rounded mb-2" style="background: #faf8ff; transition: all 0.2s;">
                            <div class="mr-3 bg-blue text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #007bff;">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Riwayat Presensi</h6>
                                <small class="text-muted">Tinjau seluruh catatan kehadiran Anda</small>
                            </div>
                        </a>
                        <a href="{{ route('perizinan.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center py-3 px-2 rounded mb-2" style="background: #faf8ff; transition: all 0.2s;">
                            <div class="mr-3 bg-yellow text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #ffc107;">
                                <i class="fas fa-envelope-open-text text-white"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Ajukan Cuti / Izin</h6>
                                <small class="text-muted">Formulir pengajuan cuti, sakit, & dinas</small>
                            </div>
                        </a>
                        <a href="{{ route('perizinan.index') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center py-3 px-2 rounded" style="background: #faf8ff; transition: all 0.2s;">
                            <div class="mr-3 bg-teal text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #20c997;">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark">Daftar Perizinan Anda</h6>
                                <small class="text-muted">Cek status persetujuan dari pimpinan</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ─── CATATAN TATA TERTIB ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex align-items-center" style="background: linear-gradient(135deg, #17a2b8, #117a8b); color: #fff;">
                    <i class="fas fa-info-circle mr-2"></i>
                    <h6 class="font-weight-bold mb-0">Informasi & Tata Tertib</h6>
                </div>
                <div class="card-body p-3 text-secondary small">
                    <p class="mb-2"><i class="fas fa-clock mr-1 text-primary"></i> <strong>Jam Kerja Efektif:</strong> Presensi masuk dilakukan sebelum pukul 08:00 WIB.</p>
                    <p class="mb-2"><i class="fas fa-exclamation-circle mr-1 text-danger"></i> <strong>Keterlambatan:</strong> Segala bentuk keterlambatan tanpa surat tugas/dinas akan terakumulasi dan dipotong secara administratif.</p>
                    <p class="mb-0"><i class="fas fa-calendar-check mr-1 text-success"></i> <strong>Batas Pengajuan Cuti:</strong> Pengajuan izin non-darurat dan cuti tahunan maksimal diajukan H-3 hari sebelum tanggal cuti dimulai.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
    body { font-family: 'Outfit', sans-serif !important; }
    .opacity-50 { opacity: 0.5; }
    .opacity-75 { opacity: 0.75; }
</style>

@push('scripts')
<script>
    function updateDashboardClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        const s = String(now.getSeconds()).padStart(2,'0');
        const el = document.getElementById('dashboard-clock');
        if(el) el.textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateDashboardClock, 1000);
    updateDashboardClock();
</script>
@endpush
@endsection
