@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    {{-- ===== TOP STATS ROW ===== --}}
    <div class="row mb-4">
        @if(!$isAdmin && isset($sisaCuti))
        @php
            $pctCuti = $sisaCuti > 0 ? round(($sisaCuti / 12) * 100) : 0;
            $colorCuti = $sisaCuti >= 7 ? '#28a745' : ($sisaCuti >= 4 ? '#ffc107' : '#dc3545');
        @endphp
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="glass-card hover-elevate p-3 h-100 border-0" style="border-left: 5px solid {{ $colorCuti }} !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-muted small font-weight-bold mb-1">Sisa Jatah Cuti</div>
                        <div class="h2 font-weight-bolder mb-0" style="color: {{ $colorCuti }};">
                            {{ $sisaCuti }} <span class="h6 text-muted font-weight-normal">/ 12 Hari</span>
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:{{ $colorCuti }}18;">
                        <i class="fas fa-umbrella-beach fa-lg" style="color:{{ $colorCuti }};"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height:5px;border-radius:10px;">
                    <div class="progress-bar" style="width:{{ $pctCuti }}%;background:{{ $colorCuti }};border-radius:10px;"></div>
                </div>
                <small class="text-muted">{{ 12 - $sisaCuti }} hari telah terpakai tahun ini</small>
            </div>
        </div>
        @endif

        {{-- Stat cards berdasarkan data yg tampil (real-time dari filter) --}}
        @php
            $totalMenunggu  = $perizinan->where('status', 'menunggu')->count();
            $totalDisetujui = $perizinan->where('status', 'disetujui')->count();
            $totalDitolak   = $perizinan->where('status', 'ditolak')->count();
            $colSize        = $isAdmin ? 4 : 3;
        @endphp

        <div class="col-12 col-sm-6 col-md-{{ $colSize }} mb-3">
            <div class="glass-card hover-elevate p-3 h-100 border-0" style="border-left:5px solid #ffc107 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-muted small font-weight-bold mb-1">Menunggu</div>
                        <div class="h2 font-weight-bolder mb-0 text-warning">{{ $totalMenunggu }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(255,193,7,.12);">
                        <i class="fas fa-clock fa-lg text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-{{ $colSize }} mb-3">
            <div class="glass-card hover-elevate p-3 h-100 border-0" style="border-left:5px solid #28a745 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-muted small font-weight-bold mb-1">Disetujui</div>
                        <div class="h2 font-weight-bolder mb-0 text-success">{{ $totalDisetujui }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(40,167,69,.12);">
                        <i class="fas fa-check-circle fa-lg text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-{{ $colSize }} mb-3">
            <div class="glass-card hover-elevate p-3 h-100 border-0" style="border-left:5px solid #dc3545 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-muted small font-weight-bold mb-1">Ditolak</div>
                        <div class="h2 font-weight-bolder mb-0 text-danger">{{ $totalDitolak }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(220,53,69,.12);">
                        <i class="fas fa-times-circle fa-lg text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN CARD ===== --}}
    <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header gradient-primary border-0 p-4">
            <h3 class="card-title text-white font-weight-bold mb-0 mt-1">
                <i class="fas fa-envelope-open-text mr-2"></i> {{ $title }}
            </h3>
            <div class="card-tools">
                @if(!$isAdmin)
                <a href="{{ route('perizinan.create') }}" class="btn btn-light btn-sm rounded-pill px-4 shadow-sm btn-animate font-weight-bold" style="color:#007bff;">
                    <i class="fas fa-plus mr-1"></i> Ajukan Izin Baru
                </a>
                @endif
            </div>
        </div>

        <div class="card-body p-4 bg-light">

            {{-- ===== FILTER FORM ===== --}}
            <div class="glass-card p-3 mb-4 border-0">
                <form action="{{ route('perizinan.index') }}" method="GET" id="filterForm">
                    <div class="row align-items-center g-2">
                        {{-- Bulan --}}
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1"><i class="fas fa-calendar-alt mr-1 text-primary"></i> Bulan Pengajuan</label>
                            <input type="month" name="bulan" class="form-control border-0 shadow-sm" value="{{ request('bulan') }}" onchange="document.getElementById('filterForm').submit()">
                        </div>
                        {{-- Status --}}
                        <div class="col-12 col-md-2 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1"><i class="fas fa-tag mr-1 text-warning"></i> Status</label>
                            <select name="status" class="form-control border-0 shadow-sm" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Status</option>
                                <option value="menunggu"  {{ request('status') == 'menunggu'  ? 'selected' : '' }}>Menunggu</option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak"   {{ request('status') == 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        {{-- Keyword search --}}
                        <div class="col-12 col-md-5 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1"><i class="fas fa-search mr-1 text-info"></i> Kata Kunci</label>
                            <div class="input-group" style="border-radius:50px;overflow:hidden;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-0 text-muted px-3"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="search" class="form-control border-0" placeholder="Cari nama, jenis izin..." value="{{ request('search') }}" style="box-shadow:none;">
                            </div>
                        </div>
                        {{-- Buttons --}}
                        <div class="col-12 col-md-2">
                            <label class="small font-weight-bold text-muted mb-1 d-none d-md-block">&nbsp;</label>
                            <div class="d-flex" style="gap:6px;">
                                <button type="submit" class="btn btn-info rounded-pill px-3 shadow-sm btn-animate font-weight-bold flex-fill">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('perizinan.index') }}" class="btn btn-outline-secondary rounded-pill px-3" title="Reset filter">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- Active filter badges --}}
                    @if(request('search') || request('bulan') || request('status'))
                    <div class="mt-2 pt-2 border-top d-flex align-items-center flex-wrap" style="gap:6px;">
                        <small class="text-muted mr-1">Filter aktif:</small>
                        @if(request('bulan'))
                            <span class="badge badge-primary rounded-pill px-3 py-1">
                                <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse(request('bulan'))->translatedFormat('F Y') }}
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="badge badge-warning rounded-pill px-3 py-1">
                                <i class="fas fa-tag mr-1"></i> {{ ucfirst(request('status')) }}
                            </span>
                        @endif
                        @if(request('search'))
                            <span class="badge badge-info rounded-pill px-3 py-1">
                                <i class="fas fa-search mr-1"></i> "{{ request('search') }}"
                            </span>
                        @endif
                    </div>
                    @endif
                </form>
            </div>

            {{-- ===== TABLE ===== --}}
            <div class="table-responsive bg-white rounded shadow-sm border-0">
                <table class="table table-hover mb-0" style="font-size:0.9rem;">
                    <thead style="background:#f4f6f9;">
                        <tr>
                            <th class="border-0 text-muted font-weight-bold align-middle py-3 pl-4" style="white-space:nowrap;">Tanggal Pengajuan</th>
                            @if($isAdmin)
                            <th class="border-0 text-muted font-weight-bold align-middle">Pegawai</th>
                            @endif
                            <th class="border-0 text-muted font-weight-bold align-middle">Jenis</th>
                            <th class="border-0 text-muted font-weight-bold align-middle">Periode & Durasi</th>
                            <th class="border-0 text-muted font-weight-bold align-middle text-center">Status</th>
                            <th class="border-0 text-muted font-weight-bold align-middle text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perizinan as $item)
                        @php
                            $durasi = \Carbon\Carbon::parse($item->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($item->tanggal_selesai)) + 1;
                            $jenisColor = match($item->jenis_izin) {
                                'cuti'       => 'primary',
                                'sakit'      => 'danger',
                                'izin'       => 'warning',
                                'dinas luar' => 'info',
                                default      => 'secondary'
                            };
                            $jenisIcon = match($item->jenis_izin) {
                                'cuti'       => 'fa-umbrella-beach',
                                'sakit'      => 'fa-briefcase-medical',
                                'izin'       => 'fa-hand-paper',
                                'dinas luar' => 'fa-briefcase',
                                default      => 'fa-file'
                            };
                        @endphp
                        <tr class="border-bottom {{ $item->status == 'menunggu' ? 'table-warning-soft' : '' }}">
                            <td class="align-middle pl-4 py-3">
                                <div class="font-weight-bold text-dark">{{ $item->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $item->created_at->format('H:i') }} WIB</small>
                            </td>
                            @if($isAdmin)
                            <td class="align-middle py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold mr-2 flex-shrink-0"
                                         style="width:36px;height:36px;font-size:.85rem;background:linear-gradient(135deg,#667eea,#764ba2);">
                                        {{ strtoupper(substr($item->pegawai->nama ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="line-height:1.2;">{{ $item->pegawai->nama ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            @endif
                            <td class="align-middle py-3">
                                <span class="badge badge-{{ $jenisColor }} px-3 py-2 rounded-pill" style="font-size:.78rem;">
                                    <i class="fas {{ $jenisIcon }} mr-1"></i> {{ strtoupper($item->jenis_izin) }}
                                </span>
                            </td>
                            <td class="align-middle py-3">
                                <div class="font-weight-bold text-dark" style="white-space:nowrap;">
                                    {{ $item->tanggal_mulai->format('d M') }} — {{ $item->tanggal_selesai->format('d M Y') }}
                                </div>
                                <span class="badge badge-light text-muted border" style="font-size:.75rem;">
                                    <i class="fas fa-calendar-week mr-1"></i>{{ $durasi }} {{ $durasi > 1 ? 'hari' : 'hari' }}
                                </span>
                            </td>
                            <td class="align-middle text-center py-3">
                                @if($item->status == 'menunggu')
                                    <span class="badge badge-warning px-3 py-2 rounded-pill shadow-sm" style="font-size:.78rem;">
                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                    </span>
                                @elseif($item->status == 'disetujui')
                                    <span class="badge badge-success px-3 py-2 rounded-pill shadow-sm" style="font-size:.78rem;">
                                        <i class="fas fa-check mr-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="badge badge-danger px-3 py-2 rounded-pill shadow-sm" style="font-size:.78rem;">
                                        <i class="fas fa-times mr-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle text-center py-3">
                                <a href="{{ route('perizinan.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-animate shadow-sm">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 6 : 5 }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-4"></i>
                                    <strong>Tidak ada data perizinan</strong>
                                    @if(request()->hasAny(['search','bulan','status']))
                                        <br><small>Coba ubah atau hapus filter pencarian</small>
                                        <br><a href="{{ route('perizinan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mt-2"><i class="fas fa-undo mr-1"></i> Reset Filter</a>
                                    @else
                                        @if(!$isAdmin)
                                        <br><small class="text-muted">Anda belum pernah mengajukan izin.</small>
                                        <br><a href="{{ route('perizinan.create') }}" class="btn btn-sm btn-primary rounded-pill mt-2"><i class="fas fa-plus mr-1"></i> Ajukan Sekarang</a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($perizinan->hasPages())
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
                <small class="text-muted font-weight-bold">
                    Menampilkan <strong>{{ $perizinan->firstItem() }}–{{ $perizinan->lastItem() }}</strong> dari <strong>{{ $perizinan->total() }}</strong> data
                </small>
                <div>{{ $perizinan->links() }}</div>
            </div>
            @endif

        </div>{{-- end card-body --}}
    </div>
</div>

<style>
.table-warning-soft { background-color: rgba(255,193,7,.06); }
.opacity-4 { opacity: .4; }
</style>
@endsection
