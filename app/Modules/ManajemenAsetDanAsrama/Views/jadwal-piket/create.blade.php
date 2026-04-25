@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}">Jadwal Piket</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Form Tambah Jadwal Piket" icon="fas fa-calendar-plus">
                <form action="{{ route('manajemenasetdanasrama.jadwal-piket.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bulan">Bulan <span class="text-danger">*</span></label>
                                <select class="form-control @error('bulan') is-invalid @enderror" id="bulan" name="bulan" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    @php
                                        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    @endphp
                                    @foreach($namaBulan as $idx => $nama)
                                        <option value="{{ $idx + 1 }}" {{ old('bulan') == ($idx + 1) ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                                @error('bulan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pekan">Pekan <span class="text-danger">*</span></label>
                                <select class="form-control @error('pekan') is-invalid @enderror" id="pekan" name="pekan" required>
                                    <option value="">-- Pilih Pekan --</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('pekan') == $i ? 'selected' : '' }}>Pekan {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('pekan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hari">Hari <span class="text-danger">*</span></label>
                                <select class="form-control @error('hari') is-invalid @enderror" id="hari" name="hari" required>
                                    <option value="">-- Pilih Hari --</option>
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                        <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                    @endforeach
                                </select>
                                @error('hari')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tempat">Tempat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tempat') is-invalid @enderror" id="tempat" name="tempat" value="{{ old('tempat') }}" placeholder="Contoh: Masjid, Halaman, Kamar Mandi" required>
                                @error('tempat')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="siswa_id">Siswa <span class="text-danger">*</span></label>
                        <select class="form-control @error('siswa_id') is-invalid @enderror" id="siswa_id" name="siswa_id" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                        @error('siswa_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
