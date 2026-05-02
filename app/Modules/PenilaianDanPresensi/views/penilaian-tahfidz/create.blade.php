@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-warning font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> {{ $title }}</h3>
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
                    <h5 class="text-dark mb-0 font-weight-bold"><i class="fas fa-file-medical mr-2"></i> Form Setoran Hafalan Baru</h5>
                </div>
                <div class="card-body p-4" style="background-color: #fffdf7;">
                    <form action="{{ route('penilaiandanpresensi.penilaiantahfidz.store') }}" method="POST" id="tahfidzForm">
                        @csrf
                        
                        <div class="row mb-4">
                            <!-- Guru Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_guru" class="font-weight-bold text-dark">Guru Pengampu <span class="text-danger">*</span></label>
                                    <select name="id_guru" id="id_guru" class="form-control select2" required {{ $isGuru ? 'readonly' : '' }}>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}" {{ ($isGuru && $loggedGuruId == $guru->id) || old('id_guru') == $guru->id ? 'selected' : '' }}>
                                                {{ $guru->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($isGuru)
                                        <input type="hidden" name="id_guru" value="{{ $loggedGuruId }}">
                                    @endif
                                </div>
                            </div>

                            <!-- Tahun Ajaran Section (Badge Style) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <div class="form-control bg-white d-flex align-items-center justify-content-between" style="border-radius: 0.5rem; height: calc(2.25rem + 2px); border: 2px solid #ffc107;">
                                        <span class="font-weight-bold text-warning">
                                            <i class="fas fa-calendar-check mr-2"></i> {{ $activeTahunAjaran->tahunajaran ?? 'Pilih TA Aktif' }}
                                        </span>
                                        <span class="badge badge-warning">Aktif</span>
                                    </div>
                                    {{-- Kolom tahun ajaran belum ada di DB Tahfidz, tapi kita siapkan datanya --}}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 p-3 bg-white shadow-sm mx-1" style="border-radius: 12px; border-left: 5px solid #ffc107;">
                            <!-- Kelas Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_kelas" class="font-weight-bold text-dark">Pilih Kelas <span class="text-danger">*</span></label>
                                    <select name="id_kelas" id="id_kelas" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Siswa Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_siswa" class="font-weight-bold text-dark">Nama Santri <span class="text-danger">*</span></label>
                                    <select name="id_siswa" id="id_siswa" class="form-control" required>
                                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal" class="font-weight-bold text-dark text-uppercase small">Tanggal Setoran</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="input_mode" class="font-weight-bold text-dark text-uppercase small">Metode Input</label>
                                    <select id="input_mode" class="form-control">
                                        <option value="detail">Per Ayat (Detail)</option>
                                        <option value="global">Per Juz / Rentang (Global)</option>
                                        <option value="halaman">Per Halaman (Global)</option>
                                    </select>
                                </div>
                            </div>
                            <div id="juz-container" class="col-md-4 d-none">
                                <div class="form-group">
                                    <label for="pilih_juz" class="font-weight-bold text-dark text-uppercase small">Pilih Juz</label>
                                    <select id="pilih_juz" class="form-control select2">
                                        <option value="">-- Pilih Juz (1-30) --</option>
                                        @for($i=1; $i<=30; $i++)
                                            <option value="{{ $i }}">Juz {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div id="halaman-container" class="col-md-4 d-none">
                                <div class="form-group">
                                    <label for="pilih_halaman" class="font-weight-bold text-dark text-uppercase small">Nomor Halaman</label>
                                    <div class="input-group">
                                        <input type="number" id="pilih_halaman" class="form-control" min="1" max="604" placeholder="Hal 1-604">
                                        <div class="input-group-append">
                                            <button type="button" id="btn-fetch-halaman" class="btn btn-warning"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Masukkan 1-604 lalu cari</small>
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
                                        <select id="rentang_awal" class="form-control select2-ayat" disabled>
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
                                        <select id="rentang_akhir" class="form-control select2-ayat" disabled>
                                            <option value="">Ayat Akhir</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="button" id="btn-generate" class="btn btn-primary btn-block font-weight-bold" style="border-radius: 10px;"><i class="fas fa-magic mr-1"></i> Generate Form Penilaian</button>
                            </div>
                        </div>

                        <!-- Area Grid Ayat -->
                        <div id="ayat-grid-container" class="d-none mb-4">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-list-ol mr-1"></i> Form Penilaian Per Ayat</h6>
                            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e1e5ef;">
                                <table class="table table-bordered bg-white mb-0">
                                    <thead style="background: #f8f9fc;">
                                        <tr>
                                            <th class="text-center" width="30%">Materi / Rentang</th>
                                            <th>Nilai (0-100) <button type="button" id="btn-rata-nilai" class="btn btn-xs btn-outline-primary ml-2">Set Semua</button></th>
                                            <th>Status Capaian <button type="button" id="btn-rata-status" class="btn btn-xs btn-outline-success ml-2">Lolos Semua</button></th>
                                        </tr>
                                    </thead>
                                    <tbody id="ayat-grid-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-warning px-5 font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Setoran
                            </button>
                        </div>
                    </form>
                </div>
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
    const siswasData = @json($siswas);
    const oldIdSiswa = "{{ old('id_siswa') }}";

    function updateSiswaOptions() {
        const kelasId = document.getElementById('id_kelas').value;
        const siswaSelect = document.getElementById('id_siswa');
        
        siswaSelect.innerHTML = '<option value="">-- Pilih Santri --</option>';
        
        if (!kelasId) {
            siswaSelect.innerHTML = '<option value="">-- Pilih Kelas Terlebih Dahulu --</option>';
            return;
        }

        const filteredSiswas = siswasData.filter(siswa => siswa.kelas_id == kelasId);
        
        if (filteredSiswas.length === 0) {
            siswaSelect.innerHTML = '<option value="">-- Tidak ada santri di kelas ini --</option>';
            return;
        }

        filteredSiswas.forEach(siswa => {
            const isSelected = (siswa.id == oldIdSiswa) ? 'selected' : '';
            siswaSelect.innerHTML += `<option value="${siswa.id}" ${isSelected}>${siswa.nama}</option>`;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const juzMapping = {
            1: { start_surah: 0, start_ayat: 1, end_surah: 1, end_ayat: 141 },
            2: { start_surah: 1, start_ayat: 142, end_surah: 1, end_ayat: 252 },
            3: { start_surah: 1, start_ayat: 253, end_surah: 2, end_ayat: 92 },
            4: { start_surah: 2, start_ayat: 93, end_surah: 3, end_ayat: 23 },
            5: { start_surah: 3, start_ayat: 24, end_surah: 3, end_ayat: 147 },
            6: { start_surah: 3, start_ayat: 148, end_surah: 4, end_ayat: 81 },
            7: { start_surah: 4, start_ayat: 82, end_surah: 5, end_ayat: 110 },
            8: { start_surah: 5, start_ayat: 111, end_surah: 6, end_ayat: 87 },
            9: { start_surah: 6, start_ayat: 88, end_surah: 7, end_ayat: 40 },
            10: { start_surah: 7, start_ayat: 41, end_surah: 8, end_ayat: 92 },
            11: { start_surah: 8, start_ayat: 93, end_surah: 9, end_ayat: 5 },
            12: { start_surah: 10, start_ayat: 6, end_surah: 11, end_ayat: 52 },
            13: { start_surah: 11, start_ayat: 53, end_surah: 13, end_ayat: 52 },
            14: { start_surah: 14, start_ayat: 1, end_surah: 15, end_ayat: 128 },
            15: { start_surah: 16, start_ayat: 1, end_surah: 17, end_ayat: 74 },
            16: { start_surah: 17, start_ayat: 75, end_surah: 19, end_ayat: 135 },
            17: { start_surah: 20, start_ayat: 1, end_surah: 21, end_ayat: 78 },
            18: { start_surah: 22, start_ayat: 1, end_surah: 24, end_ayat: 20 },
            19: { start_surah: 24, start_ayat: 21, end_surah: 26, end_ayat: 55 },
            20: { start_surah: 26, start_ayat: 56, end_surah: 28, end_ayat: 45 },
            21: { start_surah: 28, start_ayat: 46, end_surah: 32, end_ayat: 30 },
            22: { start_surah: 32, start_ayat: 31, end_surah: 35, end_ayat: 27 },
            23: { start_surah: 35, start_ayat: 28, end_surah: 38, end_ayat: 31 },
            24: { start_surah: 38, start_ayat: 32, end_surah: 40, end_ayat: 46 },
            25: { start_surah: 40, start_ayat: 47, end_surah: 44, end_ayat: 37 },
            26: { start_surah: 45, start_ayat: 1, end_surah: 50, end_ayat: 30 },
            27: { start_surah: 50, start_ayat: 31, end_surah: 56, end_ayat: 29 },
            28: { start_surah: 57, start_ayat: 1, end_surah: 65, end_ayat: 12 },
            29: { start_surah: 66, start_ayat: 1, end_surah: 76, end_ayat: 50 },
            30: { start_surah: 77, start_ayat: 1, end_surah: 113, end_ayat: 6 }
        };

        $('#input_mode').on('change', function() {
            let mode = $(this).val();
            $('#juz-container, #halaman-container').addClass('d-none');
            
            if (mode === 'global') {
                $('#juz-container').removeClass('d-none');
            } else if (mode === 'halaman') {
                $('#halaman-container').removeClass('d-none');
            }
            
            $('#pilih_juz').val('').trigger('change');
            $('#pilih_halaman').val('');
        });

        $('#btn-fetch-halaman').on('click', function() {
            let page = $('#pilih_halaman').val();
            if (!page || page < 1 || page > 604) {
                Swal.fire('Opps!', 'Masukkan nomor halaman valid (1-604).', 'warning');
                return;
            }

            Swal.fire({
                title: 'Mencari Halaman...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`https://api.alquran.cloud/v1/page/${page}/quran-uthmani`)
                .then(response => response.json())
                .then(res => {
                    Swal.close();
                    if (res.code === 200) {
                        let ayahs = res.data.ayahs;
                        let first = ayahs[0];
                        let last = ayahs[ayahs.length - 1];

                        // Find surah indexes by name latin (matching quranData)
                        let startSurahIdx = -1;
                        let endSurahIdx = -1;

                        // We need to match based on surah number since API returns surah.number
                        // AlQuran Cloud Surah numbers are 1-indexed
                        startSurahIdx = first.surah.number - 1;
                        endSurahIdx = last.surah.number - 1;

                        $('#surat_awal').val(startSurahIdx).trigger('change');
                        setTimeout(() => {
                            $('#rentang_awal').val(first.numberInSurah).trigger('change');
                            $('#surat_akhir').val(endSurahIdx).trigger('change');
                            setTimeout(() => {
                                $('#rentang_akhir').val(last.numberInSurah).trigger('change');
                            }, 300);
                        }, 300);
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire('Error', 'Gagal mengambil data halaman dari API.', 'error');
                });
        });

        $('#pilih_juz').on('change', function() {
            let juz = $(this).val();
            if (juz && juzMapping[juz]) {
                let range = juzMapping[juz];
                $('#surat_awal').val(range.start_surah).trigger('change');
                setTimeout(() => {
                    $('#rentang_awal').val(range.start_ayat).trigger('change');
                    $('#surat_akhir').val(range.end_surah).trigger('change');
                    setTimeout(() => {
                        $('#rentang_akhir').val(range.end_ayat).trigger('change');
                    }, 300);
                }, 300);
            }
        });

        document.getElementById('id_kelas').addEventListener('change', updateSiswaOptions);
        
        if (document.getElementById('id_kelas').value) {
            updateSiswaOptions();
        }

        // Initialize general select2
        if($('.select2').length) {
            $('.select2').select2({width: '100%'});
        }

        // Fetch Quran Data from equran.id API
        fetch('https://equran.id/api/v2/surat')
            .then(response => response.json())
            .then(res => {
                if(res.code === 200) {
                    let quranData = res.data;
                    
                    let options = '<option value="">-- Pilih Surah --</option>';
                    quranData.forEach((surat, index) => {
                        options += `<option value="${index}" data-ayat="${surat.jumlahAyat}" data-nama="${surat.namaLatin}">${surat.nomor}. ${surat.namaLatin}</option>`;
                    });
                    
                    $('#surat_awal').html(options).select2({
                        placeholder: "Cari Surah...",
                        allowClear: true,
                        width: '100%'
                    });
                    
                    $('#surat_akhir').html(options).select2({
                        placeholder: "Cari Surah...",
                        allowClear: true,
                        width: '100%'
                    });

                    // Populate ayat dropdown based on selected surah awal
                    $('#surat_awal').on('change', function() {
                        let maxAyat = $(this).find(':selected').data('ayat');
                        let selectAwal = $('#rentang_awal');
                        selectAwal.empty();
                        
                        if(maxAyat) {
                            selectAwal.prop('disabled', false).append('<option value="">Ayat Awal</option>');
                            for(let i=1; i<=maxAyat; i++) {
                                selectAwal.append(`<option value="${i}">${i}</option>`);
                            }
                            selectAwal.select2({ width: '100%', placeholder: 'Ayat Awal' });
                        } else {
                            selectAwal.prop('disabled', true).append('<option value="">Ayat Awal</option>');
                        }
                    });

                    // Populate ayat dropdown based on selected surah akhir
                    $('#surat_akhir').on('change', function() {
                        let maxAyat = $(this).find(':selected').data('ayat');
                        let selectAkhir = $('#rentang_akhir');
                        selectAkhir.empty();
                        
                        if(maxAyat) {
                            selectAkhir.prop('disabled', false).append('<option value="">Ayat Akhir</option>');
                            for(let i=1; i<=maxAyat; i++) {
                                selectAkhir.append(`<option value="${i}">${i}</option>`);
                            }
                            selectAkhir.select2({ width: '100%', placeholder: 'Ayat Akhir' });
                        } else {
                            selectAkhir.prop('disabled', true).append('<option value="">Ayat Akhir</option>');
                        }
                    });

                    $('#btn-generate').on('click', function() {
                        let suratAwalIdx = parseInt($('#surat_awal').val());
                        let suratAkhirIdx = parseInt($('#surat_akhir').val());
                        let awal = parseInt($('#rentang_awal').val());
                        let akhir = parseInt($('#rentang_akhir').val());
                        let mode = $('#input_mode').val();

                        if(isNaN(suratAwalIdx) || isNaN(suratAkhirIdx) || isNaN(awal) || isNaN(akhir)) {
                            Swal.fire('Opps!', 'Lengkapi pilihan Surah Awal, Ayat Awal, Surah Akhir, dan Ayat Akhir.', 'warning');
                            return;
                        }

                        if(suratAwalIdx > suratAkhirIdx) {
                            Swal.fire('Opps!', 'Surah Akhir harus sama atau setelah Surah Awal.', 'warning');
                            return;
                        }

                        if(suratAwalIdx === suratAkhirIdx && awal > akhir) {
                            Swal.fire('Opps!', 'Pada surah yang sama, Ayat Akhir harus lebih besar atau sama dengan Ayat Awal.', 'warning');
                            return;
                        }

                        let tbody = $('#ayat-grid-body');
                        tbody.empty();
                        let rowIndex = 0;

                        if (mode === 'global') {
                            // Single row for the whole range
                            let surahAwal = quranData[suratAwalIdx];
                            let surahAkhir = quranData[suratAkhirIdx];
                            let label = `${surahAwal.namaLatin} (${awal}) s/d ${surahAkhir.namaLatin} (${akhir})`;
                            
                            tbody.append(`
                                <tr>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-primary">${label}</div>
                                        <small class="text-muted">Setoran Global / Rentang</small>
                                        <input type="hidden" name="surat_awal[]" value="${surahAwal.namaLatin}">
                                        <input type="hidden" name="surat_akhir[]" value="${surahAkhir.namaLatin}">
                                        <input type="hidden" name="ayat_awal[]" value="${awal}">
                                        <input type="hidden" name="ayat_akhir[]" value="${akhir}">
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" name="nilai[]" class="form-control input-nilai" placeholder="0-100" min="0" max="100" required value="100">
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-success w-50 active">
                                                <input type="radio" name="status_capaian[0]" value="Lolos" checked required> Lolos
                                            </label>
                                            <label class="btn btn-outline-danger w-50">
                                                <input type="radio" name="status_capaian[0]" value="Tidak Lolos" required> Tidak Lolos
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        } else {
                            // Detailed rows (existing logic)
                            for(let s = suratAwalIdx; s <= suratAkhirIdx; s++) {
                                let surah = quranData[s];
                                let startAyat = (s === suratAwalIdx) ? awal : 1;
                                let endAyat = (s === suratAkhirIdx) ? akhir : surah.jumlahAyat;

                                for(let i = startAyat; i <= endAyat; i++) {
                                    tbody.append(`
                                        <tr>
                                            <td class="align-middle">
                                                <div class="font-weight-bold text-dark">${surah.namaLatin}</div>
                                                <small class="text-muted">Ayat ${i}</small>
                                                <input type="hidden" name="surat_awal[]" value="${surah.namaLatin}">
                                                <input type="hidden" name="surat_akhir[]" value="${surah.namaLatin}">
                                                <input type="hidden" name="ayat_awal[]" value="${i}">
                                                <input type="hidden" name="ayat_akhir[]" value="${i}">
                                            </td>
                                            <td class="align-middle">
                                                <input type="number" name="nilai[]" class="form-control input-nilai" placeholder="0-100" min="0" max="100" required>
                                            </td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                                    <label class="btn btn-outline-success w-50">
                                                        <input type="radio" name="status_capaian[${rowIndex}]" value="Lolos" required> Lolos
                                                    </label>
                                                    <label class="btn btn-outline-danger w-50">
                                                        <input type="radio" name="status_capaian[${rowIndex}]" value="Tidak Lolos" required> Tidak Lolos
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    `);
                                    rowIndex++;
                                }
                            }
                        }

                        // Re-initialize buttons since they are dynamically added
                        $('.btn-group-toggle').each(function() {
                            $(this).find('label').on('click', function() {
                                $(this).addClass('active').siblings().removeClass('active');
                                $(this).find('input').prop('checked', true);
                            });
                        });

                        $('#ayat-grid-container').removeClass('d-none').hide().fadeIn();
                    });

                    // Utility Buttons
                    $('#btn-rata-nilai').on('click', function() {
                        Swal.fire({
                            title: 'Set Semua Nilai',
                            input: 'number',
                            inputAttributes: {
                                min: 0,
                                max: 100,
                                step: 1
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Terapkan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                $('.input-nilai').val(result.value);
                            }
                        });
                    });

                    $('#btn-rata-status').on('click', function() {
                        $('#ayat-grid-body .btn-outline-success').click();
                    });

                }
            })
            .catch(error => {
                console.error("Gagal mengambil data Al-Quran", error);
                $('#surat_awal').html('<option value="">Gagal meload API</option>');
                $('#surat_akhir').html('<option value="">Gagal meload API</option>');
            });
    });
</script>
@endpush
@endsection