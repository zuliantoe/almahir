@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Presensi" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.presensi.store') }}" method="POST">
            @csrf

            {{-- Scan Kartu Section --}}
            <div class="alert alert-info mb-4">
                <i class="fas fa-barcode mr-2"></i>
                <strong>Scan Kartu Siswa</strong> - Arahkan kartu ke scanner untuk mengisi data siswa secara otomatis
            </div>

            <div class="form-group">
                <label for="scan_id">
                    <i class="fas fa-barcode mr-1"></i> Scan ID / NIS
                </label>
                <input type="text" 
                       name="scan_id" 
                       id="scan_id" 
                       class="form-control form-control-lg" 
                       placeholder="Arahkan kartu atau masukkan NIS..."
                       autofocus>
                <small class="form-text text-muted">Fokus pada field ini dan scan kartu siswa</small>
            </div>

            {{-- Siswa Section --}}
            <div class="form-group">
                <label for="id_siswa">Siswa</label>
                <select name="id_siswa" id="id_siswa" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                    @endforeach
                </select>
                <div id="siswa_info" class="alert alert-success mt-2" style="display: none;">
                    <small id="siswa_nama"></small>
                </div>
            </div>

            {{-- Guru Section --}}
            <div class="form-group">
                <label for="id_guru">Guru</label>
                <select name="id_guru" id="id_guru" class="form-control" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mata Pelajaran Section --}}
            <div class="form-group">
                <label for="id_mapel">Mata Pelajaran</label>
                <select name="id_mapel" id="id_mapel" class="form-control" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama ?? $mapel->name ?? 'Mapel '.$mapel->id }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Jadwal Pelajaran Section --}}
            <div class="form-group">
                <label for="id_jadwal_pelajaran">Jadwal Pelajaran</label>
                <select name="id_jadwal_pelajaran" id="id_jadwal_pelajaran" class="form-control" required>
                    <option value="">-- Pilih Jadwal Pelajaran --</option>
                    @foreach($jadwals as $jadwal)
                        <option value="{{ $jadwal->id }}">{{ $jadwal->hari }} - {{ \Carbon\Carbon::parse($jadwal->jamawal)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jamakhir)->format('H:i') }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Jam Section --}}
            <div class="form-group">
                <label for="jam">Jam</label>
                <input type="time" name="jam" id="jam" class="form-control" value="{{ date('H:i') }}" required>
            </div>

            {{-- Status Section --}}
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Hadir">✓ Hadir</option>
                    <option value="Izin">⊘ Izin</option>
                    <option value="Sakit">✕ Sakit</option>
                    <option value="Alpha">✗ Alpha</option>
                </select>
            </div>

            {{-- Kategori Section --}}
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Sekolah">Sekolah</option>
                    <option value="Pengajian">Pengajian</option>
                    <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                </select>
            </div>

            {{-- Submit Section --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save mr-1"></i> Simpan Presensi
                </button>
                <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('scan_id').addEventListener('change', function() {
    const scanId = this.value.trim();
    
    if (!scanId) return;
    
    // Disable input while processing
    this.disabled = true;
    
    fetch('{{ route("penilaiandanpresensi.presensi.scan-card") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ scan_id: scanId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Set siswa value
            document.getElementById('id_siswa').value = data.data.id_siswa;
            
            // Show success message
            document.getElementById('siswa_info').style.display = 'block';
            document.getElementById('siswa_nama').textContent = '✓ Siswa ditemukan: ' + data.data.nama_siswa;
            
            // Focus to next field
            document.getElementById('id_guru').focus();
        } else {
            alert('❌ ' + data.message);
            this.value = '';
            this.focus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat membaca kartu');
        this.value = '';
        this.focus();
    })
    .finally(() => {
        this.disabled = false;
    });
});
</script>
@endpush
