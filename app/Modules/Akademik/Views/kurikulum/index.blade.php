@extends('layouts.app')

@section('title', 'Manajemen Kurikulum')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Kurikulum</h1>
            <x-btn :href="route('akademik.kurikulum.create')" icon="fas fa-plus">
                Tambah Kurikulum
            </x-btn>
        </div>
    </div>

    {{-- Form Filter --}}
    <x-card title="Filter Kurikulum" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.kurikulum.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-5 mb-3">
                    <label>Master Kurikulum</label>
                    <select name="master_kurikulum_id" class="form-control">
                        <option value="">Semua Kurikulum</option>
                        @foreach($masterKurikulums as $mk)
                            <option value="{{ $mk->id }}" {{ request('master_kurikulum_id') == $mk->id ? 'selected' : '' }}>{{ $mk->nama_kurikulum }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Tingkat</label>
                    <select name="tingkat_id" class="form-control">
                        <option value="">Semua Tingkat</option>
                        @foreach($tingkats as $tingkat)
                            <option value="{{ $tingkat->id }}" {{ request('tingkat_id') == $tingkat->id ? 'selected' : '' }}>{{ $tingkat->nama_tingkat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <x-btn type="submit" class="btn-info w-100" icon="fas fa-search">
                        Filter
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel Data --}}
    <x-card title="Daftar Struktur Kurikulum" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Kurikulum</th>
                        <th>Tingkat</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">Jam</th>
                        <th class="text-center">KKM</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurikulum as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kurikulum->currentPage() - 1) * $kurikulum->perPage() }}</td>
                        <td><span class="badge badge-light border">{{ $item->masterKurikulum->nama_kurikulum }}</span></td>
                        <td>{{ $item->tingkat->nama_tingkat }}</td>
                        <td>{{ $item->kelas->nama_kelas }}</td>
                        <td><strong>{{ $item->mataPelajaran->nama }}</strong></td>
                        <td class="text-center">{{ $item->totaljam }} Jam</td>
                        <td class="text-center"><span class="text-primary font-weight-bold">{{ $item->kkm }}</span></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.kurikulum.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.kurikulum.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <x-btn size="sm" class="btn-danger" title="Hapus" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </x-btn>
                            </div>
                            
                            <form id="delete-form-{{ $item->id }}" action="{{ route('akademik.kurikulum.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-book-open fa-2x mb-3"></i><br>
                            Tidak ada data kurikulum
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kurikulum->hasPages())
        <x-slot name="footer">
            {{ $kurikulum->links() }}
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
        text: "Apakah Anda yakin ingin menghapus data kurikulum ini?",
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
