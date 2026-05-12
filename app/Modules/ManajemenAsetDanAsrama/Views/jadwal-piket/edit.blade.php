@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.jadwal-piket.index') }}">Jadwal Piket</a></li>
            <li class="breadcrumb-item active">Switch</li>
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
            <x-card title="Tukar Putaran Lokasi Piket" icon="fas fa-exchange-alt">
                <form action="{{ route('manajemenasetdanasrama.jadwal-piket.update', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- INFORMASI PIKET ASAL (TERKUNCI) --}}
                    <div class="bg-light p-4 rounded mb-4 border-left border-info" style="border-width: 4px !important;">
                        <h6 class="font-weight-bold text-info uppercase mb-3"><i class="fas fa-info-circle mr-1"></i> Data Penugasan Terkini</h6>
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <span class="text-muted small d-block">SANTRI TERTUGAS</span>
                                <strong class="text-dark" style="font-size: 1.1rem;">{{ $jadwal->siswa->nama ?? '-' }}</strong>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <span class="text-muted small d-block">KAMAR ASAL</span>
                                <strong class="text-dark">{{ $jadwal->kamar->nama_kamar ?? '-' }}</strong>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <span class="text-muted small d-block">WAKTU PIKET</span>
                                <strong class="text-dark">
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }} 
                                    <span class="badge badge-secondary ml-1 uppercase">{{ $jadwal->shift }}</span>
                                </strong>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <span class="text-muted small d-block">LOKASI TUGAS SAAT INI</span>
                                <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.95rem;">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $jadwal->lokasi_piket }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- PILIHAN TARGET SWITCH --}}
                    <div class="form-group">
                        <label for="target_jadwal_id" class="font-weight-bold text-dark">
                            Pilih Target Pertukaran Lokasi Piket <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">
                            Daftar di bawah adalah penugasan lokasi piket lain pada <strong>hari dan shift yang sama</strong>. Sistem akan menyilang tempat penugasan mereka secara instan.
                        </p>
                        
                        <select class="form-control select2 @error('target_jadwal_id') is-invalid @enderror" id="target_jadwal_id" name="target_jadwal_id" required>
                            <option value="">-- Pilih Lokasi Piket & Santri Target --</option>
                            @forelse($candidates as $cand)
                                <option value="{{ $cand->id }}" {{ old('target_jadwal_id') == $cand->id ? 'selected' : '' }}>
                                    Lokasi: {{ $cand->lokasi_piket }} &nbsp;|&nbsp; Ditugaskan ke: {{ $cand->siswa->nama ?? '-' }} ({{ $cand->kamar->nama_kamar ?? '-' }})
                                </option>
                            @empty
                                <option value="" disabled>-- Tidak ada penugasan tempat lain di shift ini untuk disilang --</option>
                            @endforelse
                        </select>
                        @error('target_jadwal_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-info font-weight-bold px-4 shadow-sm" {{ $candidates->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-random mr-1"></i> Tukar Tempat Piket
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.jadwal-piket.index', ['tanggal_mulai' => \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d')]) }}" class="btn btn-secondary ml-1 shadow-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih Lokasi Piket & Santri Target --",
                allowClear: true
            });
        }
    });
</script>
@endpush
