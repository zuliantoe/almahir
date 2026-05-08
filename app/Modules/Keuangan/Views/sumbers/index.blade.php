@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('keuangan.sumbers.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Sumber
        </a>
    </div>



    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-database mr-2"></i> Daftar Sumber Pemasukan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted text-uppercase small">
                        <tr>
                            <th class="px-4" style="width: 80px;">#</th>
                            <th class="px-4">Nama Sumber</th>
                            <th class="px-4 text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sumbers as $index => $sumber)
                        <tr>
                            <td class="px-4 text-muted">{{ $index + 1 }}</td>
                            <td class="px-4 font-weight-bold text-dark">{{ $sumber->nama }}</td>
                            <td class="px-4 text-center">
                                <div class="d-flex justify-content-center" style="gap: 8px;">
                                    <a href="{{ route('keuangan.sumbers.edit', $sumber->id) }}" 
                                       class="btn btn-sm btn-info rounded shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('keuangan.sumbers.destroy', $sumber->id) }}" 
                                          method="POST" 
                                          class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded shadow-sm delete-btn" 
                                                title="Hapus" data-source="{{ $sumber->nama }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="fas fa-folder-open fa-3x opacity-2"></i>
                                </div>
                                <p class="mb-0">Belum ada data sumber pemasukan.</p>
                                <a href="{{ route('keuangan.sumbers.create') }}" class="btn btn-link btn-sm mt-2">Tambah Data Baru</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .table thead th {
        border-top: none;
        letter-spacing: 0.05em;
    }
    .card {
        border-radius: 12px;
    }
    .btn {
        border-radius: 8px;
    }
    .btn-group {
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.delete-form');
                const source = this.getAttribute('data-source');
                Swal.fire({
                    title: 'Hapus sumber ini?',
                    text: 'Anda akan menghapus sumber "' + source + '". Tindakan ini tidak dapat dibatalkan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endpush
