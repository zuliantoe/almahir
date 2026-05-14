@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-success font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> {{ $title }}</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-outline-success btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-success py-3">
                    <h5 class="text-white mb-0 font-weight-bold"><i class="fas fa-graduation-cap mr-2"></i> Form Input Nilai Akademik</h5>
                </div>
                <div class="card-body p-4" style="background-color: #fcfdfe;">
                    <form action="{{ route('penilaiandanpresensi.penilaianakademik.store') }}" method="POST" id="penilaianForm">
                        @csrf

                        {{-- Master Data Section --}}
                        <div class="row mb-4">
                            <!-- Guru Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="guru_id" class="font-weight-bold text-dark">Guru Pengampu <span class="text-danger">*</span></label>
                                    <select name="guru_id" id="guru_id" class="form-control" required {{ $isGuru ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}" {{ ($isGuru && $loggedGuruId == $guru->id) || old('guru_id') == $guru->id ? 'selected' : '' }}>
                                                {{ $guru->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($isGuru)
                                        <input type="hidden" name="guru_id" id="guru_id_hidden" value="{{ $loggedGuruId }}">
                                    @endif
                                </div>
                            </div>

                            <!-- Tahun Ajaran Section -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <div class="form-control bg-white d-flex align-items-center justify-content-between" style="border-radius: 0.5rem; height: calc(2.25rem + 2px); border: 2px solid var(--success-color);">
                                        <span class="font-weight-bold text-success small">
                                            <i class="fas fa-calendar-check mr-1"></i> {{ $activeTahunAjaran->tahunajaran ?? 'Aktif' }}
                                        </span>
                                    </div>
                                    <input type="hidden" name="tahunajaran_id" id="tahunajaran_id" value="{{ $activeTahunAjaran->id ?? '' }}">
                                </div>
                            </div>

                            <!-- Jenis Nilai Section -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenis_nilai" class="font-weight-bold text-dark">Jenis Nilai <span class="text-danger">*</span></label>
                                    <select name="jenis_nilai" id="jenis_nilai" class="form-control" required>
                                        <option value="Harian" {{ old('jenis_nilai') == 'Harian' ? 'selected' : '' }}>Harian</option>
                                        <option value="UTS" {{ old('jenis_nilai') == 'UTS' ? 'selected' : '' }}>UTS</option>
                                        <option value="UAS" {{ old('jenis_nilai') == 'UAS' ? 'selected' : '' }}>UAS</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Subject & KKM Section --}}
                        <div class="row mb-4 p-3 bg-white shadow-sm mx-1" style="border-radius: 12px; border-left: 5px solid var(--success-color);">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mapel_id" class="font-weight-bold text-dark">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select name="mapel_id" id="mapel_id" class="form-control" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($mapels as $mapel)
                                            <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                                                {{ $mapel->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kkm" class="font-weight-bold text-dark text-uppercase small text-success">KKM Otomatis</label>
                                    <input type="number" name="kkm" id="kkm" class="form-control font-weight-bold text-success" value="{{ old('kkm', 70) }}" required readonly>
                                    <small class="text-muted">Disesuaikan otomatis berdasarkan kategori mata pelajaran.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Rombel Selection Section --}}
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="rombel_id" class="font-weight-bold text-dark">Pilih Rombel Santri <span class="text-danger">*</span></label>
                                    <select name="rombel_id" id="rombel_id" class="form-control" required>
                                        <option value="">-- Pilih Rombel --</option>
                                        @foreach($rombels as $r)
                                            <option value="{{ $r->id }}" {{ old('rombel_id') == $r->id ? 'selected' : '' }}>
                                                {{ $r->nama_rombel }} ({{ $r->kelas->nama_kelas ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Rombel yang muncul adalah yang aktif di Tahun Ajaran ini.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Student List Table --}}
                        <div id="siswaTableContainer" style="display: none;">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold text-muted mb-0"><i class="fas fa-users mr-1"></i> Daftar Santri</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm shadow-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" id="searchSiswa" class="form-control border-left-0" placeholder="Cari nama santri di sini...">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive bg-white rounded shadow-sm" style="max-height: 600px; overflow-y: auto;">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light sticky-top" style="z-index: 10;">
                                        <tr>
                                            <th class="px-4" style="width: 50px;">No</th>
                                            <th>Santri</th>
                                            <th class="text-center" style="width: 150px;">Riwayat</th>
                                            <th class="text-center" style="width: 280px;">
                                                Nilai Akademik (0-100)
                                                <button type="button" id="btn-set-all-nilai" class="btn btn-xs btn-outline-success ml-2" style="font-size: 0.7rem;">
                                                    <i class="fas fa-edit mr-1"></i> Set Semua
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="siswaListBody">
                                        {{-- Will be populated by JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="noSiswaMessage" class="text-center py-5" style="display: none;">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada santri di kelas ini.</p>
                        </div>

                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-success px-5 font-weight-bold shadow-sm" id="submitBtn" style="display: none;">
                                <i class="fas fa-save mr-1"></i> Simpan Semua Nilai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fetchStudents() {
        const rombelId = document.getElementById('rombel_id').value;
        const container = document.getElementById('siswaTableContainer');
        const body = document.getElementById('siswaListBody');
        const message = document.getElementById('noSiswaMessage');
        const btn = document.getElementById('submitBtn');

        body.innerHTML = '';
        
        if (!rombelId) {
            container.style.display = 'none';
            message.style.display = 'none';
            btn.style.display = 'none';
            return;
        }

        // Show loading state
        body.innerHTML = '<tr><td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil data santri...</td></tr>';
        container.style.display = 'block';

        const url = "{{ route('penilaiandanpresensi.penilaianakademik.get-siswa-by-rombel', ':id') }}".replace(':id', rombelId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                body.innerHTML = '';
                if (data.length > 0) {
                    data.forEach((siswa, index) => {
                        body.innerHTML += `
                            <tr>
                                <td class="px-4 text-muted">${index + 1}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">${siswa.nama}</div>
                                    <small class="text-muted">NIS: ${siswa.nis || '-'}</small>
                                    <input type="hidden" name="penilaian[${index}][siswa_id]" value="${siswa.id}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info btn-history" data-siswa-id="${siswa.id}" data-siswa-nama="${siswa.nama}">
                                        <i class="fas fa-history"></i> Lihat
                                    </button>
                                </td>
                                <td class="text-center px-4">
                                    <input type="number" name="penilaian[${index}][nilai]" class="form-control text-center font-weight-bold input-nilai-akademik" 
                                           placeholder="-" min="0" max="100">
                                </td>
                            </tr>
                        `;
                    });
                    container.style.display = 'block';
                    message.style.display = 'none';
                    btn.style.display = 'inline-block';
                } else {
                    container.style.display = 'none';
                    message.style.display = 'block';
                    btn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                body.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Gagal mengambil data santri.</td></tr>';
            });
            
        // Also fetch KKM if mapel is already selected
        fetchKKM();
    }

    function fetchKKM() {
        const rombelId = document.getElementById('rombel_id').value;
        const mapelId = document.getElementById('mapel_id').value;
        const kkmInput = document.getElementById('kkm');

        if (!rombelId || !mapelId) return;

        const url = "{{ route('penilaiandanpresensi.penilaianakademik.get-kkm', [':rombelId', ':mapelId']) }}"
            .replace(':rombelId', rombelId)
            .replace(':mapelId', mapelId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                kkmInput.value = data.kkm;
            })
            .catch(error => {
                console.error('Error fetching KKM:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 manually and safely
        function initSelect2() {
            const el = $('#rombel_id');
            // Check if select2 is already initialized by checking for the class or data
            if (el.hasClass('select2-hidden-accessible')) {
                el.select2('destroy');
            }
            // Clear any potential leftover containers just in case
            el.next('.select2-container').remove();
            
            el.select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }
        
        // Initial load
        refreshGuruData(); 

        $(document).on('change', '#rombel_id', fetchStudents);
        $(document).on('change', '#mapel_id', fetchKKM);

        // AJAX for Teacher/Year Change
        function refreshGuruData() {
            let guruId = $('#guru_id').val();
            if (!guruId) {
                guruId = $('#guru_id_hidden').val();
            }
            const taId = $('#tahunajaran_id').val();
            const mapelSelect = $('#mapel_id');
            const rombelSelect = $('#rombel_id');

            if (!guruId || !taId) return;

            // Show loading
            mapelSelect.html('<option value="">Sedang memuat...</option>');
            rombelSelect.html('<option value="">Sedang memuat...</option>');

            const url = "{{ route('penilaiandanpresensi.penilaianakademik.get-data-by-guru', ':id') }}".replace(':id', guruId) + "?tahunajaran_id=" + taId;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Update Mapels
                    let mapelHtml = '<option value="">-- Pilih Mata Pelajaran --</option>';
                    data.mapels.forEach(m => {
                        mapelHtml += `<option value="${m.id}">${m.nama}</option>`;
                    });
                    mapelSelect.html(mapelHtml);

                    // Update Rombels
                    let rombelHtml = '<option value="">-- Pilih Rombel --</option>';
                    data.rombels.forEach(r => {
                        rombelHtml += `<option value="${r.id}">${r.nama_rombel}</option>`;
                    });
                    
                    rombelSelect.html(rombelHtml);
                    initSelect2(); // This will handle destroy and re-init cleanly
                    rombelSelect.trigger('change');
                    
                    // Reset student list
                    document.getElementById('siswaTableContainer').style.display = 'none';
                    document.getElementById('noSiswaMessage').style.display = 'none';
                    document.getElementById('submitBtn').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching teacher data:', error);
                    mapelSelect.html('<option value="">Gagal memuat data</option>');
                    rombelSelect.html('<option value="">Gagal memuat data</option>');
                });
        }

        $('#guru_id').on('change', refreshGuruData);
        
        // Search Filter Logic
        $(document).on('keyup', '#searchSiswa', function() {
            let value = $(this).val().toLowerCase();
            $("#siswaListBody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Keyboard Navigation
        $(document).on('keydown', '.input-nilai-akademik', function(e) {
            let inputs = $('.input-nilai-akademik:visible');
            let index = inputs.index(this);

            if (e.which === 40) { // Down
                if (index + 1 < inputs.length) {
                    inputs.eq(index + 1).focus().select();
                }
                e.preventDefault();
            } else if (e.which === 38) { // Up
                if (index > 0) {
                    inputs.eq(index - 1).focus().select();
                }
                e.preventDefault();
            }
        });

        // Handle Set Semua Nilai
        $(document).on('click', '#btn-set-all-nilai', function() {
            Swal.fire({
                title: 'Set Nilai Masal',
                text: 'Masukkan nilai yang akan diterapkan ke seluruh santri.',
                input: 'number',
                inputAttributes: { min: 0, max: 100 },
                showCancelButton: true,
                confirmButtonText: 'Terapkan',
                confirmButtonColor: '#28a745',
            }).then((result) => {
                if (result.isConfirmed && result.value !== '') {
                    $('.input-nilai-akademik').val(result.value);
                }
            });
        });

        // Handle History Click
        $(document).on('click', '.btn-history', function() {
            const siswaId = $(this).data('siswa-id');
            const siswaNama = $(this).data('siswa-nama');
            const mapelId = $('#mapel_id').val();

            if (!mapelId) {
                Swal.fire('Opps!', 'Pilih Mata Pelajaran terlebih dahulu.', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('penilaiandanpresensi.penilaianakademik.history') }}",
                method: 'GET',
                data: { siswa_id: siswaId, mapel_id: mapelId },
                success: function(data) {
                    let html = `<div class="text-left"><p class="mb-2">Santri: <b>${siswaNama}</b></p><table class="table table-sm table-bordered"><thead><tr><th>Tanggal</th><th>Jenis</th><th>Nilai</th></tr></thead><tbody>`;
                    if (data.length > 0) {
                        data.forEach(item => {
                            let date = new Date(item.created_at).toLocaleDateString('id-ID');
                            html += `<tr><td>${date}</td><td>${item.jenis_nilai}</td><td><b>${item.nilai}</b></td></tr>`;
                        });
                    } else {
                        html += '<tr><td colspan="3" class="text-center">Belum ada riwayat.</td></tr>';
                    }
                    html += `</tbody></table></div>`;
                    Swal.fire({ title: 'Riwayat Nilai', html: html, width: '500px' });
                }
            });
        });
    });
</script>
@endpush
@endsection
