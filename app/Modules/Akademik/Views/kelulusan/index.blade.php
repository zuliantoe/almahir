@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-graduation-cap mr-1 text-info"></i> {{ $title }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('akademik.kelulusan.index') }}" method="GET" id="filter-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tahun Ajaran</label>
                                    <select name="ta_id" class="form-control" onchange="document.getElementById('filter-form').submit()">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        @foreach($tahunAjarans as $ta)
                                            <option value="{{ $ta->id }}" {{ $ta_id == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->tahunajaran }} {{ $ta->semester ? '- ' . $ta->semester : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rombel / Kelas Akhir</label>
                                    <select name="rombel_id" class="form-control" onchange="document.getElementById('filter-form').submit()" {{ !$ta_id ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Rombel --</option>
                                        @foreach($rombelList as $rombel)
                                            <option value="{{ $rombel->id }}" {{ $rombel_id == $rombel->id ? 'selected' : '' }}>
                                                {{ $rombel->nama_rombel }} ({{ $rombel->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    @if($rombel_id && $siswaList->count() > 0)
                        <form action="{{ route('akademik.kelulusan.process') }}" method="POST" id="process-form">
                            @csrf
                            <input type="hidden" name="rombel_id" value="{{ $rombel_id }}">
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="m-0 text-primary font-weight-bold"><i class="fas fa-users mr-1"></i> Daftar Calon Alumni</h5>
                                <button type="button" class="btn btn-info font-weight-bold px-4 shadow-sm" onclick="confirmProcess()">
                                    <i class="fas fa-graduation-cap mr-1"></i> Proses Kelulusan Massal
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th width="50">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="checkAll" checked>
                                                    <label class="custom-control-label" for="checkAll"></label>
                                                </div>
                                            </th>
                                            <th width="150">NIS</th>
                                            <th>Nama Siswa</th>
                                            <th width="100">L/P</th>
                                            <th width="150">Status Saat Ini</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswaList as $rs)
                                            <tr>
                                                <td class="text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input siswa-checkbox" 
                                                               name="siswa_ids[]" value="{{ $rs->siswa_id }}" id="siswa_{{ $rs->siswa_id }}" checked>
                                                        <label class="custom-control-label" for="siswa_{{ $rs->siswa_id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center font-weight-bold"><code>{{ $rs->siswa->nis ?? '-' }}</code></td>
                                                <td>{{ $rs->siswa->nama ?? '-' }}</td>
                                                <td class="text-center text-muted">{{ $rs->siswa->jenis_kelamin ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-success px-3">Aktif</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    @elseif($rombel_id && $siswaList->count() == 0)
                        <div class="alert alert-warning text-center py-4 shadow-sm">
                            <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                            <h5>Tidak ada siswa aktif di rombel ini.</h5>
                            <p class="mb-0">Mungkin semua siswa sudah diluluskan atau rombel ini memang kosong.</p>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted border rounded bg-light">
                            <i class="fas fa-search fa-3x mb-3 text-secondary" style="opacity: 0.3"></i>
                            <h5>Pilih Tahun Ajaran dan Rombel</h5>
                            <p>Silakan pilih kelas mana yang siswanya akan Anda nyatakan **LULUS**.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#checkAll').on('change', function() {
            $('.siswa-checkbox').prop('checked', $(this).prop('checked'));
        });

        $('.siswa-checkbox').on('change', function() {
            if (!$(this).prop('checked')) {
                $('#checkAll').prop('checked', false);
            } else {
                let allChecked = $('.siswa-checkbox:not(:checked)').length === 0;
                // $('#checkAll').prop('checked', allChecked);
            }
        });
    });

    function confirmProcess() {
        let selectedCount = $('.siswa-checkbox:checked').length;
        
        if(selectedCount === 0) {
            Swal.fire('Peringatan', 'Pilih minimal 1 siswa untuk diluluskan.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Kelulusan',
            text: `Anda yakin akan meluluskan ${selectedCount} siswa? Mereka akan berstatus Alumni dan tidak aktif lagi di rombel ini.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Luluskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#process-form').submit();
            }
        });
    }
</script>
@endpush
