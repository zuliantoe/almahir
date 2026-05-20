@extends('layouts.app')

@section('title', 'Dashboard Ustadz/Ustadzah — SIAKAD ALMAHIR')

@section('content')
@php
    $hariIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('l');
    $tanggalIni = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
@endphp

<div class="container-fluid pb-4">

    {{-- ═══ WELCOME CARD ═══ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%); color: #fff; overflow: hidden; position: relative;">
                <div style="position: absolute; top: -30px; right: -30px; width: 200px; height: 200px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -50px; right: 80px; width: 150px; height: 150px; background: rgba(255,255,255,0.02); border-radius: 50%;"></div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto d-none d-md-block">
                            <div style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.2); overflow: hidden; background: rgba(255,255,255,0.1);">
                                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=3b82f6&color=fff&size=80' }}"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col">
                            <p class="mb-1 small" style="color: rgba(255,255,255,0.6); letter-spacing: 1px; text-transform: uppercase; font-size: 0.7rem;">Ahlan Wa Sahlan, Ustadz/Ustadzah</p>
                            <h3 class="font-weight-bold mb-1" style="color: #fff;">{{ Auth::user()->name }}</h3>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                <span class="badge" style="background: rgba(59, 130, 246, 0.25); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-id-card mr-1"></i> NIP: {{ $guru->nip ?? '-' }}
                                </span>
                                <span class="badge" style="background: rgba(168, 85, 247, 0.25); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i> Pendidik & Pengajar
                                </span>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.25); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            </div>
                        </div>
                        <div class="col-auto text-right d-none d-sm-block">
                            <div class="small mb-1" style="color: rgba(255,255,255,0.5); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Tahun Ajaran {{ $tahunAjaranAktif }}</div>
                            <div class="font-weight-bold" style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">{{ $hariIni }}, {{ $tanggalIni }}</div>
                            <div id="dashboard-clock" class="mt-1" style="color: #60a5fa; font-size: 1.3rem; font-weight: 700; letter-spacing: 2px; font-family: 'Courier New', monospace;">00:00:00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ QUICK STATS ROW ═══ --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #3b82f6;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #3b82f6;">{{ $totalJadwal }}</div>
                    <div class="text-muted small font-weight-bold">Beban Mengajar</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Total Sesi / Minggu</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #10b981;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #10b981;">{{ $totalRombel }}</div>
                    <div class="text-muted small font-weight-bold">Kelas Diajar</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Rombongan Belajar</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid {{ $pendingIzinSiswa > 0 ? '#ef4444' : '#6b7280' }};">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: {{ $pendingIzinSiswa > 0 ? '#ef4444' : '#6b7280' }};">{{ $pendingIzinSiswa }}</div>
                    <div class="text-muted small font-weight-bold">Izin Siswa Pending</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Menunggu Konfirmasi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #8b5cf6;">
                <div class="card-body py-3 px-2 text-truncate">
                    @if($absensiHariIni)
                        <div class="mb-1 text-success font-weight-bold" style="font-size: 1rem; line-height: 1.8rem;">
                            <i class="fas fa-check-circle mr-1"></i>{{ $absensiHariIni->status }}
                        </div>
                        <div class="text-muted small font-weight-bold">Presensi Pegawai</div>
                        <div style="font-size: 0.7rem; color: #8b5cf6;">
                            Masuk: {{ $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : '--:--' }}
                        </div>
                    @else
                        <div class="mb-1 text-danger font-weight-bold" style="font-size: 1.1rem; line-height: 1.8rem;">
                            <i class="fas fa-times-circle mr-1"></i>Belum Masuk
                        </div>
                        <div class="text-muted small font-weight-bold">Presensi Pegawai</div>
                        <div style="font-size: 0.7rem; color: #aaa;">Presensi Hari Ini</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ SHORTCUT MENU GRID ═══ --}}
    <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-th-large mr-2 text-primary"></i>Menu Pintasan Guru</h5>
    <div class="row mb-4">
        {{-- 1. Kaldik --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('akademik.kalender-akademik.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-success" style="background: rgba(16, 185, 129, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Kaldik</span>
                </div>
            </a>
        </div>
        {{-- 2. Jadwal Mengajar --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-info" style="background: rgba(59, 130, 246, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Jadwal Mengajar</span>
                </div>
            </a>
        </div>
        {{-- Penilaian Akademik --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-purple" style="background: rgba(168, 85, 247, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Nilai Akademik</span>
                </div>
            </a>
        </div>
        {{-- Penilaian Tahfidz --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-pink" style="background: rgba(236, 72, 153, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-quran"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Nilai Tahfidz</span>
                </div>
            </a>
        </div>
        {{-- 3. Absensi Siswa --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-primary" style="background: rgba(79, 70, 229, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Absensi Siswa</span>
                </div>
            </a>
        </div>
        {{-- 4. Cetak Raport --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('penilaiandanpresensi.penilaianakademik.raport.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-warning" style="background: rgba(245, 158, 11, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Cetak Raport</span>
                </div>
            </a>
        </div>
        {{-- 5. Konfirmasi Izin Sakit --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-danger" style="background: rgba(239, 68, 68, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Konfirmasi Izin</span>
                </div>
            </a>
        </div>
        {{-- 6. Pengajuan Izin --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('perizinan.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-info" style="background: rgba(6, 182, 212, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Pengajuan Izin</span>
                </div>
            </a>
        </div>
        {{-- 7. Absensi Pegawai --}}
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
            <a href="{{ route('absensi.index') }}" class="card border-0 shadow-sm h-100 text-center shortcut-card py-3">
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-wrapper mb-2 text-purple" style="background: rgba(139, 92, 246, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <span class="font-weight-bold text-dark" style="font-size: 0.82rem;">Absensi Pegawai</span>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        {{-- ═══ JADWAL MENGAJAR HARI INI ═══ --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-chalkboard fa-lg mr-3"></i>
                        <div>
                            <h5 class="font-weight-bold mb-0">Jadwal Mengajar Hari Ini</h5>
                            <small class="opacity-75">{{ $hariIni }}, {{ $tanggalIni }}</small>
                        </div>
                    </div>
                    <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="btn btn-sm btn-light text-primary font-weight-bold rounded-pill px-3">
                        <i class="fas fa-external-link-alt mr-1"></i> Jadwal Lengkap
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($jadwalHariIni->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 py-2 px-4 text-muted small text-uppercase" style="font-size: 0.72rem;">Jam ke</th>
                                        <th class="border-0 py-2 text-muted small text-uppercase" style="font-size: 0.72rem;">Waktu</th>
                                        <th class="border-0 py-2 text-muted small text-uppercase" style="font-size: 0.72rem;">Mata Pelajaran</th>
                                        <th class="border-0 py-2 text-muted small text-uppercase" style="font-size: 0.72rem;">Kelas/Rombel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalHariIni as $j)
                                    <tr>
                                        <td class="px-4 py-2 align-middle">
                                            <span class="badge badge-primary rounded-circle" style="width: 28px; height: 28px; line-height: 19px; font-size: 0.8rem; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ $j->jamke }}
                                            </span>
                                        </td>
                                        <td class="py-2 align-middle text-nowrap text-muted small">
                                            {{ \Carbon\Carbon::parse($j->jamawal)->format('H:i') }} – {{ \Carbon\Carbon::parse($j->jamakhir)->format('H:i') }}
                                        </td>
                                        <td class="py-2 align-middle font-weight-bold text-dark">
                                            {{ $j->mataPelajaran->nama_mapel ?? '-' }}
                                        </td>
                                        <td class="py-2 align-middle">
                                            <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="border-radius: 4px;">
                                                {{ $j->rombel->nama_rombel ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">Tidak ada jadwal mengajar untuk hari ini.</p>
                            <small class="text-muted">Gunakan waktu luang Anda untuk mempersiapkan materi pembelajaran berikutnya.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══ KOLOM KANAN ═══ --}}
        <div class="col-lg-4 mb-4">
            {{-- Kalender Akademik --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <h6 class="font-weight-bold mb-0">Kalender Akademik</h6>
                    </div>
                    <a href="{{ route('akademik.kalender-akademik.index') }}" class="btn btn-sm btn-light text-success font-weight-bold rounded-pill px-3" style="font-size: 0.7rem;">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    @if($eventTerdekat->isNotEmpty())
                        @foreach($eventTerdekat as $ev)
                        <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                            <div class="text-center mr-3 flex-shrink-0" style="min-width: 46px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 10px; padding: 6px 4px; color: #fff;">
                                <div style="font-size: 1.1rem; font-weight: 800; line-height: 1;">{{ $ev->tanggal_awal->format('d') }}</div>
                                <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $ev->tanggal_awal->translatedFormat('M') }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-dark small">{{ $ev->nama_kegiatan }}</div>
                                @if($ev->deskripsi)
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ Str::limit($ev->deskripsi, 60) }}</div>
                                @endif
                                @if($ev->tanggal_awal->toDateString() !== $ev->tanggal_akhir->toDateString())
                                    <div style="font-size: 0.7rem; color: #999;">s/d {{ $ev->tanggal_akhir->translatedFormat('d M Y') }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-calendar-check fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">Tidak ada event terdekat mendatang.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Presensi Pegawai Card --}}
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-clock mr-2"></i>
                        <h6 class="font-weight-bold mb-0">Kehadiran Anda Hari Ini</h6>
                    </div>
                    <a href="{{ route('absensi.index') }}" class="btn btn-sm btn-light rounded-pill px-3" style="font-size: 0.7rem; color: #8b5cf6; font-weight: 700;">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="card-body p-3 text-center">
                    <div class="p-3 rounded mb-3" style="background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-left: 4px solid #8b5cf6;">
                        <div class="row">
                            <div class="col-6 border-right">
                                <span class="small text-muted d-block">Presensi Masuk</span>
                                <strong class="text-dark" style="font-size: 1.1rem;">
                                    {{ $absensiHariIni && $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : '--:--' }}
                                </strong>
                            </div>
                            <div class="col-6">
                                <span class="small text-muted d-block">Presensi Pulang</span>
                                <strong class="text-dark" style="font-size: 1.1rem;">
                                    {{ $absensiHariIni && $absensiHariIni->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : '--:--' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                    @if(!$absensiHariIni)
                        <p class="small text-muted mb-3"><i class="fas fa-info-circle mr-1 text-warning"></i>Anda belum melakukan presensi masuk hari ini.</p>
                        <a href="{{ route('absensi.create') }}" class="btn btn-block rounded-pill text-white font-weight-bold" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); font-size: 0.8rem;">
                            <i class="fas fa-sign-in-alt mr-1"></i> Lakukan Presensi Sekarang
                        </a>
                    @else
                        <div class="alert alert-success py-2 px-3 mb-0" style="border-radius: 30px; font-size: 0.8rem;">
                            <i class="fas fa-check-circle mr-1"></i> Anda telah tercatat <strong>{{ $absensiHariIni->status }}</strong> hari ini.
                        </div>
                    @endif
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
    .shortcut-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
        text-decoration: none !important;
    }
    .shortcut-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
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
