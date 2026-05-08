@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-level-up-alt mr-1 text-primary"></i> {{ $title }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('akademik.kenaikan-kelas.index') }}" method="GET" id="filter-form">
                        <div class="row">
                            <!-- Kolom Asal -->
                            <div class="col-md-5">
                                <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-sign-out-alt mr-1"></i> Data Asal</h5>
                                <div class="form-group">
                                    <label>Tahun Ajaran Asal</label>
                                    <select name="ta_asal" class="form-control" onchange="document.getElementById('filter-form').submit()">
                                        <option value="">-- Pilih Tahun Ajaran Asal --</option>
                                        @foreach($tahunAjarans as $ta)
                                            <option value="{{ $ta->id }}" {{ $ta_asal == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->tahunajaran }} {{ $ta->semester ? '- ' . $ta->semester : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Rombel Asal</label>
                                    <select name="rombel_asal" class="form-control" onchange="document.getElementById('filter-form').submit()" {{ !$ta_asal ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Rombel Asal --</option>
                                        @foreach($rombelAsalList as $rombel)
                                            <option value="{{ $rombel->id }}" {{ $rombel_asal == $rombel->id ? 'selected' : '' }}>
                                                {{ $rombel->nama_rombel }} ({{ $rombel->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <i class="fas fa-arrow-circle-right fa-3x text-success"></i>
                            </div>

                            <!-- Kolom Tujuan -->
                            <div class="col-md-5">
                                <h5 class="text-success border-bottom pb-2 mb-3"><i class="fas fa-sign-in-alt mr-1"></i> Data Tujuan</h5>
                                <div class="form-group">
                                    <label>Tahun Ajaran Tujuan</label>
                                    <select name="ta_tujuan" class="form-control" onchange="document.getElementById('filter-form').submit()">
                                        <option value="">-- Pilih Tahun Ajaran Tujuan --</option>
                                        @foreach($tahunAjarans as $ta)
                                            <option value="{{ $ta->id }}" {{ $ta_tujuan == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->tahunajaran }} {{ $ta->semester ? '- ' . $ta->semester : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Rombel Tujuan</label>
                                    <select name="rombel_tujuan" class="form-control" onchange="document.getElementById('filter-form').submit()" {{ !$ta_tujuan ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Rombel Tujuan --</option>
                                        @foreach($rombelTujuanList as $rombel)
                                            <option value="{{ $rombel->id }}" {{ $rombel_tujuan == $rombel->id ? 'selected' : '' }}>
                                                {{ $rombel->nama_rombel }} ({{ $rombel->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    @if($rombel_asal && $rombel_tujuan && $siswaList->count() > 0)
                        <form action="{{ route('akademik.kenaikan-kelas.process') }}" method="POST" id="process-form">
                            @csrf
                            <input type="hidden" name="rombel_asal" value="{{ $rombel_asal }}">
                            <input type="hidden" name="rombel_tujuan" value="{{ $rombel_tujuan }}">
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="m-0"><i class="fas fa-users mr-1"></i> Daftar Siswa untuk Dipromosikan</h5>
                                <button type="button" class="btn btn-success font-weight-bold px-4" onclick="confirmProcess()">
                                    <i class="fas fa-check-circle mr-1"></i> Proses Kenaikan Kelas
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="50" class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="checkAll" checked>
                                                    <label class="custom-control-label" for="checkAll"></label>
                                                </div>
                                            </th>
                                            <th width="80" class="text-center">No</th>
                                            <th width="150" class="text-center">NISN</th>
                                            <th>Nama Siswa</th>
                                            <th width="100" class="text-center">L/P</th>
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
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center font-weight-bold">{{ $rs->siswa->nis ?? '-' }}</td>
                                                <td>{{ $rs->siswa->nama ?? '-' }}</td>
                                                <td class="text-center">{{ $rs->siswa->jenis_kelamin ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    @elseif($rombel_asal && $siswaList->count() == 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada siswa di rombel asal yang dipilih.
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-info-circle fa-3x mb-3 text-light"></i>
                            <h5>Silakan lengkapi pilihan Data Asal dan Data Tujuan terlebih dahulu.</h5>
                            <p>Fitur ini digunakan untuk memindahkan siswa secara massal dari satu rombel ke rombel lain.</p>
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
                $('#checkAll').prop('checked', allChecked);
            }
        });
    });

    function confirmProcess() {
        let selectedCount = $('.siswa-checkbox:checked').length;
        
        if(selectedCount === 0) {
            Swal.fire('Peringatan', 'Silakan pilih minimal 1 siswa untuk diproses.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Promosi Siswa',
            text: `Anda akan memindahkan ${selectedCount} siswa ke rombel tujuan. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Proses Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#process-form').submit();
            }
        });
    }
</script>
@endpush
