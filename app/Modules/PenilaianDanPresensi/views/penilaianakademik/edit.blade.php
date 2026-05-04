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
                                    <label for="id_guru" class="font-weight-bold text-dark">Guru <span class="text-danger">*</span></label>
                                    <select name="id_guru" id="id_guru" class="form-control select2" required {{ $isGuru ? 'readonly' : '' }}>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}" {{ $penilaianAkademik->id_guru == $guru->id ? 'selected' : '' }}>
                                                {{ $guru->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($isGuru)
                                        <input type="hidden" name="id_guru" value="{{ $loggedGuruId }}">
                                    @endif
                                </div>
                            </div>

                            <!-- Tahun Ajaran Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_tahun_ajaran" class="font-weight-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <div class="form-control bg-light d-flex align-items-center justify-content-between" style="border-radius: 0.5rem; height: calc(2.25rem + 2px);">
                                        <span class="font-weight-bold text-success">
                                            <i class="fas fa-calendar-check mr-2"></i> {{ $penilaianAkademik->tahunAjaran->tahunajaran ?? ($activeTahunAjaran->tahunajaran ?? '-') }}
                                        </span>
                                        <span class="badge badge-success">Aktif</span>
                                    </div>
                                    <input type="hidden" name="id_tahun_ajaran" id="id_tahun_ajaran" value="{{ $penilaianAkademik->id_tahun_ajaran ?? ($activeTahunAjaran->id ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Mata Pelajaran Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_mapel" class="font-weight-bold text-dark">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select name="id_mapel" id="id_mapel" class="form-control" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($mapels as $mapel)
                                            <option value="{{ $mapel->id }}" {{ $penilaianAkademik->id_mapel == $mapel->id ? 'selected' : '' }}>
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
                                    <input type="hidden" name="id_siswa" value="{{ $penilaianAkademik->id_siswa }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nilai" class="font-weight-bold text-dark">Nilai Akademik <span class="text-danger">*</span></label>
                                    <input type="number" name="nilai" id="nilai" class="form-control form-control-lg border-success text-success font-weight-bold" value="{{ $penilaianAkademik->nilai }}" required min="0" max="100">
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
        const guruId = document.getElementById('id_guru').value;
        const mapelSelect = document.getElementById('id_mapel');
        const currentMapelId = "{{ $penilaianAkademik->id_mapel }}";
        
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
            }
        }
    }

    document.getElementById('id_guru').addEventListener('change', updateMapelOptions);
    document.getElementById('id_mapel').addEventListener('change', setKKM);

    // Jalankan saat pertama kali halaman dibuka agar data langsung muncul
    updateMapelOptions();
    setKKM();
});
</script>
@endsection
