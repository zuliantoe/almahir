@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    {{-- Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Jadwal Pelajaran</h1>
                <p class="text-muted small mb-0">Atur dan pantau alokasi waktu pengajaran santri secara terpadu</p>
            </div>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.jadwal-pelajaran.create')" icon="fas fa-plus" class="btn-primary rounded-pill px-4 shadow-sm">
                Tambah Jadwal Massal
            </x-btn>
            @elseif(Auth::check() && Auth::user()->hasRole('GURU'))
            <x-btn :href="route('akademik.jadwal-pelajaran.index')" icon="fas fa-calendar-alt" class="btn-info rounded-pill px-4">
                Kembali ke Jadwal Saya
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Filter & Duplikasi --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <x-card title="Penyaringan Data" icon="fas fa-filter" outline collapsible class="shadow-sm border-0 rounded-xl h-100">
                <form action="{{ route('akademik.jadwal-pelajaran.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="filter-label">Rombel / Kelas</label>
                            <select name="rombel_id" class="form-control select2-premium">
                                <option value="">— Semua Rombel —</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                        {{ $rombel->nama_rombel }} ({{ optional($rombel->kelas)->nama_kelas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="filter-label">Hari</label>
                            <select name="hari" class="form-control select2-premium">
                                <option value="">— Semua —</option>
                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                    <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="filter-label">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control select2-premium">
                                <option value="">— Semua Mapel —</option>
                                @foreach($mapels as $m)
                                    <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="filter-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-control select2-premium">
                                <option value="">— Pilih Tahun —</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->tahunajaran }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="filter-label">Guru Pengajar</label>
                            <select name="guru_id" class="form-control select2-premium">
                                <option value="">— Semua Guru —</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2" style="gap: 8px;">
                        @if(request()->anyFilled(['rombel_id','hari','mapel_id','tahun_ajaran_id','guru_id']))
                        <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-undo mr-1"></i> Reset Filter
                        </a>
                        @endif
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="fas fa-search mr-2"></i> Cari Jadwal
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
        
        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
        <div class="col-lg-4 mt-3 mt-lg-0">
            <x-card title="Duplikasi Semua Jadwal Rombel" icon="fas fa-copy" type="success" outline collapsible class="shadow-sm border-0 rounded-xl h-100">
                <form action="{{ route('akademik.jadwal-pelajaran.copy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyalin semua jadwal dari rombel asal ke rombel tujuan?')">
                    @csrf
                    <div class="mb-3">
                        <label class="filter-label">Rombel Asal (Sumber data)</label>
                        <select name="from_rombel_id" class="form-control select2-premium" required>
                            <option value="">— Pilih Rombel Asal —</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}">{{ $rombel->nama_rombel }} ({{ optional($rombel->kelas)->nama_kelas }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="filter-label">Rombel Tujuan (Sasaran salin)</label>
                        <select name="to_rombel_id" class="form-control select2-premium" required>
                            <option value="">— Pilih Rombel Tujuan —</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}">{{ $rombel->nama_rombel }} ({{ optional($rombel->kelas)->nama_kelas }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm w-100">
                            <i class="fas fa-paste mr-2"></i> Proses Duplikasi
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
        @endif
    </div>

    @if(request()->filled('rombel_id') && count($summaryJP) > 0)
    <x-alert type="info" dismissible>
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle mr-2 fa-lg"></i>
            <span>
                Estimasi total JP per semester dihitung berdasarkan rentang tanggal di <strong>Kalender Akademik</strong> dikurangi hari libur (Non-KBM).
            </span>
        </div>
    </x-alert>
    @endif

    {{-- Tabel Data --}}
    <x-card title="Daftar Jadwal Pelajaran" type="primary" outline class="shadow-lg border-0 rounded-xl overflow-hidden">
        <div class="table-responsive">
            <table class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="60">NO</th>
                        <th>ROMBEL / KELAS</th>
                        <th>HARI & WAKTU</th>
                        <th class="text-center">JAM</th>
                        <th>MATA PELAJARAN</th>
                        <th class="text-center"></th>
                        <th>GURU PENGAJAR</th>
                        <th class="text-center" width="120">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalPelajaran as $item)
                    <tr class="transition-all hover-row">
                        <td class="text-center font-weight-bold text-muted small">
                            {{ ($jadwalPelajaran->currentPage() - 1) * $jadwalPelajaran->perPage() + $loop->iteration }}
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold text-dark">{{ optional($item->rombel)->nama_rombel ?? '-' }}</span>
                                <span class="small text-muted">{{ optional(optional($item->rombel)->kelas)->nama_kelas }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="badge badge-primary px-3 py-1 rounded-pill mb-1 shadow-sm" style="width: fit-content;">{{ $item->hari }}</span>
                                <span class="small text-secondary font-weight-bold"><i class="far fa-clock mr-1 text-primary"></i> {{ substr($item->jamawal, 0, 5) }} – {{ substr($item->jamakhir, 0, 5) }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="jam-ke-badge">{{ $item->jamke }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold text-primary">{{ optional($item->mataPelajaran)->nama ?? '-' }}</span>
                                <span class="badge badge-light border small px-2 py-0" style="width: fit-content;">Kode: {{ optional($item->mataPelajaran)->kode }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if(isset($summaryJP[$item->mapel_id]))
                                <span class="badge badge-info-soft px-3 py-1 rounded-pill font-weight-bold">
                                    {{ $summaryJP[$item->mapel_id] }} JP
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm mr-2">{{ substr(optional($item->guru)->nama, 0, 1) }}</div>
                                <span class="small font-weight-bold text-dark">{{ optional($item->guru)->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                <x-btn :href="route('akademik.jadwal-pelajaran.show', $item->id)" size="sm" class="btn-light text-info border rounded-circle" title="Detail" style="margin: 0; padding: 6px 10px;">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                @if(Auth::check() && !Auth::user()->hasRole(['GURU', 'SISWA']))
                                <a href="{{ route('akademik.jadwal-pelajaran.create', [
                                    'rombel_id' => $item->rombel_id,
                                    'mapel_id' => $item->mapel_id,
                                    'guru_id' => $item->guru_id,
                                    'hari' => $item->hari,
                                    'jamke' => $item->jamke,
                                    'jamawal' => substr($item->jamawal, 0, 5),
                                    'jamakhir' => substr($item->jamakhir, 0, 5)
                                ]) }}" class="btn btn-sm btn-light text-success border rounded-circle" title="Duplikasi Jadwal" style="margin: 0; padding: 6px 10px;">
                                    <i class="fas fa-copy"></i>
                                </a>
                                <x-btn :href="route('akademik.jadwal-pelajaran.edit', $item->id)" size="sm" class="btn-light text-warning border rounded-circle" title="Edit" style="margin: 0; padding: 6px 10px;">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.jadwal-pelajaran.destroy', $item->id) }}" method="POST" class="d-inline" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn type="submit" size="sm" class="btn-light text-danger border rounded-circle btn-delete" title="Hapus" style="padding: 6px 10px;">
                                        <i class="fas fa-trash"></i>
                                    </x-btn>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times fa-4x text-light mb-3"></i>
                                <h5 class="text-muted">Tidak ada data jadwal pelajaran</h5>
                                <p class="text-muted small">Coba ubah filter atau tambahkan jadwal baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwalPelajaran->hasPages())
        <div class="p-3 border-top">
            {{ $jadwalPelajaran->withQueryString()->links() }}
        </div>
        @endif
    </x-card>

<style>
    .rounded-xl { border-radius: 1rem !important; }
    .transition-all { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-premium thead th {
        background: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.06rem;
        color: #5a738e;
        border-top: 0;
        border-bottom: 2px solid #eef2f6;
        font-weight: 700;
    }
    .hover-row:hover { 
        background-color: rgba(67, 97, 238, 0.02) !important; 
        transform: translateX(4px); 
    }
    .badge-soft-primary { background-color: rgba(67, 97, 238, 0.08); color: #4361ee; }
    .badge-info-soft { background-color: rgba(76, 201, 240, 0.12); color: #0077b6; }
    .jam-ke-badge {
        width: 28px; height: 28px;
        background: #eef2ff;
        color: #4361ee;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid #dbe2ff;
    }
    .avatar-sm {
        width: 32px; height: 32px;
        background: #f1f3f9;
        color: #4e5e7a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
        border: 1px solid #e1e5ef;
    }
    .select2-premium { 
        height: 42px !important; 
        border-radius: 8px !important; 
        border: 1px solid #e1e5ef !important;
        background-color: #fff !important;
        color: #4e5e7a !important;
        font-weight: 500 !important;
        font-size: 0.9rem !important;
        padding: 8px 12px !important;
    }
    .filter-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #8a8a8e;
        margin-bottom: 0.4rem;
        letter-spacing: 0.04rem;
    }
</style>
</div>
@endsection
