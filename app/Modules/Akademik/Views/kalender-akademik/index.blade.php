@extends('layouts.app')

@section('title', 'Kalender Akademik')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <h1 class="h3 mb-3 mb-md-0 text-gray-800">Kalender Akademik</h1>
            <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                <x-btn :href="route('akademik.kalender-akademik.index', ['view' => 'calendar'])" class="btn-outline-primary" icon="fas fa-calendar-alt">
                    Mode Kalender
                </x-btn>
                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                <x-btn :href="route('akademik.kalender-akademik.create')" icon="fas fa-plus" class="ml-2">
                    Tambah
                </x-btn>
                @endif
            </div>
        </div>
    </div>

    {{-- Form Filter --}}
    <x-card title="Filter Kegiatan" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.kalender-akademik.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-4 mb-3">
                    <label>Tahun Ajaran</label>
                    <select name="tahunajaran_id" class="form-control">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahunajaran_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->tahunajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Cari Kegiatan</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nama kegiatan...">
                </div>

                <div class="col-md-4 mb-3 d-flex align-items-end" style="gap: 8px;">
                    <x-btn type="submit" class="btn-info flex-fill" icon="fas fa-search">
                        Filter
                    </x-btn>
                    @if(request()->anyFilled(['tahunajaran_id', 'search']))
                    <a href="{{ route('akademik.kalender-akademik.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel Data --}}
    <x-card title="Agenda Akademik" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Nama Kegiatan</th>
                        <th>Jenis</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                        <th width="150px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($kalenderAkademik as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kalenderAkademik->currentPage() - 1) * $kalenderAkademik->perPage() }}</td>
                        <td>
                            {{ $item->tahunAjaran->tahunajaran }}
                            @if($item->semester)
                                ({{ $item->semester }})
                            @endif
                        </td>
                        <td><strong>{{ $item->nama_kegiatan }}</strong></td>
                        <td>
                            <span class="badge badge-secondary">{{ optional($item->jenisKegiatan)->jeniskegiatan ?? '-' }}</span>
                            @if($item->jenisKegiatan && !$item->jenisKegiatan->is_kbm)
                                <span class="badge badge-danger ml-1" title="Hari ini diliburkan/tidak ada KBM">Non-KBM</span>
                            @endif
                        </td>

                        <td><i class="far fa-calendar-alt mr-1"></i> {{ date('d/m/Y', strtotime($item->tanggal_awal)) }}</td>
                        <td><i class="far fa-calendar-check mr-1"></i> {{ date('d/m/Y', strtotime($item->tanggal_akhir)) }}</td>
                        @if(Auth::check() && !Auth::user()->hasRole(['GURU', 'SISWA']))
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                <x-btn :href="route('akademik.kalender-akademik.show', $item->id)" size="sm" class="btn-info" title="Detail" style="margin: 0;">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.kalender-akademik.edit', $item->id)" size="sm" class="btn-warning" title="Edit" style="margin: 0;">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <form action="{{ route('akademik.kalender-akademik.destroy', $item->id) }}" method="POST" class="d-inline" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn type="submit" size="sm" class="btn-danger btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </x-btn>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->hasRole(['GURU', 'SISWA']) ? 6 : 7 }}" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-2x mb-3"></i><br>
                            Tidak ada data kalender akademik
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kalenderAkademik->hasPages())
        <x-slot name="footer">
            {{ $kalenderAkademik->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection

