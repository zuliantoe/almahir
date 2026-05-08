@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- ===== DETAIL CARD ===== --}}
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-lg" style="border-radius:15px;overflow:hidden;">

                {{-- Header with status --}}
                @php
                    $headerBg = match($perizinan->status) {
                        'disetujui' => 'linear-gradient(135deg,#28a745,#20c997)',
                        'ditolak'   => 'linear-gradient(135deg,#dc3545,#e83e8c)',
                        default     => 'linear-gradient(135deg,#ffc107,#fd7e14)',
                    };
                    $statusLabel = match($perizinan->status) {
                        'disetujui' => 'DISETUJUI',
                        'ditolak'   => 'DITOLAK',
                        default     => 'MENUNGGU KONFIRMASI',
                    };
                    $statusIcon = match($perizinan->status) {
                        'disetujui' => 'fa-check-circle',
                        'ditolak'   => 'fa-times-circle',
                        default     => 'fa-clock',
                    };
                    $durasi = \Carbon\Carbon::parse($perizinan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($perizinan->tanggal_selesai)) + 1;
                    $jenisIcon = match($perizinan->jenis_izin) {
                        'cuti'       => 'fa-umbrella-beach',
                        'sakit'      => 'fa-briefcase-medical',
                        'izin'       => 'fa-hand-paper',
                        'dinas luar' => 'fa-briefcase',
                        default      => 'fa-file-alt'
                    };
                @endphp

                <div class="p-4 text-white" style="background:{{ $headerBg }};">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-60 mb-1 font-weight-bold" style="letter-spacing:.5px;">DETAIL PENGAJUAN IZIN</div>
                            <h4 class="font-weight-bolder mb-1">
                                <i class="fas {{ $jenisIcon }} mr-2"></i>{{ strtoupper($perizinan->jenis_izin) }}
                            </h4>
                            <div class="text-white-70">{{ $perizinan->pegawai->nama ?? 'N/A' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="badge px-3 py-2 shadow"
                                 style="background:rgba(255,255,255,.25);font-size:.85rem;border-radius:20px;">
                                <i class="fas {{ $statusIcon }} mr-1"></i> {{ $statusLabel }}
                            </div>
                            <div class="small text-white-60 mt-2">
                                Diajukan: {{ $perizinan->created_at->translatedFormat('d F Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 bg-light">
                    {{-- Info boxes --}}
                    <div class="row mb-4">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="glass-card p-3 text-center border-0 h-100">
                                <i class="fas fa-calendar-alt fa-2x text-primary mb-2 d-block"></i>
                                <div class="small text-muted">Tanggal Mulai</div>
                                <div class="font-weight-bold text-dark">{{ $perizinan->tanggal_mulai->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="glass-card p-3 text-center border-0 h-100">
                                <i class="fas fa-calendar-check fa-2x text-success mb-2 d-block"></i>
                                <div class="small text-muted">Tanggal Selesai</div>
                                <div class="font-weight-bold text-dark">{{ $perizinan->tanggal_selesai->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="glass-card p-3 text-center border-0 h-100">
                                <i class="fas fa-calendar-week fa-2x text-warning mb-2 d-block"></i>
                                <div class="small text-muted">Total Durasi</div>
                                <div class="font-weight-bold text-dark">{{ $durasi }} Hari</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="glass-card p-3 text-center border-0 h-100">
                                <i class="fas fa-user fa-2x text-info mb-2 d-block"></i>
                                <div class="small text-muted">Pengaju</div>
                                <div class="font-weight-bold text-dark" style="font-size:.85rem;">{{ $perizinan->pegawai->nama ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Alasan --}}
                    <div class="glass-card p-4 mb-3 border-0">
                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-comment-alt text-warning mr-2"></i>Alasan / Keterangan</h6>
                        <p class="mb-0 text-muted" style="line-height:1.7;">{{ $perizinan->alasan }}</p>
                    </div>

                    {{-- Catatan admin --}}
                    @if($perizinan->keterangan_admin)
                    <div class="glass-card p-4 mb-3 border-0" style="border-left:4px solid #17a2b8 !important;">
                        <h6 class="font-weight-bold text-info mb-2"><i class="fas fa-comment-dots mr-2"></i>Catatan Admin</h6>
                        <p class="mb-0 text-muted fst-italic">{{ $perizinan->keterangan_admin }}</p>
                    </div>
                    @endif

                    {{-- Lampiran --}}
                    @if($perizinan->bukti)
                    <div class="glass-card p-4 mb-3 border-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-paperclip text-secondary mr-2"></i>Lampiran Bukti</h6>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" 
                                    onclick="openImagePreview('{{ Storage::url($perizinan->bukti) }}')">
                                <i class="fas fa-expand mr-1"></i> Perbesar Gambar
                            </button>
                        </div>
                        <div class="position-relative group" style="cursor: pointer;" onclick="openImagePreview('{{ Storage::url($perizinan->bukti) }}')">
                            <img src="{{ Storage::url($perizinan->bukti) }}" 
                                 class="img-fluid rounded shadow-sm border w-100" 
                                 style="max-height:500px; object-fit: contain; background: #f8f9fa;">
                            <div class="overlay-hover d-flex align-items-center justify-content-center">
                                <div class="bg-white rounded-circle p-3 shadow-lg text-primary">
                                    <i class="fas fa-search-plus fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                        .position-relative.group { overflow: hidden; border-radius: 12px; }
                        .overlay-hover {
                            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                            background: rgba(67, 97, 238, 0.2);
                            opacity: 0; transition: all 0.3s ease;
                        }
                        .position-relative.group:hover .overlay-hover { opacity: 1; }
                        .position-relative.group img { transition: transform 0.5s ease; }
                        .position-relative.group:hover img { transform: scale(1.02); }
                    </style>

                    {{-- Timeline status --}}
                    <div class="glass-card p-4 border-0">
                        <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-stream text-primary mr-2"></i>Riwayat Status</h6>
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:32px;height:32px;">
                                <i class="fas fa-paper-plane text-white" style="font-size:.75rem;"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold text-dark" style="font-size:.9rem;">Pengajuan Dikirim</div>
                                <small class="text-muted">{{ $perizinan->created_at->translatedFormat('d F Y, H:i') }}</small>
                            </div>
                        </div>
                        @if($perizinan->status !== 'menunggu')
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                                 style="width:32px;height:32px;background:{{ $perizinan->status == 'disetujui' ? '#28a745' : '#dc3545' }};">
                                <i class="fas {{ $statusIcon }} text-white" style="font-size:.75rem;"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold text-dark" style="font-size:.9rem;">
                                    {{ $perizinan->status == 'disetujui' ? 'Pengajuan Disetujui' : 'Pengajuan Ditolak' }}
                                </div>
                                <small class="text-muted">{{ $perizinan->updated_at->translatedFormat('d F Y, H:i') }}</small>
                            </div>
                        </div>
                        @else
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:32px;height:32px;">
                                <i class="fas fa-hourglass-half text-white" style="font-size:.75rem;"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold text-warning" style="font-size:.9rem;">Menunggu Keputusan Admin</div>
                                <small class="text-muted">Pengajuan Anda sedang dalam antrian peninjauan</small>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('perizinan.index') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-animate">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== APPROVAL PANEL (Admin only) ===== --}}
        @if($perizinan->status == 'menunggu' && (Auth::user()->hasRole('SUPER_ADMIN') || Auth::user()->hasRole('STAF_TU')))
        <div class="col-md-4">
            <div class="glass-card p-4 border-0 mb-4 shadow-sm" style="border-top:5px solid #007bff !important;position:sticky;top:80px;">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mr-3"
                         style="width:44px;height:44px;">
                        <i class="fas fa-gavel text-white"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold text-primary mb-0">Panel Keputusan</h6>
                        <small class="text-muted">Tinjau dan berikan keputusan</small>
                    </div>
                </div>

                {{-- Ringkasan untuk admin --}}
                <div class="p-3 rounded mb-3" style="background:#f4f6f9;">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Pemohon</small>
                        <small class="font-weight-bold text-dark">{{ $perizinan->pegawai->nama ?? 'N/A' }}</small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Jenis</small>
                        <small class="font-weight-bold text-dark">{{ strtoupper($perizinan->jenis_izin) }}</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Durasi</small>
                        <small class="font-weight-bold text-dark">{{ $durasi }} hari</small>
                    </div>
                </div>

                <form id="form-approval" action="{{ route('perizinan.update-status', $perizinan->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" id="status-input" value="">

                    <div class="form-group">
                        <label class="font-weight-bold small text-muted">Catatan <small>(Opsional)</small></label>
                        <textarea name="keterangan_admin" class="form-control border-0 shadow-sm" rows="3"
                                  placeholder="Misal: alasan penolakan atau instruksi tindak lanjut..."></textarea>
                    </div>

                    <div class="d-flex flex-column mt-3" style="gap:8px;">
                        <button type="button" class="btn btn-success btn-block shadow rounded-pill btn-animate font-weight-bold py-2"
                                onclick="confirmApproval('disetujui')">
                            <i class="fas fa-check-circle mr-2"></i>SETUJUI PENGAJUAN
                        </button>
                        <button type="button" class="btn btn-danger btn-block shadow rounded-pill btn-animate font-weight-bold py-2"
                                onclick="confirmApproval('ditolak')">
                            <i class="fas fa-times-circle mr-2"></i>TOLAK PENGAJUAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
function confirmApproval(status) {
    const isApprove = status === 'disetujui';
    Swal.fire({
        title: isApprove ? 'Setujui Pengajuan?' : 'Tolak Pengajuan?',
        text: isApprove
            ? 'Pengajuan izin ini akan disetujui dan pemohon akan diberitahu.'
            : 'Pengajuan izin ini akan ditolak. Pastikan Anda sudah mengisi catatan jika perlu.',
        icon: isApprove ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: isApprove ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: isApprove ? '<i class="fas fa-check mr-1"></i> Ya, Setujui!' : '<i class="fas fa-times mr-1"></i> Ya, Tolak!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('status-input').value = status;
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            document.getElementById('form-approval').submit();
        }
    });
}
</script>
@endpush
@endsection
