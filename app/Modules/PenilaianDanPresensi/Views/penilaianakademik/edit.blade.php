@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-success"><i class="fas fa-edit mr-2"></i> {{ $title }}</h3>
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
                    <h5 class="text-white mb-0"><i class="fas fa-file-invoice mr-2"></i> Form Perubahan Nilai</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('penilaiandanpresensi.penilaianakademik.update', $penilaianAkademik->id) }}" method="POST" id="penilaianForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <!-- Guru Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="guru_id" class="font-weight-bold text-dark">Guru <span class="text-danger">*</span></label>
                                    <select name="guru_id" id="guru_id" class="form-control select2" required {{ $isGuru ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}" {{ $penilaianAkademik->guru_id == $guru->id ? 'selected' : '' }}>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tahunajaran_id" class="font-weight-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <div class="form-control bg-light d-flex align-items-center justify-content-between" style="border-radius: 0.5rem; height: calc(2.25rem + 2px);">
                                        <span class="font-weight-bold text-success small">
                                            <i class="fas fa-calendar-check mr-1"></i> {{ $penilaianAkademik->tahunAjaran->tahunajaran ?? ($activeTahunAjaran->tahunajaran ?? '-') }}
                                        </span>
                                    </div>
                                    <input type="hidden" name="tahunajaran_id" id="tahunajaran_id" value="{{ $penilaianAkademik->tahunajaran_id ?? ($activeTahunAjaran->id ?? '') }}">
                                </div>
                            </div>

                            <!-- Jenis Nilai Section -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenis_nilai" class="font-weight-bold text-dark">Jenis Nilai <span class="text-danger">*</span></label>
                                    <select name="jenis_nilai" id="jenis_nilai" class="form-control" required>
                                        <option value="Harian" {{ $penilaianAkademik->jenis_nilai == 'Harian' ? 'selected' : '' }}>Harian</option>
                                        <option value="UTS" {{ $penilaianAkademik->jenis_nilai == 'UTS' ? 'selected' : '' }}>UTS</option>
                                        <option value="UAS" {{ $penilaianAkademik->jenis_nilai == 'UAS' ? 'selected' : '' }}>UAS</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Mata Pelajaran Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mapel_id" class="font-weight-bold text-dark">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select name="mapel_id" id="mapel_id" class="form-control" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($mapels as $mapel)
                                            <option value="{{ $mapel->id }}" {{ $penilaianAkademik->mapel_id == $mapel->id ? 'selected' : '' }}>
                                                {{ $mapel->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- KKM Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kkm" class="font-weight-bold text-dark">KKM <span class="text-danger">*</span></label>
                                    <input type="number" name="kkm" id="kkm" class="form-control" value="{{ $penilaianAkademik->kkm }}" required min="0" max="100">
                                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Nilai KKM akan terisi otomatis berdasarkan kategori mata pelajaran.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                             <!-- Kelas & Siswa Section (Read-only for Edit) -->
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Santri & Kelas</label>
                                    <div class="form-control bg-light d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold"><i class="fas fa-user-graduate mr-2 text-success"></i> {{ $penilaianAkademik->siswa->nama ?? '-' }}</span>
                                        <span class="badge badge-info shadow-sm">{{ $penilaianAkademik->siswa->kelas->nama_kelas ?? '-' }}</span>
                                    </div>
                                    <input type="hidden" name="siswa_id" value="{{ $penilaianAkademik->siswa_id }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nilai" class="font-weight-bold text-dark">Nilai Akademik <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="nilai" id="nilai" class="form-control form-control-lg border-success text-success font-weight-bold" value="{{ $penilaianAkademik->nilai }}" required min="0" max="100">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info btn-history" data-siswa-id="{{ $penilaianAkademik->siswa_id }}" data-siswa-nama="{{ $penilaianAkademik->siswa->nama }}">
                                                <i class="fas fa-history mr-1"></i> Riwayat
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-success px-5 shadow-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jadwalsData = @json($jadwals);
    const allMapelsData = @json($allMapels);

    function updateMapelOptions() {
        const guruId = document.getElementById('guru_id').value;
        const mapelSelect = document.getElementById('mapel_id');
        const currentMapelId = "{{ $penilaianAkademik->mapel_id }}";
        
        if (!guruId) {
            mapelSelect.innerHTML = '<option value="">-- Pilih Guru terlebih dahulu --</option>';
            return;
        }

        const mapelIds = new Set();
        let options = '<option value="">-- Pilih Mata Pelajaran --</option>';
        
        const guruJadwals = jadwalsData.filter(j => j.guru_id == guruId);
        
        if (guruJadwals.length > 0) {
            guruJadwals.forEach(jadwal => {
                if (jadwal.mata_pelajaran && !mapelIds.has(jadwal.mata_pelajaran.id)) {
                    mapelIds.add(jadwal.mata_pelajaran.id);
                    const selected = jadwal.mata_pelajaran.id == currentMapelId ? 'selected' : '';
                    options += `<option value="${jadwal.mata_pelajaran.id}" ${selected}>${jadwal.mata_pelajaran.nama}</option>`;
                }
            });
        } else {
            options = '<option value="">-- Pilih Mata Pelajaran (Cadangan) --</option>';
            allMapelsData.forEach(mapel => {
                const selected = mapel.id == currentMapelId ? 'selected' : '';
                options += `<option value="${mapel.id}" ${selected}>${mapel.nama}</option>`;
            });
        }

        mapelSelect.innerHTML = options;
    }

    // Auto-set KKM logic
    function setKKM() {
        const mapelId = document.getElementById('mapel_id').value;
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
            }
        }
    }

    document.getElementById('guru_id').addEventListener('change', updateMapelOptions);
    document.getElementById('mapel_id').addEventListener('change', setKKM);

    // Handle History Click
    $(document).on('click', '.btn-history', function() {
        const siswaId = $(this).data('siswa-id');
        const siswaNama = $(this).data('siswa-nama');
        const mapelId = $('#mapel_id').val();

        if (!mapelId) {
            Swal.fire('Opps!', 'Pilih Mata Pelajaran terlebih dahulu.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Memuat Riwayat...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "{{ route('penilaiandanpresensi.penilaianakademik.history') }}",
            method: 'GET',
            data: { siswa_id: siswaId, mapel_id: mapelId },
            success: function(data) {
                Swal.close();
                let html = `
                    <div class="text-left">
                        <p class="mb-2 font-weight-bold">Santri: <span class="text-primary">${siswaNama}</span></p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis</th>
                                        <th>Nilai</th>
                                        <th>TA</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                if (data.length > 0) {
                    data.forEach(item => {
                        let date = new Date(item.created_at).toLocaleDateString('id-ID');
                        let color = item.nilai >= $('#kkm').val() ? 'text-success' : 'text-danger';
                        html += `
                            <tr>
                                <td>${date}</td>
                                <td>${item.jenis_nilai || '-'}</td>
                                <td class="font-weight-bold ${color}">${item.nilai}</td>
                                <td><small>${item.tahun_ajaran ? item.tahun_ajaran.tahunajaran : '-'}</small></td>
                            </tr>
                        `;
                    });
                } else {
                    html += '<tr><td colspan="4" class="text-center py-3 text-muted">Belum ada riwayat nilai.</td></tr>';
                }

                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: '<i class="fas fa-history mr-2"></i> Riwayat Nilai Akademik',
                    html: html,
                    width: '600px',
                    confirmButtonText: 'Tutup'
                });
            },
            error: function() {
                Swal.close();
                Swal.fire('Error', 'Gagal memuat data riwayat.', 'error');
            }
        });
    });

    // Jalankan saat pertama kali halaman dibuka agar data langsung muncul
    updateMapelOptions();
    setKKM();
});
</script>
@endsection
