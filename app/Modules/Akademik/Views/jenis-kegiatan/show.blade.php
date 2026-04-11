@extends('layouts.app')

@section('title', 'Detail Jenis Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Detail Jenis Kegiatan" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted">Nama Jenis Kegiatan</th>
                                <td><strong>{{ $jenisKegiatan->jeniskegiatan }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Deskripsi</th>
                                <td>{{ $jenisKegiatan->deskripsi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Total Penggunaan</th>
                                <td>
                                    <span class="badge badge-success px-3 py-2">
                                        {{ $jenisKegiatan->kalenderAkademik->count() }} Kegiatan
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <hr>

                <div class="d-flex justify-content-end">
                    <x-btn :href="route('akademik.jenis-kegiatan.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.jenis-kegiatan.edit', $jenisKegiatan->id)" class="btn-warning text-white mr-2" icon="fas fa-edit">
                        Edit Data
                    </x-btn>
                    <x-btn size="md" class="btn-danger" icon="fas fa-trash" onclick="confirmDelete()">
                        Hapus
                    </x-btn>
                    
                    <form id="delete-form" action="{{ route('akademik.jenis-kegiatan.destroy', $jenisKegiatan->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Apakah Anda yakin ingin menghapus data ini?",
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
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endpush
