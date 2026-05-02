@extends('layouts.app')

@section('title', $title ?? 'Detail & Edit Siswa')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title ?? 'Detail & Edit Siswa' }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('siswa.index') }}">Siswa</a></li>
                <li class="breadcrumb-item active">{{ $siswa->nama }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- Left Column: Personal Info --}}
            <div class="col-md-8">
                <x-card title="Informasi Pribadi" type="primary">
                    <div class="row">
                        <div class="col-md-6">
                            <x-input 
                                label="NIS (Nomor Induk Siswa)" 
                                name="nis" 
                                value="{{ old('nis', $siswa->nis) }}"
                                placeholder="Masukkan NIS"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-input 
                                label="Nama Lengkap" 
                                name="nama" 
                                value="{{ old('nama', $siswa->nama) }}"
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
                                value="{{ old('email', $siswa->email) }}"
                                placeholder="siswa@email.com"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-input 
                                label="Tanggal Lahir" 
                                name="tanggal_lahir" 
                                type="date"
                                value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}"
                                required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input 
                                label="Tempat Lahir" 
                                name="tempat_lahir" 
                                value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                                placeholder="Kota kelahiran" />
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input 
                                label="No. Telepon" 
                                name="telepon" 
                                value="{{ old('telepon', $siswa->telepon) }}"
                                placeholder="08xxxxxxxxxx" />
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status Siswa</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="aktif" {{ old('status', $siswa->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="lulus" {{ old('status', $siswa->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                    <option value="keluar" {{ old('status', $siswa->status) == 'keluar' ? 'selected' : '' }}>Keluar / Pindah</option>
                                    <option value="cuti" {{ old('status', $siswa->status) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                  rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', $siswa->alamat) }}</textarea>
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
                        <label>Tahun Masuk (Tahun Ajaran)</label>
                        <select name="tahun_masuk" class="form-control @error('tahun_masuk') is-invalid @enderror">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @if(isset($tahunAjaran))
                                @foreach($tahunAjaran as $ta)
                                    <option value="{{ $ta->id }}" {{ old('tahun_masuk', $siswa->tahun_masuk) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahunajaran }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('tahun_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Foto Siswa</label>
                        @if($siswa->foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="foto" id="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Ubah foto...</label>
                        </div>
                        <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                    </div>
                </x-card>

                {{-- Action Buttons --}}
                <x-card>
                    <div class="d-grid gap-2">
                        <x-btn type="submit" class="btn-success btn-block" icon="fas fa-save">
                            Simpan Perubahan
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
