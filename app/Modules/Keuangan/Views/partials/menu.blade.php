{{-- 
|--------------------------------------------------------------------------
| KEUANGAN (Admin Only)
|--------------------------------------------------------------------------
| Salin potongan kode ini ke file sidebar utama Anda (resources/views/layouts/partials/sidebar.blade.php)
--}}
@if(Auth::check())
<li class="nav-header">KEUANGAN</li>

{{-- Dashboard --}}
<li class="nav-item">
    <a href="{{ route('keuangan.index') }}" class="nav-link {{ request()->is('keuangan') || request()->is('keuangan/index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

{{-- Transaksi --}}
<li class="nav-item">
    <a href="{{ route('keuangan.transaksis.index') }}" class="nav-link {{ request()->is('keuangan/transaksis*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-exchange-alt"></i>
        <p>Transaksi</p>
    </a>
</li>

{{-- Pemasukan --}}
<li class="nav-item">
    <a href="{{ route('keuangan.pemasukans.index') }}" class="nav-link {{ request()->is('keuangan/pemasukans*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-arrow-down"></i>
        <p>Pemasukan</p>
    </a>
</li>

{{-- Pengeluaran --}}
<li class="nav-item">
    <a href="{{ route('keuangan.pengeluarans.index') }}" class="nav-link {{ request()->is('keuangan/pengeluarans*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-arrow-up"></i>
        <p>Pengeluaran</p>
    </a>
</li>

{{-- Uang Saku --}}
<li class="nav-item">
    <a href="{{ route('keuangan.uangsakus.index') }}" class="nav-link {{ request()->is('keuangan/uangsakus*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-coins"></i>
        <p>Uang Saku</p>
    </a>
</li>

{{-- Tagihan Santri --}}
<li class="nav-item">
    <a href="{{ route('keuangan.tagihansantris.index') }}" class="nav-link {{ request()->is('keuangan/tagihansantris*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice"></i>
        <p>Tagihan Santri</p>
    </a>
</li>

{{-- Pembayaran Santri --}}
<li class="nav-item">
    <a href="{{ route('keuangan.pembayaransantris.index') }}" class="nav-link {{ request()->is('keuangan/pembayaransantris*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-credit-card"></i>
        <p>Pembayaran Santri</p>
    </a>
</li>

{{-- Sumber Pemasukan --}}
<li class="nav-item">
    <a href="{{ route('keuangan.sumbers.index') }}" class="nav-link {{ request()->is('keuangan/sumbers*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-funnel-dollar"></i>
        <p>Sumber Pemasukan</p>
    </a>
</li>

{{-- Tujuan Pengeluaran --}}
<li class="nav-item">
    <a href="{{ route('keuangan.tujuans.index') }}" class="nav-link {{ request()->is('keuangan/tujuans*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-bullseye"></i>
        <p>Tujuan Pengeluaran</p>
    </a>
</li>
@endif
