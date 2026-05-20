@extends('layouts.app')

@section('title', $title)

@push('css')
@include('manajemenasetdanasrama::partials.styles-dashboard')
@endpush

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item active">Kerusakan Aset</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Quick Information --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">{{ number_format($stats['total'] ?? 0) }}</h3>
                    <p>Total Laporan Masuk</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">{{ number_format($stats['ringan'] ?? 0) }}</h3>
                    <p>Rusak Ringan</p>
                </div>
                <div class="icon"><i class="fas fa-info-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">{{ number_format($stats['sedang'] ?? 0) }}</h3>
                    <p>Rusak Sedang</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3 style="font-size: 1.8rem;">{{ number_format($stats['berat'] ?? 0) }}</h3>
                    <p>Rusak Berat</p>
                </div>
                <div class="icon"><i class="fas fa-radiation"></i></div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <a href="{{ route('manajemenasetdanasrama.aset.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Master Aset
            </a>
            <a href="{{ route('manajemenasetdanasrama.pemeliharaan.index') }}" class="btn btn-outline-primary shadow-sm">
                Lanjut ke Pemeliharaan <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row">
        <div class="col-md-12">
            <x-card title="Daftar Kerusakan Aset" icon="fas fa-exclamation-triangle">
                <x-slot name="tools">
                    <a href="{{ route('manajemenasetdanasrama.kerusakan.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Lapor Kerusakan
                    </a>
                </x-slot>

                @include('manajemenasetdanasrama::partials.table-kerusakan', [
                    'items' => $kerusakan,
                    'mode' => 'index'
                ])

                @if($kerusakan->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $kerusakan->links() }}
                </div>
                @endif

                <x-slot name="footer">
                    <small class="text-muted">Total data: {{ $kerusakan->total() }}</small>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

@include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Laporan Kerusakan'])
@endsection
