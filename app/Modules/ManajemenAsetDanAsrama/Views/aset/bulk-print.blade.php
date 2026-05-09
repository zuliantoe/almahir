@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark font-weight-bold">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.aset.index') }}">Master Aset</a></li>
            <li class="breadcrumb-item active">Cetak Masal</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Input Kode Aset" icon="fas fa-print" class="card-outline card-primary shadow-lg">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <div class="d-flex">
                        <div class="mr-3">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Petunjuk Pengisian:</h6>
                            <p class="small mb-0">
                                Masukkan beberapa **Kode Aset** sekaligus. Anda bisa memisahkannya dengan **Baris Baru (Enter)**, **Koma (,)**, atau **Spasi**. 
                                <br>Contoh: <code>MEJ-2026-0001, KUR-2026-0005, LAP-2026-0010</code>
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('manajemenasetdanasrama.aset.bulk-print-action') }}" method="POST" target="_blank">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="text-uppercase small font-weight-bold text-muted">Daftar Kode Aset <span class="text-danger">*</span></label>
                        <textarea name="codes" class="form-control form-control-lg border-primary shadow-sm" 
                                  rows="10" placeholder="Masukkan kode aset di sini..." 
                                  style="border-radius: 15px; font-family: 'Courier New', Courier, monospace; line-height: 1.6;" 
                                  required></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-link text-muted">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow" style="border-radius: 12px;">
                            <i class="fas fa-print mr-2"></i> Generasi Label Sekarang
                        </button>
                    </div>
                </form>
            </x-card>

            <div class="text-center mt-4 text-muted small animate__animated animate__fadeInUp">
                <p><i class="fas fa-magic mr-1"></i> Sistem akan secara otomatis mencari aset yang sesuai dan menyiapkan file cetak dalam tab baru.</p>
            </div>
        </div>
    </div>
</div>
@endsection
