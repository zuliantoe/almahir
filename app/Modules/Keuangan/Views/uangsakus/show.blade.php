@extends('layouts.app')

@section('title', 'Detail Uang Saku')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Uang Saku</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('keuangan.uangsakus.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm mr-2"></i> Kembali
            </a>
            <a href="{{ route('keuangan.uangsakus.edit', $uangsaku->id) }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4 rounded-xl overflow-hidden">
                <div class="card-header py-3 bg-white border-bottom border-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Informasi Lengkap</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Profil Santri -->
                        <div class="col-12 mb-4">
                            <div class="d-flex align-items-center bg-light p-3 rounded-lg border">
                                <div class="icon-circle bg-primary text-white mr-3 shadow-sm" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 1.5rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="small text-muted font-weight-bold uppercase">Santri</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $uangsaku->siswa->nama ?? 'Unknown' }}</div>
                                    <div class="small text-muted">NIS: {{ $uangsaku->siswa->nis ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-1 text-uppercase">Nominal</label>
                            <div class="h4 font-weight-bold text-primary">
                                Rp{{ number_format($uangsaku->jumlah, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-1 text-uppercase">Tanggal Transaksi</label>
                            <div class="h5 font-weight-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($uangsaku->tanggal)->translatedFormat('d F Y') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-1 text-uppercase">Status</label>
                            <div>
                                <span class="badge {{ $uangsaku->status == 'Sudah Diterima Santri' ? 'badge-info' : 'badge-light border' }} px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="fas {{ $uangsaku->status == 'Sudah Diterima Santri' ? 'fa-check-double' : 'fa-clock' }} mr-1"></i>
                                    {{ $uangsaku->status }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="small font-weight-bold text-muted mb-1 text-uppercase">Dibuat Pada</label>
                            <div class="text-gray-800">
                                {{ $uangsaku->created_at->translatedFormat('d F Y, H:i') }} WIB
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="small font-weight-bold text-muted mb-1 text-uppercase">Deskripsi / Keterangan</label>
                            <div class="bg-light p-3 rounded-lg border min-height-100">
                                {{ $uangsaku->deskripsi ?: 'Tidak ada keterangan tambahan.' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <form action="{{ route('keuangan.uangsakus.destroy', $uangsaku->id) }}" method="POST" class="delete-form d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger btn-block delete-btn" data-source="{{ $uangsaku->siswa->nama }}">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Data Uang Saku Ini
                        </button>
                    </form>
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
    .uppercase { text-transform: uppercase; }
    .min-height-100 { min-height: 100px; }
    .icon-circle { flex-shrink: 0; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-form');
                const source = this.getAttribute('data-source');
                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: "Menghapus transaksi dari " + source + " tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endpush
