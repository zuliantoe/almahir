@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Edit Izin/Sakit" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.izinsakit.update', $izinSakit->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="siswa_id">Siswa <span class="text-danger">*</span></label>
                <select name="siswa_id" id="siswa_id" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('siswa_id', $izinSakit->siswa_id) == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                <select name="kelas_id" id="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id', $izinSakit->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jenis">Jenis <span class="text-danger">*</span></label>
                <select name="jenis" id="jenis" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Izin" {{ old('jenis', $izinSakit->jenis)=='Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ old('jenis', $izinSakit->jenis)=='Sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
            </div>

            <div id="dynamic-fields" style="display: none;">
                <div class="form-group">
                    <label for="keterangan">Keterangan / Alasan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $izinSakit->keterangan) }}</textarea>
                </div>
                
                <div class="form-group">
                    <label for="bukti_foto">Bukti (Surat Dokter / Foto)</label>
                    @if($izinSakit->bukti_foto)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $izinSakit->bukti_foto) }}" target="_blank" class="btn btn-sm btn-info">Lihat Bukti Saat Ini</a>
                        </div>
                    @endif
                    <input type="file" name="bukti_foto" id="bukti_foto" class="form-control-file" accept="image/*">
                    <small class="text-muted">Maksimal ukuran file: 2MB. Biarkan kosong jika tidak ingin mengubah foto.</small>
                </div>
            </div>

            <div class="form-group">
                <label for="tgl_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control" value="{{ old('tgl_mulai', $izinSakit->tgl_mulai?->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="tgl_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control" value="{{ old('tgl_selesai', $izinSakit->tgl_selesai?->format('Y-m-d')) }}" required>
            </div>



            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jenisSelect = document.getElementById('jenis');
        const dynamicFields = document.getElementById('dynamic-fields');

        function toggleFields() {
            if (jenisSelect.value === 'Izin' || jenisSelect.value === 'Sakit') {
                dynamicFields.style.display = 'block';
            } else {
                dynamicFields.style.display = 'none';
            }
        }

        jenisSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial call to handle validation errors logic and current state
    });
</script>
@endpush
