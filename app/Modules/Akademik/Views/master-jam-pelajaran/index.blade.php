@extends('layouts.app')

@section('title', 'Master Jam Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-clock mr-2"></i> Master Jam Pelajaran
                    </h3>
                    @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm shadow-sm mr-2" data-toggle="modal" data-target="#modalCopyHari">
                            <i class="fas fa-copy mr-1"></i> Salin Antar Hari
                        </button>
                        <a href="{{ route('akademik.master-jam-pelajaran.create') }}" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </a>
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Hari</th>
                                    <th width="100" class="text-center">Jam Ke</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Status</th>
                                    @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                                    <th width="180" class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($masterJamPelajarans as $row)
                                    <tr>
                                        <td><span class="badge badge-secondary px-2 py-1">{{ $row->hari }}</span></td>
                                        <td class="text-center"><span class="badge badge-info">{{ $row->jamke }}</span></td>
                                        <td>{{ substr($row->jamawal, 0, 5) }}</td>
                                        <td>{{ substr($row->jamakhir, 0, 5) }}</td>
                                        <td>
                                            @if($row->is_istirahat)
                                                <span class="badge badge-danger"><i class="fas fa-coffee mr-1"></i> Istirahat</span>
                                            @else
                                                <span class="badge badge-success"><i class="fas fa-book mr-1"></i> KBM</span>
                                            @endif
                                        </td>
                                        @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center" style="gap: 6px;">
                                                <a href="{{ route('akademik.master-jam-pelajaran.duplicate', $row->id) }}" class="btn btn-info btn-sm shadow-sm" title="Duplikat" style="padding: 4px 8px; margin: 0 2px;">
                                                    <i class="fas fa-clone"></i>
                                                </a>
                                                <a href="{{ route('akademik.master-jam-pelajaran.edit', $row->id) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit" style="padding: 4px 8px; margin: 0 2px;">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('akademik.master-jam-pelajaran.destroy', $row->id) }}" method="POST" style="display:inline; margin: 0 2px;" onsubmit="return confirm('Hapus master jam pelajaran ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm shadow-sm" type="submit" title="Hapus" style="padding: 4px 8px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->hasRole(['GURU', 'SISWA']) ? 5 : 6 }}" class="text-center py-5 text-muted">
                                            Belum ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Salin Antar Hari --}}
<div class="modal fade" id="modalCopyHari" tabindex="-1" role="dialog" aria-labelledby="modalCopyHariLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('akademik.master-jam-pelajaran.copy') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalCopyHariLabel">
                        <i class="fas fa-copy text-primary mr-2"></i>Salin Jam Pelajaran Antar Hari
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Dari Hari (Sumber Data):</label>
                        <select name="dari_hari" class="form-control" required>
                            <option value="">Pilih Hari Asal</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Ke Hari (Tujuan):</label>
                        <div class="px-2">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" name="ke_hari[]" value="{{ $h }}" class="custom-control-input" id="chk_{{ $h }}">
                                    <label class="custom-control-label" for="chk_{{ $h }}">{{ $h }}</label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted mt-1 d-block">
                            * Jam pelajaran yang sudah ada atau bentrok (tumpang tindih waktu) pada hari tujuan akan dilewati secara otomatis demi mencegah bentrok jadwal.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-copy mr-1"></i>Salin Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

