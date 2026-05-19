@extends('layouts.app')

@section('title', 'Kenaikan Kelas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kenaikan Kelas & Kelulusan</h1>
                <p class="text-muted">Memindahkan santri dari Tahun Ajaran <span class="badge badge-dark px-2">{{ $sourceYear->tahunajaran }}</span> ke <span class="badge badge-success px-2">{{ $destinationYear->tahunajaran }}</span></p>
            </div>
            <x-btn :href="route('akademik.rombel.history')" icon="fas fa-history" class="btn-outline-primary px-4 rounded-pill shadow-sm">
                Lihat Riwayat
            </x-btn>
        </div>
    </div>

    {{-- Session alerts are handled globally via SweetAlert2 in layout --}}

    <form action="{{ route('akademik.kenaikan-kelas.process') }}" method="POST" id="formKenaikan">
        @csrf
        <div class="row">
            <div class="col-lg-4">
                <x-card title="Konfigurasi Periode" icon="fas fa-cog" type="primary" outline class="shadow-lg border-0 rounded-xl overflow-hidden">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark small text-uppercase">Tahun Ajaran Tujuan (Aktif)</label>
                        <div class="p-3 bg-primary-soft rounded-lg mb-4 d-flex align-items-center border border-primary">
                            <i class="fas fa-calendar-check fa-2x text-primary mr-3"></i>
                            <div>
                                <h5 class="mb-0 font-weight-bold text-primary">{{ $destinationYear->tahunajaran }}</h5>
                                <span class="badge badge-primary">Periode Aktif</span>
                            </div>
                        </div>

                        <label class="font-weight-bold text-dark small text-uppercase">Rombongan Belajar (Asal)</label>
                        <select name="rombel_id" id="rombel_id" class="form-control select2-premium" required>
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" data-tingkat="{{ $rombel->tingkat_id }}">
                                    {{ $rombel->nama_rombel }} (Tingkat {{ $rombel->tingkat->nama_tingkat ?? '' }}) 
                                    — {{ $rombel->riwayatSiswa->where('status', 'aktif')->count() }} Santri
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-4 p-3 bg-light rounded-lg border">
                            <h6 class="font-weight-bold small text-primary text-uppercase mb-2"><i class="fas fa-info-circle mr-1"></i> Info Alur</h6>
                            <p class="small text-muted mb-0">
                                Siswa yang <strong>Naik</strong> akan dipindahkan ke tahun ajaran <strong class="text-success">{{ $destinationYear->tahunajaran }}</strong>.
                                Sistem akan mencarikan kelas padanan secara otomatis berdasarkan tingkat berikutnya.
                            </p>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Panel Siswa -->
            <div class="col-lg-8">
                <x-card title="Daftar Siswa untuk Dipindahkan" icon="fas fa-user-graduate" type="info" outline shadow>
                    <div id="siswaPlaceholder" class="text-center py-5">
                        <i class="fas fa-users fa-4x text-light mb-3"></i>
                        <h5 class="text-muted">Silakan pilih Rombel Asal terlebih dahulu</h5>
                        <p class="text-muted small">Daftar siswa yang akan naik kelas akan muncul di sini.</p>
                    </div>

                    <div id="siswaContainer" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-checkbox custom-control-lg mr-4">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label font-weight-bold" for="selectAll">Pilih Semua Siswa</label>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm d-none" id="btnLulusSemua">
                                    <i class="fas fa-user-graduate mr-1"></i> Set Lulus Semua
                                </button>
                            </div>
                            <span class="badge badge-pill badge-primary px-3 py-2" id="siswaCountBadge">0 Siswa</span>
                        </div>

                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover border">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">NIS</th>
                                        <th class="text-center">Status Akhir</th>
                                    </tr>
                                </thead>
                                <tbody id="siswaListBody">
                                    <!-- Data via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-right">
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg font-weight-bold" style="border-radius: 30px;">
                                <i class="fas fa-rocket mr-2"></i> Proses Kenaikan Kelas
                            </button>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load Siswa when Rombel changes
    $('#rombel_id').change(function() {
        const rombelId = $(this).val();
        const tingkatId = $(this).find(':selected').data('tingkat');
        const maxTingkatId = {{ $maxTingkatId ?? 0 }};
        
        if (rombelId) {
            if (tingkatId == maxTingkatId) {
                $('#btnLulusSemua').removeClass('d-none');
            } else {
                $('#btnLulusSemua').addClass('d-none');
            }
            $('#siswaPlaceholder').addClass('d-none');
            $('#siswaContainer').removeClass('d-none');
            $('#siswaListBody').html('<tr><td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data siswa...</td></tr>');

            $.get('{{ route("akademik.kenaikan-kelas.get-siswa") }}', { rombel_id: rombelId }, function(data) {
                let html = '';
                data.forEach(function(rs) {
                    html += `
                        <tr>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="siswa_ids[]" value="${rs.siswa.id}" class="custom-control-input siswa-check" id="s_${rs.siswa.id}" checked>
                                    <label class="custom-control-label" for="s_${rs.siswa.id}"></label>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold">${rs.siswa.nama}</div>
                            </td>
                            <td class="text-center"><code class="text-primary">${rs.siswa.nis}</code></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-success btn-xs active">
                                        <input type="radio" name="status[${rs.siswa.id}]" value="naik" checked> Naik
                                    </label>
                                    <label class="btn btn-outline-warning btn-xs">
                                        <input type="radio" name="status[${rs.siswa.id}]" value="tidak_naik"> Tinggal
                                    </label>
                                    <label class="btn btn-outline-danger btn-xs">
                                        <input type="radio" name="status[${rs.siswa.id}]" value="lulus"> Lulus
                                    </label>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $('#siswaListBody').html(html);
                updateCount();
            });
        } else {
            $('#siswaPlaceholder').removeClass('d-none');
            $('#siswaContainer').addClass('d-none');
        }
    });

    // Luluskan Semua Logic
    $('#btnLulusSemua').click(function() {
        Swal.fire({
            title: 'Set Lulus Semua?',
            text: 'Semua siswa yang dipilih akan diatur statusnya menjadi Lulus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Set Lulus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('input[type="radio"][value="lulus"]').prop('checked', true).closest('label').addClass('active');
                $('input[type="radio"][value="naik"], input[type="radio"][value="tidak_naik"]').prop('checked', false).closest('label').removeClass('active');
            }
        });
    });

    // Select All Logic
    $('#selectAll').change(function() {
        $('.siswa-check').prop('checked', $(this).prop('checked'));
        updateCount();
    });

    $(document).on('change', '.siswa-check', function() {
        updateCount();
    });

    $('#formKenaikan').submit(function(e) {
        const siswaSelected = $('.siswa-check:checked').length;

        if (siswaSelected === 0) {
            Swal.fire('Peringatan', 'Silakan pilih setidaknya satu siswa.', 'warning');
            return false;
        }

        e.preventDefault();

        Swal.fire({
            title: 'Konfirmasi Kenaikan',
            text: 'Apakah Anda yakin ingin memproses kenaikan kelas untuk rombel ini? Rombel akan otomatis dinaikkan tingkatnya.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Proses Sekarang!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const btn = $('#formKenaikan').find('button[type="submit"]');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...');
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    function updateCount() {
        const count = $('.siswa-check:checked').length;
        $('#siswaCountBadge').text(count + ' Siswa Terpilih');
    }
});
</script>

<style>
    .rounded-xl { border-radius: 1rem !important; }
    .rounded-lg { border-radius: 0.75rem !important; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .hover-scale:hover { transform: scale(1.02); }
    .select2-premium + .select2-container .select2-selection--single {
        height: 45px;
        border-radius: 10px;
        border: 1px solid #d1d3e2;
        padding-top: 8px;
    }
    .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
    .table-premium thead th {
        background: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05rem;
        color: #4e73df;
        border-top: 0;
    }
    .badge-soft-success { background-color: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2); }
    .sticky-top { z-index: 10; top: 0; }
    .table th { text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; }
    .table td { vertical-align: middle; }
</style>
@endpush
@endsection
