@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Pelajaran</h1>
            <x-btn :href="route('akademik.jadwal-pelajaran.create')" icon="fas fa-plus">
                Tambah Jadwal
            </x-btn>
        </div>
    </div>

    {{-- Form Filter --}}
    <x-card title="Filter Data" icon="fas fa-filter" outline collapsible>
        <form action="{{ route('akademik.jadwal-pelajaran.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-5 mb-3">
                    <label>Filter Rombel</label>
                    <select name="rombel_id" class="form-control">
                        <option value="">Semua Rombel</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->nama_rombel }} ({{ $rombel->kelas->nama_kelas }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Hari</label>
                    <select name="hari" class="form-control">
                        <option value="">Semua Hari</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                            <option value="{{ $hari }}" {{ request('hari') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <x-btn type="submit" class="btn-info w-100" icon="fas fa-search">
                        Terapkan Filter
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Tabel Data --}}
    <x-card title="Daftar Jadwal Pelajaran" type="primary" outline>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Rombel</th>
                        <th>Hari</th>
                        <th class="text-center">Jam Ke</th>
                        <th class="text-center">Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalPelajaran as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($jadwalPelajaran->currentPage() - 1) * $jadwalPelajaran->perPage() }}</td>
                        <td>{{ $item->rombel->nama_rombel }}</td>
                        <td><span class="badge badge-info">{{ $item->hari }}</span></td>
                        <td class="text-center">{{ $item->jamke }}</td>
                        <td class="text-center text-muted">
                            {{ substr($item->jamawal, 0, 5) }} - {{ substr($item->jamakhir, 0, 5) }}
                        </td>
                        <td><strong>{{ $item->mataPelajaran->nama }}</strong></td>
                        <td>{{ $item->guru->nama }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <x-btn :href="route('akademik.jadwal-pelajaran.show', $item->id)" size="sm" class="btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </x-btn>
                                <x-btn :href="route('akademik.jadwal-pelajaran.edit', $item->id)" size="sm" class="btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </x-btn>
                                <x-btn size="sm" class="btn-danger" title="Hapus" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </x-btn>
                            </div>
                            
                            <form id="delete-form-{{ $item->id }}" action="{{ route('akademik.jadwal-pelajaran.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                            Tidak ada data jadwal pelajaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($jadwalPelajaran->hasPages())
        <x-slot name="footer">
            {{ $jadwalPelajaran->links() }}
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
        text: "Apakah Anda yakin ingin menghapus jadwal ini?",
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
