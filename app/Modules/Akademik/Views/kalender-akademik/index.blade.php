@extends('layouts.app')

@section('title', 'Kalender Akademik')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Kalender Akademik</h1>
            <x-btn :href="route('akademik.kalender-akademik.create')" icon="fas fa-plus">
                Tambah Kegiatan
            </x-btn>
        </div>
    </div>

    {{-- Form Filter --}}
    <x-card title="Filter Kegiatan" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.kalender-akademik.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-5 mb-3">
                    <label>Tahun Ajaran</label>
                    <select name="tahunajaran_id" class="form-control">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahunajaran_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->tahunajaran }} ({{ $ta->semester }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Cari Kegiatan</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nama kegiatan...">
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
    <x-card title="Agenda Akademik" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Nama Kegiatan</th>
                        <th>Jenis</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kalenderAkademik as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kalenderAkademik->currentPage() - 1) * $kalenderAkademik->perPage() }}</td>
                        <td>{{ $item->tahunAjaran->tahunajaran }} ({{ $item->tahunAjaran->semester }})</td>
                        <td><strong>{{ $item->nama_kegiatan }}</strong></td>
                        <td><span class="badge badge-secondary">{{ $item->jenisKegiatan->jeniskegiatan }}</span></td>
                        <td><i class="far fa-calendar-alt mr-1"></i> {{ date('d/m/Y', strtotime($item->tanggal_awal)) }}</td>
                        <td><i class="far fa-calendar-check mr-1"></i> {{ date('d/m/Y', strtotime($item->tanggal_akhir)) }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.kalender-akademik.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.kalender-akademik.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <x-btn size="sm" class="btn-danger" title="Hapus" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </x-btn>
                            </div>
                            
                            <form id="delete-form-{{ $item->id }}" action="{{ route('akademik.kalender-akademik.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-2x mb-3"></i><br>
                            Tidak ada data kalender akademik
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kalenderAkademik->hasPages())
        <x-slot name="footer">
            {{ $kalenderAkademik->links() }}
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
        text: "Apakah Anda yakin ingin menghapus kegiatan ini?",
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
