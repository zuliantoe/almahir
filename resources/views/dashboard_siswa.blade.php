@extends('layouts.app')

@section('title', 'Dashboard Santri — SIAKAD ALMAHIR')

@section('content')
@php
    $hariIni = \Carbon\Carbon::now()->translatedFormat('l');
    $tanggalIni = \Carbon\Carbon::now()->translatedFormat('d F Y');
@endphp

<div class="container-fluid pb-4">

    {{-- ═══ WELCOME CARD ═══ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%); color: #fff; overflow: hidden; position: relative;">
                <div style="position: absolute; top: -30px; right: -30px; width: 200px; height: 200px; background: rgba(255,255,255,0.04); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -50px; right: 80px; width: 150px; height: 150px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto d-none d-md-block">
                            <div style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); overflow: hidden; background: rgba(255,255,255,0.1);">
                                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4cc9f0&color=fff&size=80' }}"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col">
                            <p class="mb-1 small" style="color: rgba(255,255,255,0.6); letter-spacing: 1px; text-transform: uppercase; font-size: 0.7rem;">Ahlan Wa Sahlan</p>
                            <h3 class="font-weight-bold mb-1" style="color: #fff;">{{ Auth::user()->name }}</h3>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                <span class="badge" style="background: rgba(76, 201, 240, 0.25); color: #4cc9f0; border: 1px solid rgba(76,201,240,0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-id-card mr-1"></i> NIS: {{ $siswa->nis ?? '-' }}
                                </span>
                                <span class="badge" style="background: rgba(72, 149, 239, 0.25); color: #4895ef; border: 1px solid rgba(72,149,239,0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-school mr-1"></i> {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                                </span>
                                <span class="badge" style="background: rgba(40,167,69,0.25); color: #28a745; border: 1px solid rgba(40,167,69,0.4); padding: 4px 10px; border-radius: 20px; font-size: 0.72rem;">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            </div>
                        </div>
                        <div class="col-auto text-right d-none d-sm-block">
                            <div class="small mb-1" style="color: rgba(255,255,255,0.5); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">{{ $tahunAjaranAktif }}</div>
                            <div class="font-weight-bold" style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">{{ $hariIni }}, {{ $tanggalIni }}</div>
                            <div id="dashboard-clock" class="mt-1" style="color: #4cc9f0; font-size: 1.3rem; font-weight: 700; letter-spacing: 2px; font-family: 'Courier New', monospace;">00:00:00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ QUICK STATS ROW ═══ --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #28a745;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #28a745;">{{ $presensiHadir }}</div>
                    <div class="text-muted small font-weight-bold">Kehadiran</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Total Hadir</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #6f42c1;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #6f42c1;">{{ $rataNilai }}</div>
                    <div class="text-muted small font-weight-bold">Rata-Rata Nilai</div>
                    <div style="font-size: 0.7rem; color: #aaa;">dari {{ $totalNilai }} Catatan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid #20c997;">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: #20c997;">Rp {{ number_format($totalUangSaku, 0, ',', '.') }}</div>
                    <div class="text-muted small font-weight-bold">Saldo Uang Saku</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Yang Sudah Diterima</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 14px; border-top: 4px solid {{ $izinPending > 0 ? '#ffc107' : '#17a2b8' }};">
                <div class="card-body py-3 px-2">
                    <div class="mb-1" style="font-size: 1.8rem; font-weight: 800; color: {{ $izinPending > 0 ? '#ffc107' : '#17a2b8' }};">{{ $izinPending }}</div>
                    <div class="text-muted small font-weight-bold">Izin Pending</div>
                    <div style="font-size: 0.7rem; color: #aaa;">Menunggu Konfirmasi</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- ═══ KOLOM KIRI ═══ --}}
        <div class="col-lg-8">

            {{-- ─── MODUL AKADEMIK: Jadwal Hari Ini ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #17a2b8, #007bff);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-calendar-day fa-lg mr-3"></i>
                        <div>
                            <h5 class="font-weight-bold mb-0">Jadwal Pelajaran Hari Ini</h5>
                            <small class="opacity-75">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</small>
                        </div>
                    </div>
                    <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="btn btn-sm btn-light text-primary font-weight-bold rounded-pill px-3">
                        <i class="fas fa-external-link-alt mr-1"></i> Lihat Semua
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
                                        <th class="border-0 py-2 text-muted small text-uppercase" style="font-size: 0.72rem;">Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalHariIni as $j)
                                    <tr>
                                        <td class="px-4 py-2 align-middle">
                                            <span class="badge badge-primary rounded-circle" style="width: 28px; height: 28px; line-height: 19px; font-size: 0.8rem;">{{ $j->jamke }}</span>
                                        </td>
                                        <td class="py-2 align-middle text-nowrap text-muted small">{{ \Carbon\Carbon::parse($j->jamawal)->format('H:i') }} – {{ \Carbon\Carbon::parse($j->jamakhir)->format('H:i') }}</td>
                                        <td class="py-2 align-middle font-weight-bold text-dark">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</td>
                                        <td class="py-2 align-middle text-muted small">{{ $j->guru->nama ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">Tidak ada jadwal pelajaran hari ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── MODUL KEHADIRAN & NILAI: Statistik Presensi ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #28a745, #11998e);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-fingerprint fa-lg mr-3"></i>
                        <div>
                            <h5 class="font-weight-bold mb-0">Kehadiran & Nilai</h5>
                            <small class="opacity-75">Rekap Presensi & Penilaian Akademik</small>
                        </div>
                    </div>
                    <a href="{{ route('penilaiandanpresensi.presensi.siswa.index') }}" class="btn btn-sm btn-light text-success font-weight-bold rounded-pill px-3">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        {{-- Presensi Chart-style --}}
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-chart-pie mr-2 text-success"></i>Rekap Kehadiran</h6>
                            @php
                                $totalPresensi = $presensiHadir + $presensiSakit + $presensiIzin + $presensiAlpa;
                                $pctHadir = $totalPresensi > 0 ? round($presensiHadir / $totalPresensi * 100) : 0;
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small font-weight-bold"><i class="fas fa-check-circle text-success mr-1"></i> Hadir</span>
                                    <span class="small font-weight-bold text-success">{{ $presensiHadir }}x</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $pctHadir }}%; border-radius: 4px;"></div>
                                </div>
                            </div>
                            <div class="list-group list-group-flush mt-2">
                                <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="small"><i class="fas fa-ambulance text-warning mr-2"></i>Sakit</span>
                                    <span class="badge badge-warning badge-pill text-white px-3">{{ $presensiSakit }}</span>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="small"><i class="fas fa-envelope-open-text text-info mr-2"></i>Izin</span>
                                    <span class="badge badge-info badge-pill px-3">{{ $presensiIzin }}</span>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="small"><i class="fas fa-times-circle text-danger mr-2"></i>Alpa</span>
                                    <span class="badge badge-danger badge-pill px-3">{{ $presensiAlpa }}</span>
                                </div>
                            </div>
                        </div>
                        {{-- Nilai & Tahfidz --}}
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-graduation-cap mr-2 text-purple" style="color:#6f42c1;"></i>Nilai & Tahfidz</h6>
                            <div class="p-3 rounded mb-3" style="background: linear-gradient(135deg, #f8f0ff, #ede7ff); border-left: 4px solid #6f42c1;">
                                <div class="small text-muted mb-1">Rata-Rata Nilai Akademik</div>
                                <div class="d-flex align-items-end">
                                    <span class="font-weight-bold mr-2" style="font-size: 2rem; color: #6f42c1; line-height: 1;">{{ $rataNilai }}</span>
                                    <span class="text-muted small mb-1">/ 100 ({{ $totalNilai }} catatan)</span>
                                </div>
                            </div>
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #fff0f6, #ffe8f0); border-left: 4px solid #e83e8c;">
                                <div class="small text-muted mb-1"><i class="fas fa-quran mr-1" style="color:#e83e8c;"></i>Setoran Tahfidz Terakhir</div>
                                @if($lastTahfidz)
                                    <div class="font-weight-bold text-dark small">{{ $lastTahfidz->surat_awal ?? '-' }} ({{ $lastTahfidz->ayat_awal ?? '-' }}) s/d {{ $lastTahfidz->surat_akhir ?? '-' }} ({{ $lastTahfidz->ayat_akhir ?? '-' }})</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ optional($lastTahfidz->tanggal)->format('d M Y') ?? '' }}</div>
                                @else
                                    <div class="text-muted small">Belum ada catatan tahfidz.</div>
                                @endif
                            </div>
                            <div class="mt-2 d-flex" style="gap: 8px;">
                                <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-sm btn-outline-secondary flex-fill rounded-pill" style="font-size: 0.75rem;">
                                    <i class="fas fa-chart-bar mr-1"></i> Nilai
                                </a>
                                <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="btn btn-sm btn-outline-secondary flex-fill rounded-pill" style="font-size: 0.75rem;">
                                    <i class="fas fa-quran mr-1"></i> Tahfidz
                                </a>
                                <a href="{{ route('penilaiandanpresensi.izinsakit.siswa.index') }}" class="btn btn-sm btn-outline-warning flex-fill rounded-pill" style="font-size: 0.75rem; color: #fd7e14; border-color: #fd7e14;">
                                    <i class="fas fa-file-medical mr-1"></i> Izin
                                    @if($izinPending > 0)
                                        <span class="badge badge-warning text-white ml-1">{{ $izinPending }}</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══ KOLOM KANAN ═══ --}}
        <div class="col-lg-4">

            {{-- ─── KURIKULUM KELAS ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #6f42c1, #a020f0);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-book-open mr-2"></i>
                        <div>
                            <h6 class="font-weight-bold mb-0">Kurikulum Kelas</h6>
                            <small class="opacity-75" style="font-size: 0.7rem;">Daftar mapel & beban jam belajar</small>
                        </div>
                    </div>
                    <span class="badge badge-light" style="color: #6f42c1; font-size: 0.72rem;">{{ $kurikulumKelas->count() }} Mapel</span>
                </div>
                <div class="card-body p-0">
                    @if($kurikulumKelas->isNotEmpty())
                        @if($rombelInfo)
                        <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between" style="background: #faf8ff;">
                            <span class="small text-muted">Rombel: <strong class="text-dark">{{ $rombelInfo->nama_rombel }}</strong></span>
                            <span class="small text-muted">Wali: <strong>{{ $rombelInfo->walikelas->nama ?? '-' }}</strong></span>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 0.82rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 py-2 px-3 text-muted" style="font-size: 0.7rem;">Mata Pelajaran</th>
                                        <th class="border-0 py-2 text-center text-muted" style="font-size: 0.7rem;">JP</th>
                                        <th class="border-0 py-2 text-center text-muted" style="font-size: 0.7rem;">KKM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kurikulumKelas as $k)
                                    <tr>
                                        <td class="px-3 py-2 align-middle font-weight-bold text-dark">{{ $k->mataPelajaran->nama_mapel ?? '-' }}</td>
                                        <td class="py-2 align-middle text-center">
                                            <span class="badge badge-light border" style="color: #6f42c1;">{{ $k->totaljam ?? '-' }}</span>
                                        </td>
                                        <td class="py-2 align-middle text-center">
                                            <span class="badge rounded-pill px-3" style="background: {{ ($k->kkm ?? 0) >= 75 ? '#d4edda' : '#fff3cd' }}; color: {{ ($k->kkm ?? 0) >= 75 ? '#155724' : '#856404' }}; font-size: 0.7rem;">
                                                {{ $k->kkm ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-book fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">Belum ada data kurikulum.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── TEMAN SEKELAS ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #17a2b8, #4895ef);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-users mr-2"></i>
                        <h6 class="font-weight-bold mb-0">Teman Sekelas</h6>
                    </div>
                    <span class="badge badge-light" style="color: #17a2b8; font-size: 0.72rem;">{{ $temanSekelas->count() }} Orang</span>
                </div>
                <div class="card-body p-3">
                    @if($temanSekelas->isNotEmpty())
                        <div class="row" style="row-gap: 10px;">
                            @foreach($temanSekelas as $teman)
                            <div class="col-4">
                                <div class="text-center p-2 rounded" style="background: #f8f9fa;">
                                    <div class="mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white"
                                         style="width: 40px; height: 40px; font-size: 0.95rem; background: linear-gradient(135deg, #17a2b8, #4895ef);">
                                        {{ strtoupper(mb_substr($teman->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="small font-weight-bold text-dark text-truncate" style="font-size: 0.72rem;" title="{{ $teman->nama }}">{{ Str::words($teman->nama ?? '-', 2, '') }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-user-friends fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">Belum ada data teman sekelas.</p>
                        </div>
                    @endif
                </div>
            </div>


            {{-- ─── MODUL AKADEMIK: Kalender Event Terdekat ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4895ef, #3f37c9);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <h6 class="font-weight-bold mb-0">Kalender Akademik</h6>
                    </div>
                    <a href="{{ route('akademik.kalender-akademik.index') }}" class="btn btn-sm btn-light text-primary font-weight-bold rounded-pill px-3" style="font-size: 0.7rem;">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    @if($eventTerdekat->isNotEmpty())
                        @foreach($eventTerdekat as $ev)
                        <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                            <div class="text-center mr-3 flex-shrink-0" style="min-width: 46px; background: linear-gradient(135deg, #4895ef, #3f37c9); border-radius: 10px; padding: 6px 4px; color: #fff;">
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
                            <p class="small mb-0">Tidak ada event mendatang.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── MODUL KEUANGAN: Uang Saku ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #20c997, #00cec9);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-wallet mr-2"></i>
                        <h6 class="font-weight-bold mb-0">Uang Saku</h6>
                    </div>
                    <a href="{{ route('keuangan.uangsakus.index') }}" class="btn btn-sm btn-light rounded-pill px-3" style="font-size: 0.7rem; color: #20c997; font-weight: 700;">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    <div class="text-center py-2 mb-3" style="background: linear-gradient(135deg, #f0fff8, #e0fff0); border-radius: 12px;">
                        <div class="text-muted small font-weight-bold mb-1">Saldo Diterima</div>
                        <div class="font-weight-bold" style="font-size: 1.4rem; color: #20c997;">Rp {{ number_format($totalUangSaku, 0, ',', '.') }}</div>
                    </div>
                    @if($uangSakuTerbaru->isNotEmpty())
                        <h6 class="text-muted small font-weight-bold text-uppercase mb-2" style="letter-spacing: 0.5px;">Riwayat Terbaru</h6>
                        @foreach($uangSakuTerbaru as $us)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <div class="small font-weight-bold text-dark">{{ Str::limit($us->deskripsi ?? 'Uang Saku', 25) }}</div>
                                <div style="font-size: 0.7rem; color: #aaa;">{{ optional($us->tanggal)->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div class="font-weight-bold text-success small">+Rp {{ number_format($us->jumlah, 0, ',', '.') }}</div>
                                <span class="badge" style="font-size: 0.6rem; background: {{ $us->status === 'Sudah Diterima Santri' ? '#d4edda' : '#fff3cd' }}; color: {{ $us->status === 'Sudah Diterima Santri' ? '#155724' : '#856404' }}; border-radius: 20px; padding: 2px 8px;">
                                    {{ $us->status === 'Sudah Diterima Santri' ? 'Diterima' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-receipt fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">Belum ada catatan uang saku.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── MODUL ASRAMA: Kamar & Jadwal Piket ─── --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #fd7e14, #e83e8c);">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-hotel mr-2"></i>
                        <h6 class="font-weight-bold mb-0">Asrama</h6>
                    </div>
                    <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="btn btn-sm btn-light rounded-pill px-3" style="font-size: 0.7rem; color: #fd7e14; font-weight: 700;">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    {{-- Kamar Info --}}
                    <div class="p-3 rounded mb-3" style="background: linear-gradient(135deg, #fff5f0, #ffebe0); border-left: 4px solid #fd7e14;">
                        <div class="small text-muted mb-1"><i class="fas fa-door-open mr-1" style="color:#fd7e14;"></i>Kamar Hunian</div>
                        @if($kamarInfo)
                            <div class="font-weight-bold text-dark">{{ $kamarInfo->nama_kamar }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Gedung: {{ $kamarInfo->gedung ?? '-' }} • Kapasitas: {{ $kamarInfo->kapasitas ?? '-' }}</div>
                        @else
                            <div class="text-muted small">Belum ditempatkan di kamar.</div>
                        @endif
                    </div>
                    {{-- Jadwal Piket --}}
                    <h6 class="text-muted small font-weight-bold text-uppercase mb-2" style="letter-spacing: 0.5px;"><i class="fas fa-broom mr-1"></i>Jadwal Piket Mendatang</h6>
                    @if($piketMendatang->isNotEmpty())
                        @foreach($piketMendatang as $piket)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="text-center mr-3 flex-shrink-0" style="min-width: 42px; background: linear-gradient(135deg, #fd7e14, #e83e8c); border-radius: 8px; padding: 5px 4px; color: #fff;">
                                <div style="font-size: 1rem; font-weight: 800; line-height: 1;">{{ $piket->tanggal->format('d') }}</div>
                                <div style="font-size: 0.55rem; text-transform: uppercase;">{{ $piket->tanggal->translatedFormat('M') }}</div>
                            </div>
                            <div>
                                <div class="font-weight-bold text-dark small">Shift: {{ $piket->shift ?? '-' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $piket->lokasi_piket ?? '-' }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-calendar-times fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">Tidak ada jadwal piket mendatang.</p>
                        </div>
                    @endif
                    <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-sm btn-block rounded-pill mt-3 font-weight-bold" style="background: linear-gradient(135deg, #fd7e14, #e83e8c); color: #fff; font-size: 0.78rem;">
                        <i class="fas fa-list mr-1"></i> Lihat Semua Jadwal Piket
                    </a>
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
