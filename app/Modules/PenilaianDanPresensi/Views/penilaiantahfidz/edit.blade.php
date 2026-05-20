@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-warning font-weight-bold"><i class="fas fa-edit mr-2"></i> {{ $title }}</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('penilaiandanpresensi.penilaiantahfidz.index') }}" class="btn btn-outline-warning btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-warning py-3">
                    <h5 class="text-dark mb-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> Form Perubahan Setoran Hafalan</h5>
                </div>
                <div class="card-body p-4" style="background-color: #fffdf7;">
                    <form action="{{ route('penilaiandanpresensi.penilaiantahfidz.update', $penilaianTahfidz->id) }}" method="POST" id="tahfidzForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <!-- Guru Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="guru_id" class="font-weight-bold text-dark">Guru Pengampu <span class="text-danger">*</span></label>
                                    <select name="guru_id" id="guru_id" class="form-control" required {{ $isGuru ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}" {{ $penilaianTahfidz->guru_id == $guru->id ? 'selected' : '' }}>
                                                {{ $guru->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($isGuru)
                                        <input type="hidden" name="guru_id" value="{{ $loggedGuruId }}">
                                    @endif
                                </div>
                            </div>

                            <!-- Tahun Ajaran Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <div class="form-control bg-white d-flex align-items-center justify-content-between" style="border-radius: 0.5rem; height: calc(2.25rem + 2px); border: 2px solid #ffc107;">
                                        <span class="font-weight-bold text-warning">
                                            <i class="fas fa-calendar-check mr-2"></i> {{ $activeTahunAjaran->tahunajaran ?? '-' }}
                                        </span>
                                        <span class="badge badge-warning">Aktif</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 p-3 bg-white shadow-sm mx-1" style="border-radius: 12px; border-left: 5px solid #ffc107;">
                             <!-- Santri & Kelas (Read-only for Edit) -->
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Santri</label>
                                    <div class="form-control bg-light">
                                        <i class="fas fa-user-graduate mr-2 text-warning"></i> {{ $penilaianTahfidz->siswa->nama ?? '-' }}
                                    </div>
                                    <input type="hidden" name="siswa_id" value="{{ $penilaianTahfidz->siswa_id }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Rombel / Kelas</label>
                                    <div class="form-control bg-light">
                                        <i class="fas fa-school mr-2 text-warning"></i> {{ $penilaianTahfidz->rombel->nama_rombel ?? $penilaianTahfidz->kelas->nama_kelas ?? '-' }}
                                    </div>
                                    <input type="hidden" name="rombel_id" value="{{ $penilaianTahfidz->rombel_id }}">
                                    <input type="hidden" name="kelas_id" value="{{ $penilaianTahfidz->kelas_id }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal" class="font-weight-bold text-dark text-uppercase small">Tanggal Setoran</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $penilaianTahfidz->tanggal ? $penilaianTahfidz->tanggal->format('Y-m-d') : date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nilai" class="font-weight-bold text-dark text-uppercase small text-primary">Nilai (0-100)</label>
                                    <input type="number" name="nilai" id="nilai" class="form-control form-control-lg border-primary text-primary font-weight-bold" value="{{ $penilaianTahfidz->nilai }}" required min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark text-uppercase small">Status Capaian</label>
                                    <div class="d-flex align-items-center mt-2">
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-success {{ $penilaianTahfidz->status_capaian == 'Lolos' ? 'active' : '' }} w-50 py-2">
                                                <input type="radio" name="status_capaian" value="Lolos" {{ $penilaianTahfidz->status_capaian == 'Lolos' ? 'checked' : '' }}> <i class="fas fa-check-circle mr-1"></i> LOLOS
                                            </label>
                                            <label class="btn btn-outline-danger {{ $penilaianTahfidz->status_capaian == 'Tidak Lolos' ? 'active' : '' }} w-50 py-2">
                                                <input type="radio" name="status_capaian" value="Tidak Lolos" {{ $penilaianTahfidz->status_capaian == 'Tidak Lolos' ? 'checked' : '' }}> <i class="fas fa-times-circle mr-1"></i> TIDAK LOLOS
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row bg-light p-3 mx-1 mb-4" style="border-radius: 12px; border: 1px dashed #d4a373;">
                            <div class="col-md-6 border-right">
                                <h6 class="font-weight-bold text-muted mb-3 text-uppercase small"><i class="fas fa-book-open mr-1"></i> Dari (Awal)</h6>
                                <div class="row">
                                    <div class="col-7">
                                        <select name="surat_awal" id="surat_awal" class="form-control select2-quran" required>
                                            <option value="">Loading Surah...</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <select name="ayat_awal" id="ayat_awal" class="form-control select2-ayat" required disabled>
                                            <option value="">Ayat Awal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pl-md-4">
                                <h6 class="font-weight-bold text-muted mb-3 text-uppercase small"><i class="fas fa-book mr-1"></i> Sampai (Akhir)</h6>
                                <div class="row">
                                    <div class="col-7">
                                        <select name="surat_akhir" id="surat_akhir" class="form-control select2-quran" required>
                                            <option value="">Loading Surah...</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <select name="ayat_akhir" id="ayat_akhir" class="form-control select2-ayat" required disabled>
                                            <option value="">Ayat Akhir</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-warning px-5 font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 40px;
        border: 1px solid #e1e5ef;
        border-radius: 10px;
        padding-top: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: #4e5e7a;
        font-weight: 500;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize general select2
        if($('.select2').length) {
            $('.select2').select2({width: '100%'});
        }

        const oldSuratAwal = "{!! addslashes($penilaianTahfidz->surat_awal) !!}";
        const oldSuratAkhir = "{!! addslashes($penilaianTahfidz->surat_akhir) !!}";
        let oldAyatAwal = "{{ $penilaianTahfidz->ayat_awal }}";
        let oldAyatAkhir = "{{ $penilaianTahfidz->ayat_akhir }}";

        // Fetch Quran Data from equran.id API
        fetch('https://equran.id/api/v2/surat')
            .then(response => response.json())
            .then(res => {
                if(res.code === 200) {
                    let options = '<option value="">-- Pilih Surah --</option>';
                    
                    res.data.forEach(surat => {
                    // Populate ayat dropdown based on selected surah
                    $('#surat_awal').on('change', function() {
                        let maxAyat = $(this).find(':selected').data('ayat');
                        let ayatSelect = $('#ayat_awal');
                        ayatSelect.empty();
                        
                        // Sync hidden fields for backwards compatibility
                        $('#surat_akhir_hidden').val($(this).val());
                        
                        if(maxAyat) {
                            ayatSelect.prop('disabled', false);
                            ayatSelect.append('<option value="">-- Ayat --</option>');
                            for(let i=1; i<=maxAyat; i++) {
                                let selected = (oldAyatAwal == i) ? 'selected' : '';
                                ayatSelect.append(`<option value="${i}" ${selected}>Ayat ${i}</option>`);
                            }
                            ayatSelect.select2({ width: '100%', placeholder: 'Cari Ayat...' });
                        } else {
                            ayatSelect.prop('disabled', true);
                            ayatSelect.append('<option value="">Pilih Surah Dulu</option>');
                            if (ayatSelect.hasClass("select2-hidden-accessible")) ayatSelect.select2('destroy');
                        }
                    });

                    $('#ayat_awal').on('change', function() {
                        $('#ayat_akhir_hidden').val($(this).val());
                    });

                    // Trigger change to set max ayat initially if already selected
                    if(oldSuratAwal) $('#surat_awal').trigger('change');
                    
                    // Reset old values after initialization so manual changes won't be overridden
                    oldAyatAwal = null;
                }
            })
            .catch(error => {
                console.error("Gagal mengambil data Al-Quran", error);
                
                // Fallback: Just put the old value so it's not totally broken
                $('#surat_awal').html(`<option value="${oldSuratAwal}" selected>${oldSuratAwal}</option>`);
                $('#ayat_awal').prop('disabled', false).html(`<option value="${oldAyatAwal}" selected>Ayat ${oldAyatAwal}</option>`);
            });
    });
</script>
@endpush
@endsection
