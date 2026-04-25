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
                <h4 class="font-weight-bold mb-1 text-dark"><i class="fas fa-calendar-alt mr-2 text-primary"></i>Manajemen Jadwal Pelajaran</h4>
                <p class="text-muted small mb-0">Kelola jadwal pelajaran per rombel dan pengajar</p>
            </div>
            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            <x-btn :href="route('akademik.jadwal-pelajaran.create')" icon="fas fa-plus" class="btn-primary shadow-sm">
                Tambah Jadwal
            </x-btn>
            @elseif(Auth::check() && Auth::user()->hasRole('GURU'))
            <x-btn :href="route('akademik.jadwal-pelajaran.index')" icon="fas fa-calendar-alt" class="btn-info shadow-sm">
                Kembali ke Jadwal Saya
            </x-btn>
            @endif
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body py-3">
            <form action="{{ route('akademik.jadwal-pelajaran.index') }}" method="GET" class="row align-items-end g-2">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Rombel / Kelas</label>
                    <select name="rombel_id" class="form-control form-control-sm">
                        <option value="">— Semua Rombel —</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->nama_rombel }} ({{ optional($rombel->kelas)->nama_kelas }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Hari</label>
                    <select name="hari" class="form-control form-control-sm">
                        <option value="">— Semua Hari —</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1">Guru Pengajar</label>
                    <select name="guru_id" class="form-control form-control-sm">
                        <option value="">— Semua Guru —</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100" style="border-radius:8px;">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['rombel_id','hari','guru_id']))
                            <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="btn btn-sm btn-light" style="border-radius:8px;" title="Reset">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-header border-0 py-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-white-50 d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;background:rgba(255,255,255,0.2);">
                    <i class="fas fa-th-list text-white"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold text-white mb-0">Master Jadwal Pelajaran</h5>
                    <p class="text-white-50 small mb-0" style="opacity:0.8;">Total {{ $jadwalPelajaran->total() }} sesi terdaftar</p>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge badge-light text-primary rounded-pill px-3 py-2 font-weight-bold shadow-sm">
                    <i class="fas fa-calendar-check mr-1"></i> Tampilan Landscape
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.95rem; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f8f9fc;">
                            <th class="text-center py-4 px-3 border-0" width="60" style="color:#4e73df; text-transform:uppercase; font-size:0.75rem; letter-spacing:1px;">No</th>
                            <th class="py-4 px-3 border-0" style="color:#4e73df; text-transform:uppercase; font-size:0.75rem; letter-spacing:1px;">Rombel / Kelas</th>
                            <th class="py-4 px-3 border-0" style="color:#4e73df; text-transform:uppercase; font-size:0.75rem; letter-spacing:1px;">Hari & Waktu</th>
                            <th class="text-center py-4 px-3 border-0" style="color:#4e73df; text-transform:uppercase; font-size:0.75rem; letter-spacing:1px;">Jam</th>
                            <th class="py-4 px-3 border-0" style="color:#4e73df; text-transform:uppercase; font-size:0.75rem; letter-spacing:1px;">Mata Pelajaran</th>
                            <th class="py-4 px-3 border-0" style="color:#4e73df; text-transform:uppercase; font-size:0.75rem; letter-spacing:1px;">Guru Pengajar</th>
                            <th class="text-center py-4 px-3 border-0" width="160" style="color:#4e73df; text-transform:uppercase; font-size:0.75rem; letter-spacing:1px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwalPelajaran as $item)
                        @php
                            $hariColors = [
                                'Senin'  => ['bg' => '#eef2ff', 'text' => '#4338ca'],
                                'Selasa' => ['bg' => '#fff7ed', 'text' => '#c2410c'],
                                'Rabu'    => ['bg' => '#f0fdf4', 'text' => '#15803d'],
                                'Kamis'  => ['bg' => '#fdf2f8', 'text' => '#be185d'],
                                'Jumat'  => ['bg' => '#ecfeff', 'text' => '#0e7490'],
                                'Sabtu'   => ['bg' => '#f5f3ff', 'text' => '#6d28d9'],
                            ];
                            $h = $hariColors[$item->hari] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                        @endphp
                        <tr style="transition: all 0.2s ease;">
                            <td class="text-center align-middle py-4 text-muted font-weight-bold">{{ ($jadwalPelajaran->currentPage() - 1) * $jadwalPelajaran->perPage() + $loop->iteration }}</td>
                            <td class="align-middle py-4">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3 rounded bg-primary-soft d-flex align-items-center justify-content-center" style="width:40px; height:40px; background: rgba(78, 115, 223, 0.1);">
                                        <i class="fas fa-school text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size:1rem;">{{ optional($item->rombel)->nama_rombel ?? '-' }}</div>
                                        <div class="text-muted small">{{ optional(optional($item->rombel)->kelas)->nama_kelas }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle py-4">
                                <span class="badge px-3 py-2 rounded-lg font-weight-bold mb-1 d-inline-block" 
                                      style="background:{{ $h['bg'] }}; color:{{ $h['text'] }}; font-size:0.85rem;">
                                    <i class="far fa-calendar-alt mr-1"></i> {{ $item->hari }}
                                </span>
                                <div class="text-muted small ml-1">
                                    <i class="far fa-clock mr-1"></i> {{ substr($item->jamawal, 0, 5) }} – {{ substr($item->jamakhir, 0, 5) }}
                                </div>
                            </td>
                            <td class="text-center align-middle py-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded font-weight-bold shadow-sm" 
                                     style="width:38px; height:38px; background:white; border:1px solid #e3e6f0; color:#4e73df; font-size:1.1rem;">
                                    {{ $item->jamke }}
                                </div>
                            </td>
                            <td class="align-middle py-4">
                                <div class="font-weight-bold text-dark" style="font-size:0.95rem;">{{ optional($item->mataPelajaran)->nama ?? '-' }}</div>
                                <div class="text-muted small">ID: {{ optional($item->mataPelajaran)->kode }}</div>
                            </td>
                            <td class="align-middle py-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($item->guru)->nama ?? 'G') }}&color=4e73df&background=e8edf8" 
                                         class="rounded-circle mr-2 shadow-sm" style="width:32px; height:32px;">
                                    <span class="font-weight-bold text-dark small">{{ optional($item->guru)->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="text-center align-middle py-4">
                                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                                <div class="btn-group shadow-sm" style="border-radius:8px; overflow:hidden;">
                                    <a href="{{ route('akademik.jadwal-pelajaran.show', $item->id) }}" 
                                       class="btn btn-sm btn-white border-right text-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('akademik.jadwal-pelajaran.edit', $item->id) }}" 
                                       class="btn btn-sm btn-white border-right text-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('akademik.jadwal-pelajaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white text-danger btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <a href="{{ route('akademik.jadwal-pelajaran.show', $item->id) }}" class="btn btn-sm btn-light rounded-pill px-3">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width:120px; opacity:0.5;" class="mb-3">
                                <h6 class="text-muted font-weight-bold">Tidak ada data jadwal pelajaran</h6>
                                <p class="text-muted small">Coba sesuaikan filter pencarian Anda</p>
                                @if(request()->hasAny(['rombel_id','hari','guru_id']))
                                    <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">Reset Filter</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($jadwalPelajaran->hasPages())
        <div class="card-footer border-0 bg-white py-4 d-flex justify-content-center">
            {{ $jadwalPelajaran->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
