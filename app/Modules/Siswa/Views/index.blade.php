@extends('layouts.app')

@section('title', $title ?? 'Data Siswa')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary font-weight-bold"><i class="fas fa-users mr-2"></i> {{ $title ?? 'Data Siswa' }}</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <a href="{{ route('siswa.create') }}" class="btn btn-primary shadow-sm px-4" style="border-radius: 50px;">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Santri Baru
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 border-0">NIS & Foto</th>
                            <th class="border-0">Nama Lengkap</th>
                            <th class="border-0">Kelas</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-center px-4">Kartu QR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                        <tr>
                            <td class="px-4 align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @if($siswa->foto)
                                            <img src="{{ asset('storage/' . $siswa->foto) }}" class="rounded-circle" width="45" height="45" style="object-fit: cover; border: 2px solid #0d6efd;">
                                        @else
                                            <div class="rounded-circle bg-primary-light d-flex align-items-center justify-content-center text-primary font-weight-bold" style="width: 45px; height: 45px; border: 2px solid #0d6efd;">
                                                {{ substr($siswa->nama, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $siswa->nis }}</div>
                                        <small class="text-muted">ID: {{ substr($siswa->id, 0, 8) }}...</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark">{{ $siswa->nama }}</div>
                                <small class="text-muted"><i class="far fa-envelope mr-1"></i> {{ $siswa->email }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-info px-3 py-1" style="border-radius: 6px;">{{ $siswa->kelas->nama_kelas ?? ($siswa->kelas->nama_rombel ?? '-') }}</span>
                            </td>
                            <td class="align-middle">
                                @php
                                    $statusClass = [
                                        'aktif' => 'success',
                                        'lulus' => 'primary',
                                        'keluar' => 'danger',
                                        'cuti' => 'warning'
                                    ][$siswa->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $statusClass }} px-3 py-1" style="border-radius: 6px; text-transform: capitalize;">{{ $siswa->status ?? 'Aktif' }}</span>
                            </td>
                            <td class="align-middle text-center px-4">
                                <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-sm" style="border-radius: 50px;" 
                                        onclick="showQRCode('{{ $siswa->nama }}', '{{ $siswa->nis }}')">
                                    <i class="fas fa-qrcode mr-1"></i> Lihat QR
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="mb-3 opacity-25">
                                    <i class="fas fa-user-friends fa-4x text-primary"></i>
                                </div>
                                <h5 class="text-muted font-weight-bold">Belum ada data santri.</h5>
                                <p class="text-muted small">Silakan tambah santri pertama Anda untuk memulai.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-4 text-center d-block position-relative">
                <h4 class="modal-title font-weight-bold mb-0">KARTU SANTRI DIGITAL</h4>
                <p class="mb-0 opacity-75 small">Sistem Informasi Akademik Almahir</p>
                <button type="button" class="close text-white position-absolute" data-dismiss="modal" aria-label="Close" style="top: 20px; right: 20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-5">
                <div class="mb-4 d-inline-block p-3 bg-white shadow-sm" style="border: 1px solid #eee; border-radius: 20px;">
                    <img id="qrImage" src="" alt="QR Code" style="width: 200px; height: 200px;">
                </div>
                <h3 id="qrNama" class="font-weight-bold text-dark mb-1">-</h3>
                <h5 id="qrNis" class="text-primary font-weight-bold mb-4">-</h5>
                <div class="alert bg-light border-0 text-muted small py-2 px-3" style="border-radius: 12px;">
                    <i class="fas fa-info-circle mr-1"></i> Gunakan QR Code ini untuk presensi harian santri.
                </div>
                <button type="button" class="btn btn-primary btn-block py-3 font-weight-bold mt-4 shadow" style="border-radius: 50px;" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i> CETAK KARTU QR
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: rgba(13, 110, 25, 0.05); }
    .table th { letter-spacing: 0.5px; text-transform: uppercase; font-size: 0.75rem; color: #8898aa; }
</style>

@push('scripts')
<script>
    function showQRCode(nama, nis) {
        // Menggunakan API goqr.me (Gratis & Stabil)
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${nis}&margin=10`;
        
        $('#qrImage').attr('src', qrUrl);
        $('#qrNama').text(nama);
        $('#qrNis').text('NIS: ' + nis);
        $('#qrModal').modal('show');
    }
</script>
@endpush
@endsection
