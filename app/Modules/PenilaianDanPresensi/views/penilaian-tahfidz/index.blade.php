@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Notifikasi otomatis via SweetAlert2 (Global Handler) --}}

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #ffc107 0%, #ffe066 100%);">
                <div class="card-body p-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="font-weight-bold mb-1"><i class="fas fa-quran mr-2"></i> Penilaian Tahfidz</h3>
                        <p class="mb-0 opacity-75">Pemantauan progres hafalan dan capaian santri</p>
                    </div>
                    @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                    <div class="ml-auto text-right">
                        <div class="badge badge-warning mb-2 p-2 shadow-sm" style="border-radius: 10px;">
                            <i class="fas fa-calendar-check mr-1"></i> TA: {{ $activeTahunAjaran->tahunajaran ?? '-' }}
                        </div>
                        <br>
                        <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.create') }}" class="btn btn-light text-warning px-4 font-weight-bold shadow-sm" style="border-radius: 50px;">
                            <i class="fas fa-plus-circle mr-2"></i> INPUT SETORAN
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
                <h5 class="mb-0 font-weight-bold text-warning"><i class="fas fa-filter mr-2"></i> Filter Setoran</h5>
                <button type="button" class="btn btn-light btn-sm" data-toggle="collapse" data-target="#filterTahfidz">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div id="filterTahfidz" class="collapse show">
            <div class="card-body pt-0">
                <form method="GET" class="row">
                    @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted">ROMBEL</label>
                        <select name="rombel_id" class="form-control select2-modern">
                            <option value="">Semua Rombel</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>{{ $rombel->nama_rombel }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">TAHUN AJARAN</label>
                        <select name="tahunajaran_id" class="form-control">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ request('tahunajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->tahunajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted">CARI SURAH</label>
                        <input type="text" name="surah" class="form-control" placeholder="Contoh: Al-Baqarah" value="{{ request('surah') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted">STATUS</label>
                        <select name="status_capaian" class="form-control">
                            <option value="">Semua</option>
                            <option value="Lolos" {{ request('status_capaian') == 'Lolos' ? 'selected' : '' }}>Lolos</option>
                            <option value="Tidak Lolos" {{ request('status_capaian') == 'Tidak Lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                        </select>
                    </div>
                    <div class="col-md-2 mt-auto mb-2">
                        <button type="submit" class="btn btn-warning btn-block font-weight-bold">
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
                    <thead style="background: #fffcf0;">
                        <tr>
                            <th class="border-0 px-4">Santri & Kelas</th>
                            <th class="border-0">Setoran (Surah/Ayat)</th>
                            <th class="border-0">Tanggal</th>
                            <th class="border-0 text-center">Nilai</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-center px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penilaianTahfidzs as $item)
                        <tr>
                            <td class="px-4">
                                <div class="font-weight-bold text-dark">{{ $item->siswa->nama ?? '-' }}</div>
                                <span class="badge badge-outline-warning text-warning border-warning" style="font-size: 0.7rem; border: 1px solid;">
                                    {{ $item->siswa->rombelSiswa->where('status', 'aktif')->first()->rombel->nama_rombel ?? $item->siswa->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($item->surat_awal == $item->surat_akhir && $item->ayat_awal == $item->ayat_akhir)
                                    <div class="text-dark font-weight-bold">{{ $item->surat_awal }}</div>
                                    <small class="text-muted">Ayat {{ $item->ayat_awal }}</small>
                                @else
                                    <div class="text-dark font-weight-bold">{{ $item->surat_awal }} ({{ $item->ayat_awal }})</div>
                                    <small class="text-muted"><i class="fas fa-long-arrow-alt-right mx-1"></i> {{ $item->surat_akhir }} ({{ $item->ayat_akhir }})</small>
                                @endif
                            </td>
                            <td>
                                <div class="text-muted"><i class="far fa-calendar-alt mr-1"></i> {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}</div>
                                <small class="text-muted">Guru: {{ $item->guru->nama ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-warning">{{ $item->nilai }}</div>
                            </td>
                            <td class="text-center">
                                @if($item->status_capaian == 'Lolos')
                                    <span class="badge badge-success px-3 py-2 shadow-sm" style="border-radius: 10px;">
                                        <i class="fas fa-check-circle mr-1"></i> Lolos
                                    </span>
                                @else
                                    <span class="badge badge-danger px-3 py-2 shadow-sm" style="border-radius: 10px;">
                                        <i class="fas fa-times-circle mr-1"></i> Tidak Lolos
                                    </span>
                                @endif
                            </td>
                            <td class="text-center px-4">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                    <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.show', $item->id) }}" class="btn btn-light btn-sm text-warning" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                                    <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.edit', $item->id) }}" class="btn btn-light btn-sm text-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('penilaiandanpresensi.penilaiantahfidz.destroy', $item->id) }}" method="POST" class="d-inline">
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
                                <i class="fas fa-quran fa-3x mb-3 d-block opacity-20 text-warning"></i>
                                <p class="text-muted">Belum ada data setoran tahfidz.</p>
                                @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                                    <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.create') }}" class="btn btn-warning btn-sm mt-2 font-weight-bold">Input Setoran Pertama</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">
                {{ $penilaianTahfidzs->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
