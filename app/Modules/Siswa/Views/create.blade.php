@extends('layouts.app')

@section('title', $title ?? 'Tambah Siswa')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title ?? 'Tambah Siswa Baru' }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('siswa.index') }}">Siswa</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            {{-- Left Column: Personal Info --}}
            <div class="col-md-8">
                <x-card title="Informasi Pribadi" type="primary">
                    <div class="row">
                        <div class="col-md-6">
                            <x-input 
                                label="NIS (Nomor Induk Siswa)" 
                                name="nis" 
                                placeholder="Masukkan NIS"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-input 
                                label="Nama Lengkap" 
                                name="nama" 
                                placeholder="Masukkan nama lengkap"
                                required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input 
                                label="Email" 
                                name="email" 
                                type="email"
                                placeholder="siswa@email.com"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-input 
                                label="Tanggal Lahir" 
                                name="tanggal_lahir" 
                                type="date"
                                required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input 
                                label="Tempat Lahir" 
                                name="tempat_lahir" 
                                placeholder="Kota kelahiran" />
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <x-input 
                        label="No. Telepon" 
                        name="telepon" 
                        placeholder="08xxxxxxxxxx" />

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                  rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </x-card>
            </div>

            {{-- Right Column: Academic Info --}}
            <div class="col-md-4">
                <x-card title="Informasi Akademik" type="info">
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror">
                            <option value="">-- Pilih Kelas --</option>
                            {{-- Options would be populated dynamically --}}
                        </select>
                        @error('kelas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-input 
                        label="Tahun Masuk" 
                        name="tahun_masuk" 
                        type="number"
                        placeholder="{{ date('Y') }}"
                        value="{{ date('Y') }}" />

                    <div class="form-group">
                        <label>Foto Siswa</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="foto" id="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih file...</label>
                        </div>
                        <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                    </div>
                </x-card>

                {{-- Action Buttons --}}
                <x-card>
                    <div class="d-grid gap-2">
                        <x-btn type="submit" class="btn-primary btn-block" icon="fas fa-save">
                            Simpan Data
                        </x-btn>
                        <x-btn class="btn-secondary btn-block" icon="fas fa-arrow-left" href="{{ route('siswa.index') }}">
                            Kembali
                        </x-btn>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Custom file input label update
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Pilih file...';
        this.nextElementSibling.innerText = fileName;
    });
</script>
@endpush
