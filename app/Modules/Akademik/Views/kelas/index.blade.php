@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Kelas</h1>
            <x-btn :href="route('akademik.kelas.create')" icon="fas fa-plus">
                Tambah Kelas Baru
            </x-btn>
        </div>
    </div>

    {{-- Daftar Kelas --}}
    <x-card title="Daftar Kelas Aktif" icon="fas fa-school" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Kelas</th>
                        <th class="text-center">Total Jadwal</th>
                        <th class="text-center">Total Kurikulum</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $k)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td><strong>{{ $k->nama }}</strong></td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $k->jadwal_pelajaran_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $k->kurikulum_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <x-btn :href="route('akademik.kelas.show', $k->id)" size="sm" class="btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </x-btn>
                                    <x-btn :href="route('akademik.kelas.edit', $k->id)" size="sm" class="btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </x-btn>
                                    <x-btn size="sm" class="btn-danger" title="Hapus" onclick="confirmDelete('{{ $k->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </x-btn>
                                </div>

                                <form id="delete-form-{{ $k->id }}" action="{{ route('akademik.kelas.destroy', $k->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-school fa-2x mb-3"></i><br>
                                Belum ada data kelas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection

@push('js')
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Yakin hapus data jadwal kelas ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-danger mx-1',
            cancelButton: 'btn btn-secondary mx-1'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
