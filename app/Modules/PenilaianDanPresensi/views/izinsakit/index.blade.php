@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Notifikasi otomatis via SweetAlert2 (Global Handler) --}}

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);">
                <div class="card-body p-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="font-weight-bold mb-1"><i class="fas fa-notes-medical mr-2"></i> Daftar Izin & Sakit</h3>
                        <p class="mb-0 opacity-75">Monitoring dan konfirmasi cepat pengajuan izin santri</p>
                    </div>
                    <div class="ml-auto text-right">
                        <div class="badge badge-light p-2 shadow-sm text-danger" style="border-radius: 10px; font-weight: 800;">
                            <i class="fas fa-calendar-check mr-1"></i> TA: {{ $activeTahunAjaran->tahunajaran ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold text-danger"><i class="fas fa-filter mr-2"></i> Filter Pengajuan</h5>
            </div>
        </div>
        <div class="card-body pt-0">
            <form method="GET" class="row">
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold text-muted">ROMBEL</label>
                    <select name="rombel_id" class="form-control select2-modern">
                        <option value="">Semua Rombel</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>{{ $rombel->nama_rombel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold text-muted">JENIS</label>
                    <select name="jenis" class="form-control">
                        <option value="">Semua</option>
                        <option value="Izin" {{ request('jenis') == 'Izin' ? 'selected' : '' }}>Izin</option>
                        <option value="Sakit" {{ request('jenis') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">STATUS</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-muted">TANGGAL</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
                </div>
                <div class="col-md-2 mt-auto mb-2">
                    <button type="submit" class="btn btn-danger btn-block font-weight-bold">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #fff5f5;">
                        <tr>
                            <th class="border-0 px-4">Santri & Kelas</th>
                            <th class="border-0">Jenis & Tipe</th>
                            <th class="border-0">Waktu</th>
                            <th class="border-0 text-center">Bukti</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-center px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($izinSakits as $item)
                        <tr>
                            <td class="px-4">
                                <div class="font-weight-bold text-dark">{{ $item->siswa->nama ?? '-' }}</div>
                                <span class="badge badge-outline-danger text-danger border-danger" style="font-size: 0.7rem; border: 1px solid;">{{ $item->rombel->nama_rombel ?? $item->rombel->nama_kelas ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="p-2 rounded-circle mr-2 bg-danger-light text-danger" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                        <i class="fas {{ $item->jenis == 'Sakit' ? 'fa-briefcase-medical' : 'fa-user-clock' }}"></i>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold d-block text-dark small">{{ $item->jenis }}</span>
                                        <small class="text-muted text-uppercase" style="font-size: 0.65rem;">{{ $item->tipe_izin }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="text-dark small font-weight-bold">{{ optional($item->tgl_mulai)->format('d M Y') }}</div>
                                @if($item->tgl_mulai != $item->tgl_selesai)
                                    <small class="text-muted">s/d {{ optional($item->tgl_selesai)->format('d M Y') }}</small>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if($item->bukti_foto)
                                    <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                                        <i class="fas fa-image mr-1"></i> Foto
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if($item->status === 'Pending')
                                    <span class="badge badge-warning px-3 py-2 text-white shadow-sm" style="border-radius: 10px;">
                                        <i class="fas fa-hourglass-half mr-1"></i> Menunggu
                                    </span>
                                @elseif($item->status === 'Disetujui')
                                    <span class="badge badge-success px-3 py-2 shadow-sm" style="border-radius: 10px;">
                                        <i class="fas fa-check-circle mr-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="badge badge-danger px-3 py-2 shadow-sm" style="border-radius: 10px;">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle text-center px-4">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                    <a href="{{ route('penilaiandanpresensi.izinsakit.show', $item->id) }}" class="btn btn-light btn-sm text-primary px-3" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($item->status === 'Pending')
                                        <button class="btn btn-success btn-sm border-0 px-3" onclick="konfirmasiStatus('{{ route('penilaiandanpresensi.izinsakit.confirm', $item->id) }}', 'Disetujui')" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm border-0 px-3" onclick="konfirmasiStatus('{{ route('penilaiandanpresensi.izinsakit.confirm', $item->id) }}', 'Ditolak')" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-envelope-open fa-3x mb-3 d-block opacity-20 text-danger"></i>
                                <p class="text-muted">Belum ada pengajuan izin atau sakit yang masuk.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($izinSakits->hasPages())
        <div class="card-footer bg-white py-3 border-0">
            {{ $izinSakits->appends(request()->all())->links() }}
        </div>
        @endif
    </div>
</div>

<form id="konfirmasiForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>

<style>
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .bg-warning-light { background-color: rgba(253, 126, 20, 0.1); }
</style>
@endsection

@push('scripts')
<script>
function konfirmasiStatus(url, status) {
    const icon = status === 'Disetujui' ? 'success' : 'warning';
    const color = status === 'Disetujui' ? '#28a745' : '#dc3545';
    const text = status === 'Disetujui' ? 'Izinkan santri untuk tidak hadir?' : 'Tolak pengajuan izin ini?';

    Swal.fire({
        title: 'Konfirmasi ' + status,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, ' + status + '!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.getElementById('konfirmasiForm');
            form.action = url;
            document.getElementById('statusInput').value = status;
            form.submit();
        }
    });
}
</script>
@endpush
