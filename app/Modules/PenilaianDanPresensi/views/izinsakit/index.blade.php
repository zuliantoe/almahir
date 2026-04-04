@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="Daftar Izin/Sakit" icon="fas fa-notes-medical">
        <x-slot name="tools">
            <a href="{{ route('penilaiandanpresensi.izinsakit.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Izin/Sakit
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:5%">No</th>
                        <th style="width:20%">Siswa</th>
                        <th style="width:20%">Kelas</th>
                        <th style="width:10%">Jenis</th>
                        <th style="width:15%">Mulai</th>
                        <th style="width:15%">Selesai</th>
                        <th class="text-center" style="width:15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinSakits as $index => $item)
                    <tr>
                        <td>{{ ($izinSakits->currentPage() - 1) * $izinSakits->perPage() + $index + 1 }}</td>
                        <td>{{ $item->siswa->nama ?? '-' }}</td>
                        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $item->jenis }}</td>
                        <td>{{ optional($item->tgl_mulai)->format('d M Y') }}</td>
                        <td>{{ optional($item->tgl_selesai)->format('d M Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('penilaiandanpresensi.izinsakit.show', $item->id) }}" class="btn btn-success" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('penilaiandanpresensi.izinsakit.edit', $item->id) }}" class="btn btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger" onclick="hapusData('{{ route('penilaiandanpresensi.izinsakit.destroy', $item->id) }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <strong>Belum ada data izin/sakit.</strong><br>
                            <a href="{{ route('penilaiandanpresensi.izinsakit.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus mr-1"></i> Tambah Data
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">
                    Menampilkan <strong>{{ $izinSakits->count() }}</strong> dari <strong>{{ $izinSakits->total() }}</strong> data
                </span>
                <nav>
                    {{ $izinSakits->links() }}
                </nav>
            </div>
        </x-slot>
    </x-card>
</div>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function hapusData(url) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        document.getElementById('deleteForm').action = url;
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
