@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <x-card title="Form Pengajuan Izin/Sakit" icon="fas fa-edit">
                <form action="{{ route('perizinan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Perizinan <span class="text-danger">*</span></label>
                                <select name="jenis_izin" class="form-control @error('jenis_izin') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="izin" {{ old('jenis_izin') == 'izin' ? 'selected' : '' }}>Izin (Kepentingan Pribadi)</option>
                                    <option value="sakit" {{ old('jenis_izin') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="cuti" {{ old('jenis_izin') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                    <option value="dinas luar" {{ old('jenis_izin') == 'dinas luar' ? 'selected' : '' }}>Dinas Luar</option>
                                </select>
                                @error('jenis_izin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input label="Tanggal Mulai" name="tanggal_mulai" type="date" required value="{{ old('tanggal_mulai') }}" />
                        </div>
                        <div class="col-md-6">
                            <x-input label="Tanggal Selesai" name="tanggal_selesai" type="date" required value="{{ old('tanggal_selesai') }}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alasan / Keterangan <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="4" placeholder="Jelaskan alasan pengajuan Anda..." required>{{ old('alasan') }}</textarea>
                        @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label>Lampiran / Bukti <small class="text-muted">(Opsional, Max 2MB: JPG, PNG)</small></label>
                        <input type="file" name="bukti" class="form-control-file @error('bukti') is-invalid @enderror">
                        @error('bukti') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('perizinan.index') }}" class="btn btn-secondary">Batal</a>
                        <x-btn variant="primary" icon="fas fa-paper-plane" type="submit">Kirim Pengajuan</x-btn>
                    </div>
                </form>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card title="Informasi" icon="fas fa-info-circle" type="info" :outline="true">
                <ul class="pl-3 mb-0 text-sm">
                    <li class="mb-2">Pastikan data yang Anda masukkan sudah benar.</li>
                    <li class="mb-2">Untuk izin sakit lebih dari 1 hari, wajib melampirkan surat keterangan dokter.</li>
                    <li class="mb-2">Approval akan dilakukan oleh Bagian TU atau Admin Sistem.</li>
                    <li>Status pengajuan dapat dipantau di halaman riwayat.</li>
                </ul>
            </x-card>
        </div>
    </div>
</div>
@endsection
