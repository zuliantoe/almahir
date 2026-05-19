@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 font-weight-bold text-dark"><i class="fas fa-users-slash text-primary mr-2"></i> {{ $title }}</h4>
                <p class="text-muted small mb-0 mt-1">Kelola data pelamar dan konversi menjadi pegawai aktif.</p>
            </div>
            <div>
                <a href="{{ route('pegawaimanager.calon-pegawai.create') }}" class="btn btn-primary rounded-pill btn-animate">
                    <i class="fas fa-plus mr-1"></i> Tambah Pelamar
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted font-weight-bold border-0">Nama & Kontak</th>
                            <th class="px-4 py-3 text-muted font-weight-bold border-0">Posisi Dilamar</th>
                            <th class="px-4 py-3 text-muted font-weight-bold border-0">Tanggal Lamar</th>
                            <th class="px-4 py-3 text-muted font-weight-bold border-0">Status</th>
                            <th class="px-4 py-3 text-muted font-weight-bold border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calonPegawai as $calon)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-weight-bold text-dark">{{ $calon->nama }}</div>
                                <div class="small text-muted"><i class="fas fa-envelope mr-1"></i> {{ $calon->email }}</div>
                                <div class="small text-muted"><i class="fas fa-phone mr-1"></i> {{ $calon->no_hp ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge badge-light px-3 py-2 text-dark shadow-sm">
                                    {{ $calon->typePegawai->nama_type ?? 'Belum ditentukan' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $calon->tanggal_melamar->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($calon->status_seleksi === 'baru')
                                    <span class="badge badge-primary px-3 py-2 rounded-pill">Baru</span>
                                @elseif($calon->status_seleksi === 'wawancara')
                                    <span class="badge badge-warning px-3 py-2 rounded-pill">Wawancara</span>
                                @elseif($calon->status_seleksi === 'diterima')
                                    <span class="badge badge-success px-3 py-2 rounded-pill">Diterima</span>
                                @else
                                    <span class="badge badge-danger px-3 py-2 rounded-pill">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($calon->status_seleksi !== 'diterima')
                                <form action="{{ route('pegawaimanager.calon-pegawai.update', $calon->id) }}" method="POST" class="d-inline" id="form-terima-{{ $calon->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action_type" value="status_update">
                                    <input type="hidden" name="status_seleksi" value="diterima">
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm btn-animate" onclick="confirmTerima('{{ $calon->id }}', '{{ addslashes($calon->nama) }}')">
                                        <i class="fas fa-check-circle mr-1"></i> Terima
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('pegawaimanager.calon-pegawai.edit', $calon->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm btn-animate">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pegawaimanager.calon-pegawai.destroy', $calon->id) }}" method="POST" class="d-inline" id="form-delete-{{ $calon->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm btn-animate" onclick="confirmDelete('{{ $calon->id }}', '{{ addslashes($calon->nama) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-folder-open fa-3x text-light"></i></div>
                                Belum ada data calon pegawai atau pelamar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-3 border-top">
            {{ $calonPegawai->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmTerima(id, nama) {
    Swal.fire({
        title: 'Terima Pegawai?',
        html: `Anda akan menerima <b>${nama}</b> sebagai pegawai aktif.<br>Sistem akan membuatkan akun secara otomatis.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Terima!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-terima-' + id).submit();
        }
    });
}

function confirmDelete(id, nama) {
    Swal.fire({
        title: 'Hapus Pelamar?',
        html: `Anda yakin ingin menghapus lamaran <b>${nama}</b> secara permanen?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-delete-' + id).submit();
        }
    });
}
</script>
@endpush
