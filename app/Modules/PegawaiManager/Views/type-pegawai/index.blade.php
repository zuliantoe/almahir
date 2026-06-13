@extends('layouts.app')

@section('title', $title)

@push('styles')
<style>
    .glass-panel-card {
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.8);
        overflow: hidden;
    }
    
    .table-premium th {
        background: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 1.2rem 1rem;
        font-weight: 700;
    }
    
    .table-premium td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
        color: #334155;
    }
    
    .table-premium tbody tr:hover td {
        background-color: #f8fafc;
    }

    .badge-soft-primary { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; border-radius: 20px; padding: 6px 14px; }

    .btn-action {
        width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 12px !important; transition: all 0.3s ease; border: none; margin: 0 3px;
        text-decoration: none !important;
    }
    .btn-action-edit { background: #e0f2fe; color: #0284c7; }
    .btn-action-edit:hover { background: #0284c7; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3); }
    .btn-action-delete { background: #fee2e2; color: #b91c1c; }
    .btn-action-delete:hover { background: #b91c1c; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(185, 28, 28, 0.3); }

    .btn-gradient-primary { background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white; border: none; transition: all 0.3s ease; padding: 8px 20px;}
    .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4); color: white; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="card glass-panel-card mb-4">
        <div class="card-header bg-white p-4 border-0 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark"><i class="fas fa-tags text-primary mr-2"></i> {{ $title }}</h5>
                <p class="text-muted small mb-0 mt-1">Kelola kategori atau tipe status untuk pegawai.</p>
            </div>
            <div class="mt-3 mt-sm-0">
                <a href="{{ route('pegawaimanager.index') }}" class="btn btn-light rounded-pill shadow-sm font-weight-bold mr-2" style="border: 1px solid #e2e8f0;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('pegawaimanager.types.create') }}" class="btn btn-gradient-primary rounded-pill shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Tipe
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;" class="text-center px-4">No</th>
                            <th class="px-4">Kategori / Nama Tipe</th>
                            <th class="text-center px-4">Jumlah Pegawai</th>
                            <th class="text-center px-4">Tanggal Dibuat</th>
                            <th class="text-center px-4" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $index => $item)
                        <tr>
                            <td class="text-center text-muted font-weight-bold px-4">{{ $types->firstItem() + $index }}</td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm"
                                         style="width: 45px; height: 45px; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                        <i class="fas fa-tag text-primary" style="font-size: 1.1rem;"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $item->nama_type }}</div>
                                        <small class="text-muted"><i class="fas fa-users text-success mr-1"></i>{{ $item->pegawai_count }} Pegawai terdaftar</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center px-4">
                                <span class="badge badge-soft-primary px-3 py-2">
                                    <i class="fas fa-users mr-1"></i>{{ $item->pegawai_count }}
                                </span>
                            </td>
                            <td class="text-center px-4 text-muted font-weight-bold">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="text-center px-4">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('pegawaimanager.types.edit', $item->id) }}" class="btn-action btn-action-edit" title="Edit Tipe">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pegawaimanager.types.destroy', $item->id) }}" method="POST" class="d-inline" id="form-delete-{{ $item->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action btn-action-delete" title="Hapus Tipe" onclick="confirmDelete('{{ $item->id }}', '{{ addslashes($item->nama_type) }}', {{ $item->pegawai_count }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5 bg-light rounded" style="border: 2px dashed #cbd5e1; margin: 0 20px;">
                                    <div class="mb-3">
                                        <i class="fas fa-tags fa-4x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Belum Ada Tipe Pegawai</h5>
                                    <p class="text-muted mb-4">Sistem membutuhkan setidaknya satu tipe pegawai (misal: Guru, Staf TU).</p>
                                    <a href="{{ route('pegawaimanager.types.create') }}" class="btn btn-gradient-primary rounded-pill px-4">
                                        <i class="fas fa-plus mr-2"></i> Tambah Tipe Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($types->hasPages())
        <div class="card-footer bg-white p-4 border-top">
            {{ $types->links() }}
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, nama, count) {
    let warningHtml = `Anda yakin ingin menghapus tipe pegawai <b>${nama}</b> secara permanen?`;
    
    if (count > 0) {
        warningHtml += `<br><br><div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" style="background: #fffbeb; color: #92400e;"><i class="fas fa-exclamation-triangle fa-2x mr-3"></i> Terdapat <b>${count} pegawai</b> yang sedang menggunakan tipe ini. Menghapus tipe ini akan membuat data pegawai terkait menjadi tidak valid atau error.</div>`;
    }

    Swal.fire({
        title: 'Hapus Tipe Pegawai?',
        html: warningHtml,
        icon: count > 0 ? 'warning' : 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger rounded-pill px-4 mx-2 shadow-sm',
            cancelButton: 'btn btn-light rounded-pill px-4 mx-2 shadow-sm border'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-delete-' + id).submit();
        }
    });
}
</script>
@endpush
