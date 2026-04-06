@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    
    {{-- Header / Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        {{-- Kolom Kiri: Ringkasan Profil --}}
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center mb-3">
                        <img class="profile-user-img img-fluid img-circle elevation-2"
                             src="{{ $pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($pegawai->nama).'&size=128' }}"
                             alt="User profile picture"
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #adb5bd;">
                    </div>

                    <h3 class="profile-username text-center font-weight-bold mb-1">{{ $pegawai->nama }}</h3>
                    <p class="text-muted text-center mb-2">
                        <span class="badge badge-success px-2 py-1">
                            <i class="fas fa-shield-alt mr-1"></i> {{ $pegawai->user->primary_role ?? 'Pegawai' }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Tipe Pegawai</b> <a class="float-right text-primary font-weight-bold">{{ $pegawai->typePegawai->nama_type ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Terdaftar Sejak</b> <a class="float-right">{{ $pegawai->created_at->format('d/m/Y') }}</a>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pegawaimanager.edit', $pegawai->id) }}" class="btn btn-info btn-block mr-1">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Informasi --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#info" data-toggle="tab"><i class="fas fa-info-circle mr-1"></i> Informasi Lengkap</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="info">
                            <div class="row">
                                <div class="col-sm-6 mb-4">
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Kontak Email</h6>
                                    <p class="mb-0 h6"><i class="fas fa-envelope text-primary mr-2"></i> {{ $pegawai->email ?? '-' }}</p>
                                </div>
                                <div class="col-sm-6 mb-4">
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Nomor Telepon/HP</h6>
                                    <p class="mb-0 h6"><i class="fas fa-phone-alt text-success mr-2"></i> {{ $pegawai->no_hp ?? '-' }}</p>
                                </div>
                                <div class="col-sm-12 mb-4">
                                    <hr>
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Alamat Domisili</h6>
                                    <p class="mb-0 h6 text-wrap" style="line-height: 1.6;">
                                        <i class="fas fa-map-marker-alt text-danger mr-2"></i> {{ $pegawai->alamat ?? 'Alamat belum diatur.' }}
                                    </p>
                                </div>
                                <div class="col-sm-6 mb-4">
                                    <hr>
                                    <h6 class="text-muted small text-uppercase font-weight-bold mb-2">Tanggal Mulai Tugas (TMT)</h6>
                                    <p class="mb-0 h6"><i class="fas fa-calendar-check text-info mr-2"></i> {{ $pegawai->tanggal_masuk ? $pegawai->tanggal_masuk->format('d F Y') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
