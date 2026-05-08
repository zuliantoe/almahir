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
                                    <label for="id_guru" class="font-weight-bold text-dark">Guru Pengampu <span class="text-danger">*</span></label>
                                    <select name="id_guru" id="id_guru" class="form-control" required {{ $isGuru ? 'readonly' : '' }}>
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
                                    <div class="form-control bg-white d-flex align-items-center justify-content-between" style="border-radius: 0.5rem; height: calc(2.25rem + 2px); border: 2px solid var(--success-color);">
                                        <span class="font-weight-bold text-success">
                                            <i class="fas fa-calendar-check mr-2"></i> {{ $activeTahunAjaran->tahunajaran ?? 'Pilih TA Aktif' }}
                                        </span>
                                        <span class="badge badge-success">Aktif</span>
                                    </div>
                                    <input type="hidden" name="id_tahun_ajaran" id="id_tahun_ajaran" value="{{ $activeTahunAjaran->id ?? '' }}">
                                </div>
                            </div>
                        </div>

                        {{-- Subject & KKM Section --}}
                        <div class="row mb-4 p-3 bg-white shadow-sm mx-1" style="border-radius: 12px; border-left: 5px solid var(--success-color);">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_mapel" class="font-weight-bold text-dark">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select name="id_mapel" id="id_mapel" class="form-control" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($mapels as $mapel)
                                            <option value="{{ $mapel->id }}" {{ old('id_mapel') == $mapel->id ? 'selected' : '' }}>
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

                        {{-- Class Selection Section --}}
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="kelas_id" class="font-weight-bold text-dark">Pilih Kelas Santri <span class="text-danger">*</span></label>
                                    <select name="kelas_id" id="kelas_id" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
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
    const siswasData = @json($siswas);
    const allMapelsData = @json($allMapels);

    function populateSiswaTable() {
        const kelasId = document.getElementById('kelas_id').value;
        const container = document.getElementById('siswaTableContainer');
        const body = document.getElementById('siswaListBody');
        const message = document.getElementById('noSiswaMessage');
        const btn = document.getElementById('submitBtn');

        body.innerHTML = '';
        
        if (!kelasId) {
            container.style.display = 'none';
            message.style.display = 'none';
            btn.style.display = 'none';
            return;
        }

        const filteredSiswas = siswasData.filter(s => s.kelas_id == kelasId);

        if (filteredSiswas.length > 0) {
            filteredSiswas.forEach((siswa, index) => {
                body.innerHTML += `
                    <tr>
                        <td class="px-4 text-muted">${index + 1}</td>
                        <td>
                            <div class="font-weight-bold text-dark">${siswa.nama}</div>
                            <small class="text-muted">NIS: ${siswa.nis || '-'}</small>
                            <input type="hidden" name="penilaian[${index}][id_siswa]" value="${siswa.id}">
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
    }

    function setKKM() {
        const mapelId = document.getElementById('id_mapel').value;
        if (!mapelId) return;

        const selectedMapel = allMapelsData.find(m => m.id == mapelId);

        if (selectedMapel) {
            const mapelNama = selectedMapel.nama.toLowerCase();
            const kategoriNama = selectedMapel.kategori ? selectedMapel.kategori.kategori : '';
            const kkmInput = document.getElementById('kkm');

            const keywordsUmum = ['ipa', 'ips', 'matematika', 'inggris', 'indonesia', 'fisika', 'kimia', 'biologi', 'pkn', 'sejarah', 'seni', 'penjas', 'olahraga'];
            const keywordsDiniyyah = ['tahfidz', 'arab', 'agama', 'fiqih', 'aqidah', 'hadits', 'diniyyah', 'quran', 'adab', 'sirah'];

            let isUmum = kategoriNama === 'Nasional' || keywordsUmum.some(key => mapelNama.includes(key));
            let isDiniyyah = kategoriNama === 'Internal' || keywordsDiniyyah.some(key => mapelNama.includes(key));

            if (isUmum) {
                kkmInput.value = 70;
            } else if (isDiniyyah) {
                kkmInput.value = 75;
            } else {
                kkmInput.value = 70;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('kelas_id').addEventListener('change', populateSiswaTable);
        document.getElementById('id_mapel').addEventListener('change', setKKM);
        
        // Search Filter Logic
        $(document).on('keyup', '#searchSiswa', function() {
            let value = $(this).val().toLowerCase();
            $("#siswaListBody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Keyboard Navigation (Arrow keys)
        $(document).on('keydown', '.input-nilai-akademik', function(e) {
            let inputs = $('.input-nilai-akademik:visible');
            let index = inputs.index(this);

            if (e.which === 40) { // Down arrow
                if (index + 1 < inputs.length) {
                    inputs.eq(index + 1).focus().select();
                }
                e.preventDefault();
            } else if (e.which === 38) { // Up arrow
                if (index > 0) {
                    inputs.eq(index - 1).focus().select();
                }
                e.preventDefault();
            }
        });

        // Handle Set Semua Nilai
        $(document).on('click', '#btn-set-all-nilai', function() {
            Swal.fire({
                title: 'Set Nilai untuk Semua Santri',
                text: 'Masukkan nilai yang akan diterapkan ke seluruh santri di tabel ini.',
                input: 'number',
                inputAttributes: {
                    min: 0,
                    max: 100,
                    step: 1
                },
                showCancelButton: true,
                confirmButtonText: 'Terapkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
            }).then((result) => {
                if (result.isConfirmed && result.value !== '') {
                    $('.input-nilai-akademik').val(result.value);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Nilai ${result.value} telah diterapkan ke semua santri.`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        if (document.getElementById('kelas_id').value) {
            populateSiswaTable();
        }
        if (document.getElementById('id_mapel').value) {
            setKKM();
        }
    });
</script>
@endpush
@endsection
