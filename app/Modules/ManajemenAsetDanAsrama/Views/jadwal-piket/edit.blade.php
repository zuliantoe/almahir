@extends('layouts.app')

@section('title', $title)

@php
    $isSiswa = auth()->user()->hasRole('SISWA');
    $canCheckOffPiket = !$isSiswa;
@endphp

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
                            <div class="col-sm-6 col-md-4 mb-2">
                                <span class="text-muted small d-block">SANTRI TERTUGAS</span>
                                <strong class="text-dark" style="font-size: 1.1rem;">{{ $jadwal->siswa->nama ?? '-' }}</strong>
                            </div>
                            <div class="col-sm-6 col-md-4 mb-2">
                                <span class="text-muted small d-block">KAMAR ASAL</span>
                                <strong class="text-dark">{{ $jadwal->kamar->nama_kamar ?? '-' }}</strong>
                            </div>
                            <div class="col-sm-6 col-md-4 mb-2">
                                <span class="text-muted small d-block">STATUS PIKET</span>
                                @if($jadwal->status === 'sudah' || $jadwal->status === 'selesai')
                                    <span class="badge badge-success px-3 py-1 font-weight-bold shadow-sm" style="font-size: 0.95rem; border-radius: 6px;">
                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge badge-warning px-3 py-1 font-weight-bold shadow-sm" style="font-size: 0.95rem; border-radius: 6px;">
                                        <i class="fas fa-clock mr-1"></i> Belum Selesai
                                    </span>
                                @endif
                            </div>
                            <div class="col-sm-6 col-md-6 mb-2 mt-2 mt-md-0">
                                <span class="text-muted small d-block">WAKTU PIKET</span>
                                <strong class="text-dark">
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }} 
                                    <span class="badge badge-secondary ml-1 uppercase">{{ $jadwal->shift }}</span>
                                </strong>
                            </div>
                            <div class="col-sm-6 col-md-6 mb-2 mt-2 mt-md-0">
                                <span class="text-muted small d-block">LOKASI TUGAS SAAT INI</span>
                                <span class="badge badge-primary px-3 py-1 font-weight-bold shadow-sm" style="font-size: 0.95rem; border-radius: 6px;">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $jadwal->lokasi_piket }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if(($jadwal->status === 'sudah' || $jadwal->status === 'selesai') && $canCheckOffPiket)
                        <div class="alert alert-warning border-0 p-3 mb-4 shadow-sm d-flex flex-column flex-sm-row align-items-sm-center justify-content-between" style="border-radius: 12px; gap: 15px; background-color: #fffbeb; border-left: 5px solid #f59e0b !important;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x mr-3"></i>
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Santri ini sudah dikonfirmasi piket</h6>
                                    <span class="text-muted small">Jika terjadi kesalahan konfirmasi, Anda dapat membatalkannya di sini.</span>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-danger font-weight-bold px-3 py-2 shadow-sm" style="border-radius: 8px;" onclick="if(confirm('Batalkan status selesai piket santri ini?')) { event.preventDefault(); document.getElementById('form-batal-selesai').submit(); }">
                                    <i class="fas fa-undo mr-1"></i> Batalkan Selesai
                                </button>
                            </div>
                        </div>
                    @endif

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
            @if(($jadwal->status === 'sudah' || $jadwal->status === 'selesai') && $canCheckOffPiket)
                <form id="form-batal-selesai" action="{{ route('manajemenasetdanasrama.jadwal-piket.batal-selesai', $jadwal->id) }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endif
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
