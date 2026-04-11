@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Tahun Ajaran</h1>
            <x-btn :href="route('akademik.tahun-ajaran.create')" icon="fas fa-plus">
                Tambah Tahun Ajaran
            </x-btn>
        </div>
    </div>

    {{-- Form Filter --}}
    <x-card title="Filter Data" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.tahun-ajaran.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-5 mb-3">
                    <x-input label="Cari Tahun Ajaran" name="search" :value="request('search')" placeholder="Masukkan tahun ajaran..." prepend="<i class='fas fa-search'></i>" />
                </div>

                <div class="col-md-4 mb-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <x-btn type="submit" class="btn-info w-100" icon="fas fa-filter">
                        Filter
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel Data --}}
    <x-card title="Daftar Periode Akademik" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjaran as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($tahunAjaran->currentPage() - 1) * $tahunAjaran->perPage() }}</td>
                        <td><span class="badge badge-light border">{{ $item->tahunajaran }}</span></td>
                        <td>
                            @if($item->status)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="text-muted"><i class="far fa-clock mr-1"></i> {{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.tahun-ajaran.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.tahun-ajaran.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <x-btn size="sm" class="btn-danger" title="Hapus" 
                                       onclick="confirmDelete('{{ $item->id }}', '{{ $item->tahunajaran }}')"
                                       :disabled="$item->status">
                                    <i class="fas fa-trash"></i>
                                </x-btn>
                            </div>
                            
                            <form id="delete-form-{{ $item->id }}" action="{{ route('akademik.tahun-ajaran.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-2x mb-3"></i><br>
                            Tidak ada data tahun ajaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tahunAjaran->hasPages())
        <x-slot name="footer">
            {{ $tahunAjaran->withQueryString()->links() }}
        </x-slot>
        @endif
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
