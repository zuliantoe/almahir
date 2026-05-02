@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 75vh;">
    <div class="col-md-6 text-center">
        <div class="glass-card p-5 border-0 shadow-lg hover-elevate" style="border-top: 8px solid #dc3545 !important; border-radius: 20px;">
            
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-light text-danger rounded-circle shadow-sm" style="width: 120px; height: 120px; background: rgba(220, 53, 69, 0.1);">
                    <i class="fas fa-lock fa-4x mb-1"></i>
                </div>
            </div>

            <h1 class="font-weight-bolder text-dark mb-3" style="font-size: 3rem; letter-spacing: -1px;">403 <span class="text-danger">Forbidden</span></h1>
            <h4 class="font-weight-bold text-muted mb-4">Oops! Anda Tidak Memiliki Akses Ke Halaman Ini.</h4>
            
            <p class="text-secondary mb-5" style="font-size: 1.1rem; line-height: 1.6;">
                Maaf, wewenang akun Anda tidak mengizinkan untuk membuka halaman tersebut.<br>
                Halaman ini khusus dilindungi untuk Administrator atau peran tertentu. Jika Anda merasa ini adalah sebuah kesalahan sistem, silakan hubungi <b>Tim IT / TU</b>.
            </p>

            <div class="d-flex justify-content-center">
                <a href="{{ url('/') }}" class="btn btn-danger rounded-pill px-5 py-3 shadow-sm btn-animate font-weight-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>
</div>

<style>
    /* Animasi sederhana untuk ikon gembok */
    .fa-lock {
        animation: shake 2.5s infinite;
    }
    @keyframes shake {
        0% { transform: rotate(0deg); }
        10% { transform: rotate(-10deg); }
        20% { transform: rotate(10deg); }
        30% { transform: rotate(0deg); }
        100% { transform: rotate(0deg); }
    }
</style>
@endsection
