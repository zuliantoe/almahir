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
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
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

    {{-- Filter --}}
    <x-card title="Penyaringan Data" icon="fas fa-filter" outline collapsible class="shadow-sm border-0 rounded-xl mb-4">
        <form action="{{ route('akademik.jadwal-pelajaran.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
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
                <div class="col-md-2 mb-3">
                    <label class="filter-label">Hari</label>
                    <select name="hari" class="form-control select2-premium">
                        <option value="">— Semua —</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="filter-label">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control select2-premium">
                        <option value="">— Semua Mapel —</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="filter-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="form-control select2-premium">
                        <option value="">— Pilih Tahun —</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->tahunajaran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm transition-all hover-scale">
                        <i class="fas fa-search mr-2"></i> Terapkan
                    </button>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="filter-label">Guru Pengajar</label>
                    <select name="guru_id" class="form-control select2-premium">
                        <option value="">— Semua Guru —</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </x-card>

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
                        <th class="text-center">EST. TOTAL JP</th>
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
                            <div class="btn-group">
                                <x-btn :href="route('akademik.jadwal-pelajaran.show', $item->id)" size="sm" class="btn-light text-info border mr-1 rounded-circle" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                                <x-btn :href="route('akademik.jadwal-pelajaran.edit', $item->id)" size="sm" class="btn-light text-warning border mr-1 rounded-circle" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.jadwal-pelajaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn type="submit" size="sm" class="btn-light text-danger border rounded-circle btn-delete" title="Hapus">
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
    .transition-all { transition: all 0.2s ease-in-out; }
    .hover-scale:hover { transform: scale(1.05); }
    .table-premium thead th {
        background: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.08rem;
        color: #4e73df;
        border-top: 0;
        border-bottom: 1px solid #e3e6f0;
    }
    .hover-row:hover { background-color: #f8f9fc !important; transform: translateX(5px); }
    .badge-soft-primary { background-color: rgba(78, 115, 223, 0.1); color: #4e73df; }
    .badge-info-soft { background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; }
    .jam-ke-badge {
        width: 32px; height: 32px;
        background: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-weight: bold;
        box-shadow: 0 2px 6px rgba(78, 115, 223, 0.3);
    }
    .avatar-sm {
        width: 28px; height: 28px;
        background: #eaecf4;
        color: #4e73df;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.75rem;
    }
    .select2-premium { 
        height: 45px !important; 
        border-radius: 10px !important; 
        border: 1px solid #d1d3e2 !important;
        background-color: #fff !important;
        color: #4e73df !important;
        font-weight: 600 !important;
    }
    .filter-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #858796;
        margin-bottom: 0.5rem;
        letter-spacing: 0.05rem;
    }
</style>
</div>
@endsection
