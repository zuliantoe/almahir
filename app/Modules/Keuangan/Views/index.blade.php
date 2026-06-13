@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@section('content')
<div class="container-fluid">
    {{-- 🌟 Personalized Welcome Card (Sistem Akademik Standard) --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid #1cc88a; background: #fff;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto d-none d-md-block">
                            <img src="{{ Auth::user()->avatar_url ?? asset('images/default-avatar.png') }}" 
                                 class="img-circle elevation-1 border" 
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random'"
                                 style="width: 75px; height: 75px; object-fit: cover; background: #f4f6f9; padding: 3px;">
                        </div>
                        <div class="col">
                            <h4 class="font-weight-bold text-dark mb-1">
                                Selamat Datang di Modul Keuangan, {{ Auth::user()->name }}!
                            </h4>
                            <p class="text-muted mb-0">
                                Menampilkan ringkasan keuangan untuk bulan <strong>{{ $monthName }} {{ now()->year }}</strong>.
                                <span class="d-none d-lg-inline ml-1 border-left pl-2">Sistem Akademik <strong>AL MAHIR</strong>.</span>
                            </p>
                        </div>
                        <div class="col-auto text-right text-muted d-none d-sm-block">
                            <div class="small font-weight-bold">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
                            <div class="small" id="dashboard-clock">00:00:00 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 Dynamic Statistics Row --}}
    <div class="row">
        {{-- Total Saldo --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white custom-icon-box" 
                      style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <i class="fas fa-wallet fa-fw"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Saldo per {{ $monthName }}</span>
                    <span class="info-box-number font-weight-bolder mb-0 {{ $saldo < 0 ? 'text-danger' : 'text-dark' }}" style="font-size: 1.15rem;">
                        Rp {{ number_format($saldo, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        
        {{-- Total Pemasukan --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white custom-icon-box"
                      style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
                    <i class="fas fa-arrow-down fa-fw"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Pemasukan ({{ $monthName }})</span>
                    <span class="info-box-number font-weight-bolder mb-0 text-dark" style="font-size: 1.15rem;">
                        Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white custom-icon-box"
                      style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);">
                    <i class="fas fa-arrow-up fa-fw"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Pengeluaran ({{ $monthName }})</span>
                    <span class="info-box-number font-weight-bolder mb-0 text-dark" style="font-size: 1.15rem;">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Uang Saku (Total / Transaksi) --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 shadow-sm border-0 bg-white" style="border-radius: 10px;">
                <span class="info-box-icon elevation-1 text-white custom-icon-box"
                      style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
                    <i class="fas fa-coins fa-fw"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase text-muted small font-weight-bold">Saldo Uang Saku</span>
                    <span class="info-box-number font-weight-bolder mb-0 text-dark" style="font-size: 1.15rem;">
                        Rp {{ number_format($saldoUangSaku, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        {{-- 📘 Overview Section --}}
        <div class="col-lg-8">
            <x-card title="Panduan Modul Keuangan" icon="fas fa-book-open" type="success" :outline="true">
                <div class="text-center py-4">
                    <h5 class="text-success font-weight-bold mb-3 small-text">SISTEM KEUANGAN TERINTEGRASI</h5>
                    <p class="text-muted px-lg-5" style="line-height: 1.8; font-size: 0.95rem;">
                        Kelola seluruh arus kas instansi melalui modul ini. Transaksi pemasukan dan pengeluaran 
                        akan otomatis memengaruhi total saldo. Sistem juga mendukung pencatatan uang saku santri secara terpusat.
                    </p>
                </div>
                <hr class="my-4">
                <div class="row text-center mb-3">
                    <div class="col-md-4">
                        <div class="p-2">
                            <i class="fas fa-exchange-alt text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <h6 class="font-weight-bold text-dark small">Pencatatan Otomatis</h6>
                            <p class="text-xs text-muted mb-0">Uang saku santri otomatis tersinkronisasi ke buku kas.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border-left border-right">
                            <i class="fas fa-shield-alt text-success mb-2" style="font-size: 1.5rem;"></i>
                            <h6 class="font-weight-bold text-dark small">Integritas Data</h6>
                            <p class="text-xs text-muted mb-0">Proteksi edit untuk transaksi otomatis menjaga akurasi.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2">
                            <i class="fas fa-file-pdf text-danger mb-2" style="font-size: 1.5rem;"></i>
                            <h6 class="font-weight-bold text-dark small">Laporan Instan</h6>
                            <p class="text-xs text-muted mb-0">Cetak riwayat transaksi bulanan & tahunan dengan sekali klik.</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- 🚀 Quick Actions Section --}}
        <div class="col-lg-4">
            <x-card title="Aksi Pintar" icon="fas fa-rocket" type="success" :outline="true" class="shadow-sm">
                <div class="list-group list-group-flush mt-2">
                    
                    <a href="{{ route('keuangan.transaksis.index') }}" class="list-group-item list-group-item-action border-0 py-3 mb-2 bg-light-soft hover-translate shadow-sm" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-white p-2 rounded-circle shadow-sm" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Laporan Transaksi</span>
                                <small class="text-muted">Lihat rekapitulasi arus kas</small>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('keuangan.pemasukans.index') }}" class="list-group-item list-group-item-action border-0 py-3 mb-2 bg-light-soft hover-translate shadow-sm" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-white p-2 rounded-circle shadow-sm" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Kelola Pemasukan</span>
                                <small class="text-muted">Catat dana masuk instansi</small>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('keuangan.pengeluarans.index') }}" class="list-group-item list-group-item-action border-0 py-3 mb-2 bg-light-soft hover-translate shadow-sm" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-white p-2 rounded-circle shadow-sm" style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Kelola Pengeluaran</span>
                                <small class="text-muted">Catat penggunaan dana kas</small>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('keuangan.uangsakus.index') }}" class="list-group-item list-group-item-action border-0 py-3 mb-2 bg-light-soft hover-translate shadow-sm" style="border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-white p-2 rounded-circle shadow-sm" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Uang Saku Santri</span>
                                <small class="text-muted">Deposit & distribusi uang saku</small>
                            </div>
                        </div>
                    </a>

                </div>
            </x-card>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
    
    .bg-light-soft { background-color: #f8f9fc; transition: all 0.2s ease; }
    .hover-translate:hover { transform: translateY(-3px); background-color: #fff; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; z-index: 10; }
    .text-xs { font-size: 0.75rem; }
    .small-text { letter-spacing: 1px; }

    /* Custom Icon Box Standardizer */
    .custom-icon-box {
        width: 65px !important;
        height: 65px !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-right: 15px;
    }
    .custom-icon-box i {
        font-size: 1.8rem !important;
    }
    .info-box {
        align-items: center !important;
        padding: 10px 15px !important;
    }
</style>

@push('scripts')
<script>
    function updateDashboardClock() {
        const now = new Date();
        const display = now.toLocaleTimeString('id-ID', { hour12: false });
        const clockElement = document.getElementById('dashboard-clock');
        if(clockElement) clockElement.textContent = display + ' WIB';
    }
    setInterval(updateDashboardClock, 1000);
    updateDashboardClock();
</script>
@endpush
@endsection
