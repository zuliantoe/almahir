@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-scroll mr-1"></i> {{ $title }}
                    </h3>
                    @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                    <div class="card-tools">
                        <a href="{{ route('akademik.master-kurikulum.create') }}" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Kurikulum
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="kurikulum-table">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Nama Kurikulum</th>
                                    <th width="200" class="text-center">Beban Mengajar Maksimal</th>
                                    <th width="150" class="text-center">Status</th>
                                    @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                                    <th width="150" class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($masterKurikulums as $kurikulum)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold text-primary">{{ $kurikulum->nama_kurikulum }}</td>
                                    <td class="text-center font-weight-bold text-dark">
                                        {{ $kurikulum->beban_mengajar_maksimal }} <span class="font-weight-normal text-muted">Jam/Minggu</span>
                                    </td>
                                    <td class="text-center">
                                        @if($kurikulum->status)
                                            <span class="badge badge-success px-3 py-2 shadow-sm">
                                                <i class="fas fa-check-circle mr-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary px-3 py-2">
                                                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    @if(!Auth::user()->hasRole(['GURU', 'SISWA']))
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                            <a href="{{ route('akademik.master-kurikulum.edit', $kurikulum->id) }}" 
                                               class="btn btn-warning btn-sm shadow-sm" 
                                               title="Edit" style="margin: 0;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm shadow-sm delete-btn" 
                                                    data-id="{{ $kurikulum->id }}"
                                                    data-name="{{ $kurikulum->nama_kurikulum }}"
                                                    title="Hapus" style="margin: 0;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $kurikulum->id }}" 
                                                  action="{{ route('akademik.master-kurikulum.destroy', $kurikulum->id) }}" 
                                                  method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->hasRole(['GURU', 'SISWA']) ? 4 : 5 }}" class="text-center py-5 text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                        Belum ada data kurikulum.
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
@endsection

@push('js')
<script>
    $(function() {
        $('.delete-btn').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus kurikulum: " + name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@endpush
