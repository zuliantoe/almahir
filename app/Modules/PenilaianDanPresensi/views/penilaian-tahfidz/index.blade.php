@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="Daftar Penilaian Tahfidz" icon="fas fa-book-reader">
        <x-slot name="tools">
            <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Surat</th>
                        <th>Ayat</th>
                        <th>Guru</th>
                        <th>Nilai</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penilaianTahfidzs as $index => $item)
                    <tr>
                        <td>{{ ($penilaianTahfidzs->currentPage()-1)*$penilaianTahfidzs->perPage()+$index+1 }}</td>
                        <td>{{ $item->siswa->nama ?? '-' }}</td>
                        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $item->surat_awal }} - {{ $item->surat_akhir }}</td>
                        <td>{{ $item->ayat_awal }} - {{ $item->ayat_akhir }}</td>
                        <td>{{ $item->guru->nama ?? '-' }}</td>
                        <td>{{ $item->nilai }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.show', $item->id) }}" class="btn btn-success">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.edit', $item->id) }}" class="btn btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger" onclick="hapusData('{{ route('penilaiandanpresensi.penilaiantahfidz.destroy', $item->id) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i><br>
                            Belum ada data.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">
                    Menampilkan <strong>{{ $penilaianTahfidzs->count() }}</strong> dari <strong>{{ $penilaianTahfidzs->total() }}</strong> data
                </span>
                <nav>{{ $penilaianTahfidzs->links() }}</nav>
            </div>
        </x-slot>
    </x-card>
</div>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function hapusData(url){
    if(confirm('Hapus data ini?')){
        document.getElementById('deleteForm').action=url;
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
@endsection
