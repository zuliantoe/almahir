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
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Pelajaran</h1>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.jadwal-pelajaran.create')" icon="fas fa-plus">
                Tambah Jadwal
            </x-btn>
            @elseif(Auth::check() && Auth::user()->hasRole('GURU'))
            <x-btn :href="route('akademik.jadwal-pelajaran.index')" icon="fas fa-calendar-alt" class="btn-info">
                Kembali ke Jadwal Saya
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Filter --}}
    <x-card title="Filter Jadwal" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.jadwal-pelajaran.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label>Rombel / Kelas</label>
                    <select name="rombel_id" class="form-control">
                        <option value="">— Semua Rombel —</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->nama_rombel }} ({{ optional($rombel->kelas)->nama_kelas }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Hari</label>
                    <select name="hari" class="form-control">
                        <option value="">— Semua Hari —</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control">
                        <option value="">— Semua Mapel —</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="form-control">
                        <option value="">— Semua Tahun —</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->tahunajaran }} - {{ $ta->semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <x-btn type="submit" class="btn-primary w-100" icon="fas fa-search">
                        Filter
                    </x-btn>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Guru Pengajar</label>
                    <select name="guru_id" class="form-control">
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
    <x-card title="Daftar Jadwal Pelajaran" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="text-center" width="60">No</th>
                        <th>Rombel / Kelas</th>
                        <th>Hari & Waktu</th>
                        <th class="text-center">Jam</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">Est. Total JP</th>
                        <th>Guru Pengajar</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalPelajaran as $item)
                    <tr>
                        <td class="text-center">{{ ($jadwalPelajaran->currentPage() - 1) * $jadwalPelajaran->perPage() + $loop->iteration }}</td>
                        <td>
                            <strong>{{ optional($item->rombel)->nama_rombel ?? '-' }}</strong><br>
                            <small class="text-muted">{{ optional(optional($item->rombel)->kelas)->nama_kelas }}</small>
                        </td>
                        <td>
                            <span class="badge badge-light border">{{ $item->hari }}</span><br>
                            <small class="text-muted"><i class="far fa-clock"></i> {{ substr($item->jamawal, 0, 5) }} – {{ substr($item->jamakhir, 0, 5) }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-primary rounded-circle" style="width: 25px; height: 25px; line-height: 20px;">{{ $item->jamke }}</span>
                        </td>
                        <td>
                            <strong>{{ optional($item->mataPelajaran)->nama ?? '-' }}</strong><br>
                            <small class="text-muted">Kode: {{ optional($item->mataPelajaran)->kode }}</small>
                        </td>
                        <td class="text-center">
                            @if(isset($summaryJP[$item->mapel_id]))
                                <span class="badge badge-info px-2 py-1">
                                    {{ $summaryJP[$item->mapel_id] }} JP
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="small font-weight-bold">{{ optional($item->guru)->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.jadwal-pelajaran.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                                <x-btn :href="route('akademik.jadwal-pelajaran.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.jadwal-pelajaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn type="submit" size="sm" class="btn-danger btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </x-btn>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <p class="text-muted mb-0">Tidak ada data jadwal pelajaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwalPelajaran->hasPages())
        <div class="mt-3">
            {{ $jadwalPelajaran->withQueryString()->links() }}
        </div>
        @endif
    </x-card>
</div>
@endsection
