@extends('layouts.app')

@section('title', 'Kenaikan Kelas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kenaikan Kelas</h1>
                <p class="text-muted">Proses perpindahan rombel dan siswa ke jenjang/tahun ajaran berikutnya</p>
            </div>
            <x-btn :href="route('akademik.rombel.history')" icon="fas fa-history" class="btn-info shadow-sm">
                Lihat Riwayat
            </x-btn>
        </div>
    </div>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <form action="{{ route('akademik.kenaikan-kelas.process') }}" method="POST" id="formKenaikan">
        @csrf
        <div class="row">
            <!-- Sidebar Konfigurasi -->
            <div class="col-lg-4">
                <x-card title="Konfigurasi Kenaikan" icon="fas fa-cog" type="primary" outline shadow>
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">1. Pilih Tahun Ajaran Asal</label>
                        <select name="tahunajaran_asal_id" id="tahun_asal" class="form-control select2" required>
                            <option value="">-- Pilih Tahun --</option>
                            @foreach($tahun_ajaran as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->tahunajaran }} - {{ $ta->semester }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Tahun ajaran tempat rombel berada saat ini.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">2. Pilih Rombel</label>
                        <select name="rombel_id" id="rombel_id" class="form-control select2" required disabled>
                            <option value="">-- Pilih Rombel --</option>
                        </select>
                        <div id="rombelLoader" class="spinner-border spinner-border-sm text-primary d-none mt-2" role="status"></div>
                    </div>

                    <hr class="my-4">

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">3. Pilih Tahun Ajaran Tujuan</label>
                        <select name="tahunajaran_tujuan_id" id="tahun_tujuan" class="form-control select2" required>
                            <option value="">-- Pilih Tahun Tujuan --</option>
                            @foreach($tahun_ajaran as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->tahunajaran }} - {{ $ta->semester }}</option>
                            @endforeach
                        </select>
                        <small class="text-primary font-weight-bold">Target kenaikan kelas.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">4. Pilih Kelas & Tingkat Tujuan</label>
                        <select name="kelas_tujuan_id" id="kelas_tujuan" class="form-control select2" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">[{{ $k->tingkat->nama_tingkat }}] {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-warning border-0 shadow-sm mt-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <small>Data rombel asal akan diarsipkan sebagai <strong>Riwayat</strong>.</small>
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
                            <div class="custom-control custom-checkbox custom-control-lg">
                                <input type="checkbox" class="custom-control-input" id="selectAll">
                                <label class="custom-control-label font-weight-bold" for="selectAll">Pilih Semua Siswa</label>
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
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg font-weight-bold" style="border-radius: 30px;" onclick="return confirm('Apakah Anda yakin ingin memproses kenaikan kelas untuk rombel ini?')">
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
    // Load Rombel when Tahun Asal changes
    $('#tahun_asal').change(function() {
        const tahunId = $(this).val();
        const rombelSelect = $('#rombel_id');
        
        if (tahunId) {
            $('#rombelLoader').removeClass('d-none');
            rombelSelect.prop('disabled', true).html('<option value="">-- Sedang memuat... --</option>');
            
            $.get('{{ route("akademik.kenaikan-kelas.get-rombel") }}', { tahunajaran_id: tahunId }, function(data) {
                let html = '<option value="">-- Pilih Rombel --</option>';
                data.forEach(function(r) {
                    html += `<option value="${r.id}">${r.nama_rombel} (${r.kelas.nama_kelas})</option>`;
                });
                rombelSelect.html(html).prop('disabled', false);
                $('#rombelLoader').addClass('d-none');
            });
        } else {
            rombelSelect.prop('disabled', true).html('<option value="">-- Pilih Rombel --</option>');
        }
    });

    // Load Siswa when Rombel changes
    $('#rombel_id').change(function() {
        const rombelId = $(this).val();
        const tahunId = $('#tahun_asal').val();
        
        if (rombelId) {
            $('#siswaPlaceholder').addClass('d-none');
            $('#siswaContainer').removeClass('d-none');
            $('#siswaListBody').html('<tr><td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data siswa...</td></tr>');

            $.get('{{ route("akademik.kenaikan-kelas.get-siswa") }}', { rombel_id: rombelId, tahunajaran_id: tahunId }, function(data) {
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

    // Select All Logic
    $('#selectAll').change(function() {
        $('.siswa-check').prop('checked', $(this).prop('checked'));
        updateCount();
    });

    $(document).on('change', '.siswa-check', function() {
        updateCount();
    });

    function updateCount() {
        const count = $('.siswa-check:checked').length;
        $('#siswaCountBadge').text(count + ' Siswa Terpilih');
    }
});
</script>

<style>
    .badge-soft-success { background-color: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2); }
    .sticky-top { z-index: 10; top: 0; }
    .table th { text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; }
    .table td { vertical-align: middle; }
</style>
@endpush
@endsection
