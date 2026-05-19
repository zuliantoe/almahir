@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('pegawaimanager.calon-pegawai.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm btn-animate">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-header bg-white p-4 border-bottom">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-user-edit text-primary mr-2"></i> Edit Data Pelamar</h5>
        </div>
        
        <div class="card-body p-4">
            <form action="{{ route('pegawaimanager.calon-pegawai.update', $calon->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $calon->nama) }}" required>
                        @error('nama') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Posisi Dilamar <span class="text-danger">*</span></label>
                        <select name="type_pegawai_id" class="form-control @error('type_pegawai_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Posisi --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_pegawai_id', $calon->type_pegawai_id) == $type->id ? 'selected' : '' }}>{{ $type->nama_type }}</option>
                            @endforeach
                        </select>
                        @error('type_pegawai_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $calon->email) }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $calon->no_hp) }}">
                        @error('no_hp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $calon->tempat_lahir) }}">
                        @error('tempat_lahir') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $calon->tanggal_lahir ? $calon->tanggal_lahir->format('Y-m-d') : '') }}">
                        @error('tanggal_lahir') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin', $calon->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('jenis_kelamin', $calon->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>


                    <div class="col-md-12 mb-3">
                        <label class="form-label font-weight-bold">Alamat</label>
                        <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $calon->alamat) }}</textarea>
                        @error('alamat') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Tanggal Melamar <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_melamar" class="form-control @error('tanggal_melamar') is-invalid @enderror" value="{{ old('tanggal_melamar', $calon->tanggal_melamar ? $calon->tanggal_melamar->format('Y-m-d') : '') }}" required>
                        @error('tanggal_melamar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Status Seleksi <span class="text-danger">*</span></label>
                        <select name="status_seleksi" class="form-control @error('status_seleksi') is-invalid @enderror" required>
                            <option value="baru" {{ old('status_seleksi', $calon->status_seleksi) == 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="wawancara" {{ old('status_seleksi', $calon->status_seleksi) == 'wawancara' ? 'selected' : '' }}>Tahap Wawancara</option>
                            <option value="diterima" {{ old('status_seleksi', $calon->status_seleksi) == 'diterima' ? 'selected' : '' }}>Diterima (Akan masuk ke tabel Pegawai)</option>
                            <option value="ditolak" {{ old('status_seleksi', $calon->status_seleksi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        @error('status_seleksi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
