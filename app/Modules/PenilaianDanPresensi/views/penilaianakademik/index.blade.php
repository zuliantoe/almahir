@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    {{-- Notifikasi otomatis via SweetAlert2 (Global Handler) --}}

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #28a745 0%, #a7ff83 100%);">
                <div class="card-body p-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="font-weight-bold mb-1"><i class="fas fa-graduation-cap mr-2"></i> Penilaian Akademik</h3>
                        <p class="mb-0 opacity-75">Manajemen dan rekapitulasi nilai akademik santri</p>
                    </div>
                    @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                    <div class="ml-auto text-right">
                        <div class="btn-group mr-2">
                            <button class="btn btn-light shadow-sm text-primary dropdown-toggle font-weight-bold" style="border-radius: 50px;" data-toggle="dropdown">
                                <i class="fas fa-file-export mr-1"></i> EXPORT RAPORT
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('penilaiandanpresensi.penilaianakademik.export-excel', request()->all()) }}"><i class="fas fa-file-excel mr-2 text-success"></i> Excel (.xls)</a>
                            </div>
                        </div>
                        <a href="{{ route('penilaiandanpresensi.penilaianakademik.create') }}" class="btn btn-light px-4 font-weight-bold shadow-sm" style="border-radius: 50px; color: #28a745;">
                            <i class="fas fa-plus-circle mr-2"></i> TAMBAH NILAI
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold text-success"><i class="fas fa-filter mr-2"></i> Filter Nilai</h5>
                <button type="button" class="btn btn-light btn-sm" data-toggle="collapse" data-target="#filterBody">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div id="filterBody" class="collapse show">
            <div class="card-body pt-0">
                <form method="GET" class="row">
                    @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted">KELAS</label>
                        <select name="kelas_id" class="form-control select2-modern">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">MATA PELAJARAN</label>
                        <select name="mapel_id" class="form-control select2-modern">
                            <option value="">Semua Mapel</option>
                            @foreach($allMapels as $mapel)
                                <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted">JENIS NILAI</label>
                        <select name="jenis_nilai" class="form-control select2-modern">
                            <option value="">Semua Jenis</option>
                            <option value="Harian" {{ request('jenis_nilai') == 'Harian' ? 'selected' : '' }}>Harian</option>
                            <option value="UTS" {{ request('jenis_nilai') == 'UTS' ? 'selected' : '' }}>UTS</option>
                            <option value="UAS" {{ request('jenis_nilai') == 'UAS' ? 'selected' : '' }}>UAS</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">TAHUN AJARAN</label>
                        <select name="tahunajaran_id" class="form-control select2-modern">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ request('tahunajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->tahunajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-auto mb-2">
                        <button type="submit" class="btn btn-success btn-block font-weight-bold">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th class="border-0 px-4">Santri</th>
                            <th class="border-0">Mata Pelajaran</th>
                            <th class="border-0 text-center">Jenis</th>
                            <th class="border-0 text-center">KKM</th>
                            <th class="border-0 text-center">Nilai</th>
                            <th class="border-0 text-center px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penilaianAkademiks as $item)
                        <tr>
                            <td class="px-4">
                                <div class="font-weight-bold text-dark">{{ $item->siswa->nama ?? '-' }}</div>
                                <small class="badge badge-outline-success text-success border-success" style="font-size: 0.7rem; border: 1px solid;">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="text-dark font-weight-500">{{ $item->mataPelajaran->nama ?? '-' }}</div>
                                <small class="text-muted">{{ $item->tahunAjaran->tahunajaran ?? '-' }}</small>
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $badgeColor = 'badge-info';
                                    if($item->jenis_nilai == 'UTS') $badgeColor = 'badge-warning';
                                    if($item->jenis_nilai == 'UAS') $badgeColor = 'badge-danger';
                                @endphp
                                <span class="badge {{ $badgeColor }} px-3 py-2" style="border-radius: 8px;">{{ $item->jenis_nilai ?? 'Harian' }}</span>
                            </td>
                            <td class="text-center"><span class="font-weight-bold text-muted">{{ $item->kkm ?? '-' }}</span></td>
                            <td class="text-center">
                                <div class="h5 mb-0 font-weight-bold {{ $item->nilai >= ($item->kkm ?? 75) ? 'text-success' : 'text-danger' }}">
                                    {{ $item->nilai }}
                                </div>
                            </td>
                            <td class="text-center px-4">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.show', $item->id) }}" class="btn btn-light btn-sm text-primary" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.edit', $item->id) }}" class="btn btn-light btn-sm text-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('penilaiandanpresensi.penilaianakademik.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm text-danger btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-20"></i>
                                <p class="text-muted">Belum ada data penilaian akademik.</p>
                                @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                                    <a href="{{ route('penilaiandanpresensi.penilaianakademik.create') }}" class="btn btn-primary btn-sm mt-2">Tambah Data</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($penilaianAkademiks->hasPages())
            <div class="px-4 py-3 bg-light border-top d-flex justify-content-start">
                {{ $penilaianAkademiks->appends(request()->all())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
