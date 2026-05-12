@extends('layouts.app')

@section('title', 'Detail Pengeluaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengeluaran</h1>
        <a href="{{ route('keuangan.pengeluarans.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2 d-none d-sm-inline-block"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4 rounded-xl overflow-hidden">
                <div class="card-header py-3 bg-white border-bottom border-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-info-circle mr-2"></i> Informasi Pengeluaran</h6>
                    <span class="badge badge-danger bg-danger text-white">ID: #{{ str_pad($pengeluaran->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Tujuan Pengeluaran -->
                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-2">Tujuan Pengeluaran</label>
                            <div class="p-3 bg-light rounded-lg border-0 shadow-sm font-weight-bold text-dark">
                                {{ $pengeluaran->tujuan->nama }}
                            </div>
                        </div>

                        <!-- Nominal -->
                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-2">Nominal</label>
                            <div class="p-3 bg-light rounded-lg border-0 shadow-sm font-weight-bold text-danger">
                                Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Tanggal -->
                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-2">Tanggal Transaksi</label>
                            <div class="p-3 bg-light rounded-lg border-0 shadow-sm text-dark">
                                {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                            </div>
                        </div>
                        
                        <!-- Waktu Dicatat -->
                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-2">Waktu Dicatat</label>
                            <div class="p-3 bg-light rounded-lg border-0 shadow-sm text-dark">
                                {{ $pengeluaran->updated_at ? $pengeluaran->updated_at->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('H.i') . ' WIB, ' . $pengeluaran->updated_at->locale('id')->translatedFormat('d F Y') : '-' }}
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12 mb-4">
                            <label class="small font-weight-bold text-muted mb-2">Deskripsi</label>
                            <div class="p-3 bg-light rounded-lg border-0 shadow-sm text-dark" style="min-height: 80px; white-space: pre-line;">
                                {{ $pengeluaran->deskripsi ?: '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-2 pt-3 border-top">
                        <form action="{{ route('keuangan.pengeluarans.destroy', $pengeluaran->id) }}" method="POST" class="delete-form mr-2">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger px-4 shadow-sm delete-btn" data-source="{{ $pengeluaran->tujuan->nama }}">
                                <i class="fas fa-trash mr-2"></i> Hapus
                            </button>
                        </form>
                        <a href="{{ route('keuangan.pengeluarans.edit', $pengeluaran->id) }}" class="btn btn-info px-4 shadow-sm font-weight-bold text-white">
                            <i class="fas fa-edit mr-2"></i> Edit Pengeluaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .rounded-xl { border-radius: 12px; }
    .rounded-lg { border-radius: 10px; }
    .bg-light { background-color: #f8f9fc !important; }
</style>
@endpush

@push('scripts')
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('.delete-form');
                const source = this.getAttribute('data-source');
                
                Swal.fire({
                    title: `Hapus pengeluaran ini?`,
                    text: `Anda akan menghapus pengeluaran dari "${source}". Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endpush
