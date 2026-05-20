@extends('layouts.app')

@section('title', 'Dashboard Santri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card glass-card hover-elevate overflow-hidden border-0 shadow-sm" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white;">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-4 font-weight-bold mb-3">أهلاً وسهلاً، {{ $siswa->nama }}!</h1>
                            <div class="d-flex flex-wrap mb-3" style="gap: 10px;">
                                @if($currentRombel)
                                    <div class="badge badge-light px-3 py-2 shadow-sm" style="border-radius: 30px; font-size: 0.9rem; color: #4361ee;">
                                        <i class="fas fa-layer-group mr-2"></i> Kelas: <strong>{{ $currentRombel->kelas->nama_kelas ?? '-' }}</strong>
                                    </div>
                                    <div class="badge badge-light px-3 py-2 shadow-sm" style="border-radius: 30px; font-size: 0.9rem; color: #4361ee;">
                                        <i class="fas fa-users mr-2"></i> Rombel: <strong>{{ $currentRombel->nama_rombel }}</strong>
                                    </div>
                                @else
                                    <div class="badge badge-warning px-3 py-2 shadow-sm" style="border-radius: 30px; font-size: 0.9rem; color: #856404;">
                                        <i class="fas fa-exclamation-triangle mr-2"></i> Belum terdaftar di Rombel
                                    </div>
                                @endif
                            </div>
                            <p class="lead mb-4 opacity-75">Semangat menuntut ilmu hari ini. Teruslah belajar dan berproses menjadi pribadi yang bermanfaat.</p>
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('penilaiandanpresensi.presensi.siswa.index') }}" class="btn btn-white btn-lg px-4 mr-3 mb-2 shadow-sm text-primary font-weight-bold" style="border-radius: 50px; background: white;">
                                    <i class="fas fa-fingerprint mr-2"></i> Absensi Saya
                                </a>
                                <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-outline-light btn-lg px-4 mb-2" style="border-radius: 50px;">
                                    <i class="fas fa-graduation-cap mr-2"></i> Lihat Nilai
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <i class="fas fa-user-graduate fa-10x opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-success-light p-4 mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Total Kehadiran</h5>
                    <h3 class="text-success font-weight-bold display-4">{{ $stats['kehadiran'] }}%</h3>
                    <p class="text-muted">Persentase kehadiran Anda bulan ini.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-info-light p-4 mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-hotel text-info fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Kamar Asrama</h5>
                    @if($kamarInfo)
                        <h3 class="text-info font-weight-bold" style="font-size: 2rem; margin-top: 15px;">{{ $kamarInfo->nama_kamar }}</h3>
                        <p class="text-muted mb-0">Gedung: {{ $kamarInfo->gedung ?? '-' }}</p>
                    @else
                        <h5 class="text-muted mt-3 mb-0">Belum ditempatkan di kamar.</h5>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-warning-light p-4 mx-auto mb-3" style="width: 80px; height: 80px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-broom text-warning fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-3">Jadwal Piket Hari Ini</h5>
                    @if($jadwalPiketHariIni && $jadwalPiketHariIni->isNotEmpty())
                        @foreach($jadwalPiketHariIni as $piket)
                            <div class="badge badge-warning p-2 mb-2 w-100" style="font-size: 1rem; color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba;">
                                <i class="fas fa-clipboard-check mr-2"></i> {{ $piket->lokasi_piket }} (Shift: {{ $piket->shift }})
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mt-3">Tidak ada jadwal piket hari ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Baris Tambahan untuk Data Modul Lain --}}
    <div class="row mt-4">
        {{-- Tagihan Keuangan --}}
        <div class="col-md-4 mb-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-danger-light p-4 mx-auto mb-3" style="width: 80px; height: 80px; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="fas fa-file-invoice-dollar text-danger fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Tagihan Belum Lunas</h5>
                    @if(isset($tagihanBelumLunas) && $tagihanBelumLunas > 0)
                        <h3 class="text-danger font-weight-bold" style="font-size: 1.8rem; margin-top: 15px;">Rp {{ number_format($tagihanBelumLunas, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0 small">Segera selesaikan pembayaran.</p>
                    @else
                        <h3 class="text-success font-weight-bold" style="font-size: 1.8rem; margin-top: 15px;">Rp 0</h3>
                        <p class="text-muted mb-0 small">Alhamdulillah, semua lunas!</p>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Hafalan Tahfidz Terakhir --}}
        <div class="col-md-4 mb-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-purple-light p-4 mx-auto mb-3" style="width: 80px; height: 80px; background-color: rgba(111, 66, 193, 0.1);">
                        <i class="fas fa-quran text-purple fa-2x" style="color: #6f42c1;"></i>
                    </div>
                    <h5 class="font-weight-bold mb-3">Hafalan Terakhir</h5>
                    @if(isset($hafalanTerbaru) && $hafalanTerbaru)
                        <div class="badge badge-purple p-2 mb-2 w-100" style="font-size: 1rem; color: #fff; background-color: #6f42c1;">
                            {{ $hafalanTerbaru->surah }} (Ayat {{ $hafalanTerbaru->ayat_awal }} - {{ $hafalanTerbaru->ayat_akhir }})
                        </div>
                        <p class="text-muted mb-0 mt-2">Nilai: <strong style="color: #6f42c1;">{{ $hafalanTerbaru->nilai }}</strong></p>
                    @else
                        <p class="text-muted mt-3">Belum ada data hafalan.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Uang Saku Terakhir --}}
        <div class="col-md-4 mb-4">
            <div class="card glass-card hover-elevate border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-success-light p-4 mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-wallet text-success fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Uang Saku Terakhir</h5>
                    @if(isset($uangSakuTerakhir) && $uangSakuTerakhir)
                        <h3 class="text-success font-weight-bold" style="font-size: 1.8rem; margin-top: 15px;">Rp {{ number_format($uangSakuTerakhir->jumlah, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0 small">Tanggal: {{ \Carbon\Carbon::parse($uangSakuTerakhir->tanggal)->format('d M Y') }}</p>
                    @else
                        <p class="text-muted mt-3">Belum ada riwayat uang saku.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-white:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(255,255,255,0.4); }
    .bg-primary-light { background-color: rgba(67, 97, 238, 0.1); }
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
    .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
</style>
@endsection
