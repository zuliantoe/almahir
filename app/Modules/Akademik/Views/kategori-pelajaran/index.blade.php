@extends('layouts.app')

@section('title', 'Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Kategori Mata Pelajaran</h1>
            <x-btn :href="route('akademik.kategori-pelajaran.create')" icon="fas fa-plus">
                Tambah Kategori
            </x-btn>
        </div>
    </div>

    <x-card title="Daftar Kategori" icon="fas fa-tags" type="primary" outline>
        <div class="table-responsive">
            {{-- Search Bar --}}
            <div class="mb-3">
                <form action="{{ route('akademik.kategori-pelajaran.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <x-input label="Cari Kategori" name="search" :value="request('search')" placeholder="Cari nama kategori..." prepend="<i class='fas fa-search'></i>" />
                        </div>
                        <div class="col-md-3">
                             <div class="btn-group w-100">
                                <x-btn type="submit" class="btn-info" icon="fas fa-search">Cari</x-btn>
                                @if(request('search'))
                                    <x-btn :href="route('akademik.kategori-pelajaran.index')" class="btn-secondary" icon="fas fa-times">Reset</x-btn>
                                @endif
                             </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Kategori</th>
                        <th class="text-center">Jumlah Mata Pelajaran</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoriPelajaran as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kategoriPelajaran->currentPage() - 1) * $kategoriPelajaran->perPage() }}</td>
                        <td><strong>{{ $item->kategori }}</strong></td>
                        <td class="text-center">
                            @if(($item->mata_pelajaran_count ?? $item->mataPelajaran->count()) > 0)
                                <span class="badge badge-info px-3 py-2">
                                    {{ $item->mata_pelajaran_count ?? $item->mataPelajaran->count() }} Mapel
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.kategori-pelajaran.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.kategori-pelajaran.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <x-btn size="sm" class="btn-danger" title="Hapus" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </x-btn>
                            </div>

                            <form id="delete-form-{{ $item->id }}" action="{{ route('akademik.kategori-pelajaran.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-tags fa-2x mb-3"></i><br>
                            Tidak ada data kategori pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kategoriPelajaran->hasPages())
        <x-slot name="footer">
            {{ $kategoriPelajaran->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection

@push('js')
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Apakah Anda yakin ingin menghapus kategori ini?",
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
