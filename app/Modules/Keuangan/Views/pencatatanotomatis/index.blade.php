@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pencatatan Otomatis</h1>
        <a href="{{ route('keuangan.pencatatanotomatis.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1 d-none d-sm-inline-block"></i> Tambah Pencatatan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pencatatan Otomatis (Recurring)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Frekuensi</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pencatatans as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($item->tipe == 'pemasukan')
                                    <span class="badge bg-success">Pemasukan</span>
                                @else
                                    <span class="badge bg-danger">Pengeluaran</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->tipe == 'pemasukan' ? optional($item->sumber)->nama : optional($item->tujuan)->nama }}
                            </td>
                            <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-info text-capitalize">{{ $item->frekuensi }}</span>
                            </td>
                            <td>
                                <small>
                                    Mulai: {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}<br>
                                    Jam: {{ \Carbon\Carbon::parse($item->waktu_eksekusi)->format('H:i') }} WIB
                                </small>
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-primary">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Selesai/Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('keuangan.pencatatanotomatis.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('keuangan.pencatatanotomatis.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pencatatan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data pencatatan otomatis.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
