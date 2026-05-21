@extends('layouts.app')

@section('title', $title ?? 'Akses Ditolak')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center py-5">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                    <h3 class="font-weight-bold">Akses Terbatas</h3>
                    <p class="text-muted mb-4">{{ $message ?? 'Halaman ini hanya dapat diakses oleh akun Pegawai.' }}</p>
                    <a href="{{ url('/') }}" class="btn btn-primary px-4 rounded-pill">
                        <i class="fas fa-home mr-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
