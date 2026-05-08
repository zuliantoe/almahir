@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard Keuangan</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Keuangan</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Info boxes --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pemasukan</span>
                    <span class="info-box-number text-success">Rp 0</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pengeluaran</span>
                    <span class="info-box-number text-danger">Rp 0</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wallet"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Saldo Saat Ini</span>
                    <span class="info-box-number text-info">Rp 0</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Recent Activity --}}
        <div class="col-md-8">
            <x-card title="Aktivitas Terakhir" icon="fas fa-history" type="primary">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="text-muted">
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Tipe</th>
                                <th class="text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Belum ada aktivitas terbaru.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <x-slot name="footer">
                    <a href="{{ route('keuangan.transaksis.index') }}" class="btn btn-sm btn-link text-primary p-0">
                        Lihat Semua Transaksi <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </x-slot>
            </x-card>
        </div>

        {{-- Quick Actions --}}
        <div class="col-md-4">
            <x-card title="Aksi Keuangan" type="secondary" :outline="true">
                <div class="list-group list-group-flush">
                    <a href="{{ route('keuangan.pemasukans.index') }}" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-plus-circle mr-2 text-success"></i>
                        Input Pemasukan Baru
                    </a>
                    <a href="{{ route('keuangan.pengeluarans.index') }}" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-minus-circle mr-2 text-danger"></i>
                        Input Pengeluaran Baru
                    </a>
                    <a href="#" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-coins mr-2 text-warning"></i>
                        Manajemen Uang Saku
                    </a>
                    <a href="#" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-file-invoice mr-2 text-primary"></i>
                        Buat Tagihan Santri
                    </a>
                </div>
            </x-card>

            <div class="card bg-gradient-primary shadow">
                <div class="card-body">
                    <h5><i class="fas fa-info-circle mr-2"></i> Laporan Cepat</h5>
                    <p class="small">Export laporan keuangan periode ini ke format PDF secara instan.</p>
                    <a href="{{ route('keuangan.transaksis.index') }}" class="btn btn-light btn-sm btn-block font-weight-bold">
                        Buka Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
