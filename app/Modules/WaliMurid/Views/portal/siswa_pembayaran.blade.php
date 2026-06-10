@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Back Button & Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walimurid.portal.siswa-detail', $siswa->id) }}" class="btn btn-light rounded-circle shadow-sm mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="font-weight-bold mb-0">Keuangan & Pembayaran: {{ $siswa->nama }}</h3>
            <p class="text-muted mb-0">Informasi tagihan aktif dan riwayat pembayaran siswa</p>
        </div>
    </div>

    <div class="row">
        {{-- Tagihan Section --}}
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white p-4 border-light d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0 text-dark">
                        <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i> Daftar Tagihan
                    </h5>
                    <span class="badge badge-pill badge-warning px-3 py-2">
                        {{ $tagihans->where('status', 'Belum Lunas')->count() }} Belum Lunas
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="p-4 border-0">Tagihan</th>
                                    <th class="p-4 border-0">Jumlah</th>
                                    <th class="p-4 border-0">Jatuh Tempo</th>
                                    <th class="p-4 border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tagihans as $tagihan)
                                    @php
                                        $statusClass = 'badge-success';
                                        if ($tagihan->status === 'Belum Lunas') {
                                            $statusClass = $tagihan->terlambat ? 'badge-danger' : 'badge-warning';
                                        }
                                        $displayStatus = $tagihan->status;
                                        if ($tagihan->status === 'Belum Lunas' && $tagihan->terlambat) {
                                            $displayStatus = 'Terlambat';
                                        }
                                    @endphp
                                    <tr class="border-top border-light">
                                        <td class="p-4">
                                            <h6 class="font-weight-bold text-dark mb-0">{{ $tagihan->judul }}</h6>
                                            <small class="text-muted">ID Tagihan: #{{ $tagihan->id }}</small>
                                        </td>
                                        <td class="p-4">
                                            <span class="font-weight-bold text-dark">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
                                            @if($tagihan->sisa_tagihan > 0 && $tagihan->sisa_tagihan < $tagihan->jumlah)
                                                <br><small class="text-danger">Sisa: Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</small>
                                            @endif
                                        </td>
                                        <td class="p-4 align-middle">
                                            <span class="text-muted small"><i class="far fa-calendar-alt mr-1"></i>{{ $tagihan->batas_waktu ? $tagihan->batas_waktu->format('d M Y') : '-' }}</span>
                                        </td>
                                        <td class="p-4 align-middle">
                                            <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill shadow-xs">{{ $displayStatus }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-5 text-center text-muted">
                                            <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-3"></i>
                                            <h6>Tidak Ada Tagihan Aktif</h6>
                                            <p class="small mb-0">Semua tagihan untuk putra/putri Anda telah lunas dibayarkan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pembayaran Section --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white p-4 border-light">
                    <h5 class="font-weight-bold mb-0 text-dark">
                        <i class="fas fa-history mr-2 text-success"></i> Riwayat Pembayaran
                    </h5>
                </div>
                <div class="card-body p-4">
                    @forelse($pembayarans as $pembayaran)
                        <div class="d-flex align-items-start pb-4 border-bottom border-light mb-4 {{ $loop->last ? 'border-0 pb-0 mb-0' : '' }}">
                            <div class="bg-success-light text-success p-3 rounded-circle mr-3" style="background: rgba(40, 167, 69, 0.1);">
                                <i class="fas fa-check fa-md"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="font-weight-bold text-dark mb-0">{{ $pembayaran->tipe_pembayaran }}</h6>
                                    <span class="badge badge-success px-2 py-1 rounded-pill small">Sukses</span>
                                </div>
                                <p class="text-muted small mb-2">Tagihan: {{ optional($pembayaran->tagihanSiswa)->judul ?? '-' }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-success">Rp {{ number_format($pembayaran->jumlah_dibayarkan, 0, ',', '.') }}</span>
                                    <span class="text-muted small"><i class="far fa-clock mr-1"></i>{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d F Y') : '-' }}</span>
                                </div>
                                @if($pembayaran->bukti_gambar)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $pembayaran->bukti_gambar) }}" target="_blank" class="btn btn-outline-secondary btn-xs rounded-pill">
                                            <i class="fas fa-image mr-1"></i> Lihat Bukti
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 opacity-3"></i>
                            <h6>Belum Ada Riwayat Pembayaran</h6>
                            <p class="small mb-0">Belum ditemukan transaksi pembayaran terbaru untuk siswa ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
