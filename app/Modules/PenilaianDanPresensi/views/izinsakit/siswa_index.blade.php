@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h2 class="font-weight-bold mb-1"><i class="fas fa-envelope-open-text mr-2"></i> Izin & Sakit</h2>
                            <p class="mb-0 opacity-75">Kelola pengajuan izin dan sakit Anda di sini.</p>
                        </div>
                        <div class="col-md-5 text-md-right mt-3 mt-md-0">
                            <a href="{{ route('penilaiandanpresensi.izinsakit.siswa.create') }}" class="btn btn-white px-4 shadow-sm text-primary font-weight-bold" style="border-radius: 50px; background: white;">
                                <i class="fas fa-plus mr-2"></i> Ajukan Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Stats --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 15px;">
                <form action="{{ route('penilaiandanpresensi.izinsakit.siswa.index') }}" method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted">Status</label>
                        <select name="status" class="form-control select2-modern">
                            <option value="">Semua Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-block" style="border-radius: 10px;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-light" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center h-100 py-2">
                    <div>
                        <h6 class="text-muted mb-1 small font-weight-bold">Tahun Ajaran</h6>
                        <h5 class="font-weight-bold mb-0 text-primary">{{ $activeTahunAjaran->tahunajaran ?? '-' }}</h5>
                    </div>
                    <div class="text-right">
                        <i class="fas fa-calendar-check fa-2x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold text-dark">Riwayat Pengajuan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th class="pl-4">Jenis</th>
                                    <th>Keterangan</th>
                                    <th>Tgl Mulai</th>
                                    <th>Tgl Selesai</th>
                                    <th class="text-center">Bukti</th>
                                    <th>Status</th>
                                    <th class="text-center pr-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($izinSakits as $item)
                                <tr>
                                    <td class="pl-4">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-shape {{ $item->jenis == 'Izin' ? 'bg-info-light text-info' : 'bg-danger-light text-danger' }} mr-3">
                                                <i class="fas {{ $item->jenis == 'Izin' ? 'fa-info-circle' : 'fa-hand-holding-medical' }}"></i>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold text-dark d-block">{{ $item->jenis }}</span>
                                                <small class="text-muted">{{ $item->tipe_izin }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ Str::limit($item->keterangan ?? '-', 30) }}</span>
                                        @if($item->mataPelajaran)
                                            <br><small class="badge badge-light text-primary">{{ $item->mataPelajaran->nama }}</small>
                                        @endif
                                    </td>
                                    <td>{{ optional($item->tgl_mulai)->format('d/m/Y') }}</td>
                                    <td>{{ optional($item->tgl_selesai)->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        @if($item->bukti_foto)
                                            <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                                <i class="fas fa-image mr-1"></i> Foto
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'badge-warning';
                                            if($item->status == 'Disetujui') $badgeClass = 'badge-success';
                                            if($item->status == 'Ditolak') $badgeClass = 'badge-danger';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2" style="border-radius: 50px;">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="text-center pr-4">
                                        <div class="btn-group">
                                            <a href="{{ route('penilaiandanpresensi.izinsakit.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($item->status == 'Pending')
                                                <a href="{{ route('penilaiandanpresensi.izinsakit.siswa.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('penilaiandanpresensi.izinsakit.siswa.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" style="width: 150px; opacity: 0.5;">
                                        <p class="text-muted mt-3">Belum ada riwayat pengajuan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    {{ $izinSakits->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-shape {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.2rem;
    }
    .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .table td { vertical-align: middle; }
    .pagination { justify-content: center; margin-bottom: 0; }
    .page-item.active .page-link { background-color: #667eea; border-color: #667eea; }
</style>
@endsection
