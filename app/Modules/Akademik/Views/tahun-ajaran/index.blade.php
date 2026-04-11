@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="font-weight-bold text-dark mb-0">Manajemen Tahun Ajaran</h2>
            <a href="{{ route('akademik.tahun-ajaran.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-plus mr-1"></i> Tambah Tahun Ajaran
            </a>
        </div>
    </div>

    {{-- Form Filter --}}
    <div class="card card-modern mb-4">
        <div class="card-body">
            <form action="{{ route('akademik.tahun-ajaran.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-3">
                        <label class="font-weight-bold">Cari Tahun Ajaran</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" name="search"
                                value="{{ request('search') }}"
                                class="form-control form-control-modern" placeholder="Masukkan tahun ajaran...">
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Status</label>
                        <select name="status" class="form-control form-control-modern">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 text-right">
                        <button type="submit" class="btn btn-primary btn-modern py-2 px-4 shadow-sm w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Info Data --}}
    <div class="alert alert-light alert-dismissible fade show border" style="border-radius: 0.5rem; background-color: white;">
        <i class="fas fa-info-circle text-info mr-2"></i>
        Menampilkan {{ $tahunAjaran->firstItem() ?? 0 }} - {{ $tahunAjaran->lastItem() ?? 0 }}
        dari total <strong>{{ $tahunAjaran->total() }}</strong> data tahun ajaran
    </div>

    {{-- Tabel Data --}}
    <div class="card card-modern">
        <div class="card-header bg-gradient-blue">
            <h3 class="card-title text-white">
                <i class="fas fa-calendar-alt mr-2"></i>Daftar Periode
            </h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-modern text-nowrap">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="40%">Tahun Ajaran</th>
                        <th width="20%">Status</th>
                        <th width="20%">Dibuat</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjaran as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($tahunAjaran->currentPage() - 1) * $tahunAjaran->perPage() }}</td>
                        <td class="font-weight-bold text-dark">
                            <span class="badge badge-light badge-modern">{{ $item->tahunajaran }}</span>
                        </td>
                        <td>
                            @if($item->status)
                                <span class="badge badge-success badge-modern">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-secondary badge-modern">
                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="text-muted"><i class="far fa-clock mr-1"></i> {{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            
                            <a href="{{ route('akademik.tahun-ajaran.show', $item->id) }}"
                               class="btn btn-info btn-sm btn-modern" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('akademik.tahun-ajaran.edit', $item->id) }}"
                               class="btn btn-warning btn-sm btn-modern text-white" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-danger btn-sm btn-modern"
                                    title="Hapus Data"
                                    onclick="confirmDelete('{{ $item->id }}', '{{ $item->tahunajaran }}')"
                                    {{ $item->status ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i>
                            </button>

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
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i>
                                <p class="font-weight-bold">Tidak ada data tahun ajaran</p>
                                @if(request()->has('search') || request()->has('status'))
                                    <a href="{{ route('akademik.tahun-ajaran.index') }}" class="btn btn-primary btn-modern mt-2">
                                        <i class="fas fa-sync"></i> Reset Filter
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
        @if($tahunAjaran->hasPages())
        <div class="card-footer bg-white border-0 pt-3 flex items-center justify-between">
            {{ $tahunAjaran->withQueryString()->links() }}
        </div>
        @endif

    </div>
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
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-danger btn-modern mx-1',
            cancelButton: 'btn btn-secondary btn-modern mx-1'
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
