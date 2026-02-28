@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <x-card title="Data Tahun Ajaran" type="primary">

        {{-- Header dengan Tombol Tambah --}}
        <x-slot name="tools">
            <a href="{{ route('akademik.tahun-ajaran.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Tahun Ajaran
            </a>
        </x-slot>

        {{-- Form Filter --}}
        <form action="{{ route('akademik.tahun-ajaran.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <x-input
                        label="Cari Tahun Ajaran"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Masukkan tahun ajaran..."
                        icon="fas fa-search"
                    />
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-filter"></i> Terapkan Filter
                        </button>

                        @if(request()->has('search') || request()->has('status'))
                            <a href="{{ route('akademik.tahun-ajaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        {{-- Info Data --}}
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Menampilkan {{ $tahunAjaran->firstItem() ?? 0 }} - {{ $tahunAjaran->lastItem() ?? 0 }}
            dari total {{ $tahunAjaran->total() }} data tahun ajaran
        </div>

        {{-- Tabel Data --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="5%">No</th>
                        <th width="40%">Tahun Ajaran</th>
                        <th width="20%">Status</th>
                        <th width="20%">Dibuat</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjaran as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($tahunAjaran->currentPage() - 1) * $tahunAjaran->perPage() }}</td>
                        <td>{{ $item->tahunajaran }}</td>
                        <td>
                            @if($item->status)
                                <span class="badge badge-success p-2">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-secondary p-2">
                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('akademik.tahun-ajaran.show', $item->id) }}"
                                   class="btn btn-info btn-sm"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('akademik.tahun-ajaran.edit', $item->id) }}"
                                   class="btn btn-warning btn-sm"
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus Data"
                                        onclick="confirmDelete('{{ $item->id }}', '{{ $item->tahunajaran }}')"
                                        {{ $item->status ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            {{-- Form Delete Tersembunyi --}}
                            <form id="delete-form-{{ $item->id }}"
                                  action="{{ route('akademik.tahun-ajaran.destroy', $item->id) }}"
                                  method="POST"
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3"></i>
                                <p>Tidak ada data tahun ajaran</p>
                                @if(request()->has('search') || request()->has('status'))
                                    <a href="{{ route('akademik.tahun-ajaran.index') }}" class="btn btn-primary">
                                        <i class="fas fa-sync"></i> Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('akademik.tahun-ajaran.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Halaman {{ $tahunAjaran->currentPage() }} dari {{ $tahunAjaran->lastPage() }}
            </div>
            <div>
                {{ $tahunAjaran->withQueryString()->links() }}
            </div>
        </div>

    </x-card>
</div>

@endsection

@push('js')
<script>
function confirmDelete(id, tahunAjaran) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus tahun ajaran "${tahunAjaran}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Notifikasi flash data
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif
</script>
@endpush
