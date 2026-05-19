@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Presensi" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.presensi.update', $presensi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="kelas_id">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $kelasItem)
                        <option value="{{ $kelasItem->id }}" {{ old('kelas_id', $presensi->siswa?->kelas_id) == $kelasItem->id ? 'selected' : '' }}>{{ $kelasItem->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="siswa_id">Siswa</label>
                <select name="siswa_id" id="siswa_id" class="form-control" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('siswa_id', $presensi->siswa_id) == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="guru_id">Guru</label>
                <select name="guru_id" id="guru_id" class="form-control" required>
                    <option value="">Pilih Guru</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" {{ old('guru_id', $presensi->guru_id) == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="mapel_id">Mata Pelajaran</label>
                <select name="mapel_id" id="mapel_id" class="form-control" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ old('mapel_id', $presensi->mapel_id) == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama ?? $mapel->name ?? 'Mapel '.$mapel->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jadwal_pelajaran_id">Jadwal Pelajaran</label>
                <select name="jadwal_pelajaran_id" id="jadwal_pelajaran_id" class="form-control" required>
                    <option value="">Pilih Jadwal Pelajaran</option>
                    @foreach($jadwals as $jadwal)
                        <option value="{{ $jadwal->id }}" {{ old('jadwal_pelajaran_id', $presensi->jadwal_pelajaran_id) == $jadwal->id ? 'selected' : '' }}>{{ $jadwal->hari }} - {{ \Carbon\Carbon::parse($jadwal->jamawal)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jamakhir)->format('H:i') }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jam">Jam</label>
                <input type="time" name="jam" id="jam" class="form-control" value="{{ old('jam', \Carbon\Carbon::parse($presensi->jam)->format('H:i')) }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="">Pilih Status</option>
                    <option value="Hadir" {{ old('status', $presensi->status) == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Izin" {{ old('status', $presensi->status) == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ old('status', $presensi->status) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Alpha" {{ old('status', $presensi->status) == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Sekolah" {{ old('kategori', $presensi->kategori) == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                    <option value="Pengajian" {{ old('kategori', $presensi->kategori) == 'Pengajian' ? 'selected' : '' }}>Pengajian</option>
                    <option value="Ekstrakurikuler" {{ old('kategori', $presensi->kategori) == 'Ekstrakurikuler' ? 'selected' : '' }}>Ekstrakurikuler</option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
const siswasData = @json($siswas);
const jadwalsData = @json($jadwals);

function getStudentOptions() {
    const kelasId = document.getElementById('kelas_id').value;
    if (!kelasId) {
        return '<option value="">-- Pilih Kelas terlebih dahulu --</option>';
    }

    const siswaList = siswasData.filter(siswa => siswa.kelas_id == kelasId);
    if (siswaList.length === 0) {
        return '<option value="">-- Tidak ada siswa di kelas ini --</option>';
    }

    let options = '<option value="">-- Pilih Siswa --</option>';
    siswaList.forEach(siswa => {
        options += `<option value="${siswa.id}">${siswa.nama}</option>`;
    });
    return options;
}

function getMapelOptions() {
    const guruId = document.getElementById('guru_id').value;
    if (!guruId) {
        return '<option value="">-- Pilih Guru terlebih dahulu --</option>';
    }

    const mapelIds = new Set();
    let options = '<option value="">-- Pilih Mata Pelajaran --</option>';
    jadwalsData.forEach(jadwal => {
        if (jadwal.guru_id == guruId && jadwal.mata_pelajaran && jadwal.mata_pelajaran.id && !mapelIds.has(jadwal.mata_pelajaran.id)) {
            mapelIds.add(jadwal.mata_pelajaran.id);
            options += `<option value="${jadwal.mata_pelajaran.id}">${jadwal.mata_pelajaran.nama}</option>`;
        }
    });

    if (mapelIds.size === 0) {
        return '<option value="">-- Guru belum mengajar mata pelajaran apa pun --</option>';
    }

    return options;
}

function getJadwalOptions() {
    const kelasId = document.getElementById('kelas_id').value;
    const guruId = document.getElementById('guru_id').value;
    if (!kelasId || !guruId) {
        return '<option value="">-- Pilih kelas dan guru terlebih dahulu --</option>';
    }

    const filtered = jadwalsData.filter(jadwal => jadwal.kelas_id == kelasId && jadwal.guru_id == guruId);
    if (filtered.length === 0) {
        return '<option value="">-- Tidak ada jadwal untuk kelas dan guru ini --</option>';
    }

    let options = '<option value="">-- Pilih Jadwal Pelajaran --</option>';
    filtered.forEach(jadwal => {
        const time = `${jadwal.hari} - ${jadwal.jamawal.substring(0,5)} - ${jadwal.jamakhir.substring(0,5)}`;
        options += `<option value="${jadwal.id}">${time}</option>`;
    });
    return options;
}

function updateStudentOptions() {
    const siswa = document.getElementById('siswa_id');
    const currentValue = siswa.value;
    siswa.innerHTML = getStudentOptions();
    if (currentValue) {
        siswa.value = currentValue;
    }
}

function updateMapelOptions() {
    const mapel = document.getElementById('mapel_id');
    const currentValue = mapel.value;
    mapel.innerHTML = getMapelOptions();
    if (currentValue) {
        mapel.value = currentValue;
    }
}

function updateJadwalOptions() {
    const jadwal = document.getElementById('jadwal_pelajaran_id');
    const currentValue = jadwal.value;
    jadwal.innerHTML = getJadwalOptions();
    if (currentValue) {
        jadwal.value = currentValue;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateStudentOptions();
    updateMapelOptions();
    updateJadwalOptions();

    document.getElementById('kelas_id').addEventListener('change', function() {
        updateStudentOptions();
        updateJadwalOptions();
    });
    document.getElementById('guru_id').addEventListener('change', function() {
        updateMapelOptions();
        updateJadwalOptions();
    });
});
</script>
@endpush
