@extends('layouts.app')

@section('title', $title)

@section('content')
<style>
    .glass-panel-card {
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .gradient-header {
        background: linear-gradient(135deg, #4f46e5, #06b6d4);
        color: white;
    }
    .holiday-row {
        transition: all 0.2s ease;
    }
    .holiday-row:hover {
        background-color: rgba(79, 70, 229, 0.02) !important;
        transform: scale(1.002);
    }
    .date-badge {
        background: rgba(79, 70, 229, 0.08);
        color: #4f46e5;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(79, 70, 229, 0.15);
    }
    .desc-text {
        color: #4b5563;
        font-size: 0.95rem;
        font-weight: 500;
    }
    .btn-delete-holiday {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
        border: 1px solid #fee2e2;
        color: #ef4444;
        background: #fef2f2;
    }
    .btn-delete-holiday:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    .empty-state-icon {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(6, 118, 212, 0.1));
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: #4f46e5;
    }
</style>

<div class="container-fluid py-3">
    <div class="card border-0 glass-panel-card">
        <div class="card-header border-0 p-4 d-flex flex-wrap justify-content-between align-items-center gradient-header">
            <h4 class="card-title text-white font-weight-bold mb-0 py-1">
                <i class="fas fa-calendar-times mr-2"></i> Pengaturan Hari Libur
            </h4>
            <div class="card-tools py-1">
                <button class="btn btn-light text-primary btn-sm rounded-pill px-4 shadow-sm btn-animate font-weight-bold py-2" data-toggle="modal" data-target="#addLiburModal">
                    <i class="fas fa-plus mr-1"></i> Tambah Hari Libur
                </button>
            </div>
        </div>

        <div class="card-body p-4 bg-light-gradient">
            <div class="table-responsive bg-white rounded shadow-sm border-0" style="border-radius: 15px !important; overflow: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th width="70" class="text-center border-0 text-muted">No</th>
                            <th width="240" class="border-0 text-muted">Tanggal Libur</th>
                            <th class="border-0 text-muted">Keterangan / Acara</th>
                            <th width="120" class="text-center border-0 text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($liburs as $index => $item)
                        <tr class="holiday-row">
                            <td class="text-center align-middle text-muted">{{ $liburs->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <div class="date-badge">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="desc-text">{{ $item->keterangan }}</div>
                            </td>
                            <td class="text-center align-middle">
                                <form action="{{ route('absensi.hari-libur.destroy', $item->id) }}" method="POST" class="d-inline form-delete-holiday">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-delete-holiday shadow-sm" onclick="confirmDeleteHoliday(this)" title="Hapus Hari Libur">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted bg-white">
                                <div class="py-4">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-calendar-check fa-2x"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark">Tidak Ada Hari Libur Terdaftar</h5>
                                    <p class="small text-muted mb-0">Semua hari kerja dihitung aktif. Klik "Tambah Hari Libur" di atas untuk menambah hari libur nasional atau khusus.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($liburs->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $liburs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Hari Libur -->
<div class="modal fade" id="addLiburModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('absensi.hari-libur.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header bg-primary text-white border-0 py-3 d-flex align-items-center">
                    <h5 class="modal-title font-weight-bold mb-0"><i class="fas fa-calendar-plus mr-2"></i> Tambah Hari Libur</h5>
                    <button type="button" class="close text-white opacity-75 ml-auto" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: white;">
                        <div class="form-group mb-3 text-left">
                            <label class="font-weight-bold text-muted small text-uppercase mb-2"><i class="far fa-calendar text-primary mr-1"></i> Tanggal Libur <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" style="border-radius: 10px; font-weight: 500;" required>
                        </div>
                        <div class="form-group mb-0 text-left">
                            <label class="font-weight-bold text-muted small text-uppercase mb-2"><i class="fas fa-sticky-note text-warning mr-1"></i> Keterangan / Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-control" rows="3" style="border-radius: 10px;" placeholder="Contoh: Libur Nasional Idul Fitri" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm btn-animate"><i class="fas fa-save mr-1"></i> Simpan Hari Libur</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#4f46e5',
        });
    @endif

    function confirmDeleteHoliday(button) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Hari libur yang dihapus akan mengembalikan perhitungan absen hari tersebut menjadi hari aktif normal.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }
</script>
@endpush
