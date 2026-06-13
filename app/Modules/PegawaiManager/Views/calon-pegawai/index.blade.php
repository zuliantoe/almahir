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

    .badge-soft-success { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; border-radius: 20px; padding: 6px 14px; }
    .badge-soft-danger { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 700; border-radius: 20px; padding: 6px 14px; }
    .badge-soft-warning { background-color: #fef9c3; color: #a16207; border: 1px solid #fde047; font-weight: 700; border-radius: 20px; padding: 6px 14px; }
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
    .btn-action-accept { background: #dcfce7; color: #15803d; width: auto; padding: 0 15px; font-weight: bold;}
    .btn-action-accept:hover { background: #15803d; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(21, 128, 61, 0.3); }

    .btn-gradient-primary { background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white; border: none; transition: all 0.3s ease; padding: 8px 20px;}
    .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4); color: white; }
    
    /* Modal Styling */
    .modal-content { border-radius: 20px; border: none; box-shadow: 0 25px 50px rgba(0,0,0,0.1); overflow: hidden; }
    .modal-header { background: linear-gradient(135deg, #06d6a0, #2dc653); color: white; border-bottom: none; padding: 1.5rem; }
    .modal-title { font-weight: 800; font-family: 'Outfit', sans-serif; letter-spacing: 0.5px; }
    .btn-generate { background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white; border: none; border-radius: 0 10px 10px 0 !important; font-weight: bold;}
    .form-control-generate { border-radius: 10px 0 0 10px !important; border: 2px solid #e2e8f0; border-right: none; font-family: monospace; font-size: 1.2rem; font-weight: bold; color: #1e293b; background: #f8fafc;}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="card glass-panel-card mb-4">
        <div class="card-header bg-white p-4 border-0 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark"><i class="fas fa-users-slash text-primary mr-2"></i> {{ $title }}</h5>
                <p class="text-muted small mb-0 mt-1">Kelola data pelamar dan konversi menjadi pegawai aktif dengan pembuatan akun otomatis.</p>
            </div>
            <div class="mt-3 mt-sm-0">
                <a href="{{ route('pegawaimanager.calon-pegawai.create') }}" class="btn btn-gradient-primary rounded-pill shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Pelamar Baru
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 text-center" style="width: 60px;">No</th>
                            <th class="px-4">Nama & Informasi Kontak</th>
                            <th class="px-4">Posisi Dilamar</th>
                            <th class="px-4">Tanggal Daftar</th>
                            <th class="px-4 text-center">Status</th>
                            <th class="px-4 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calonPegawai as $index => $calon)
                        <tr>
                            <td class="px-4 text-center text-muted font-weight-bold">{{ $calonPegawai->firstItem() + $index }}</td>
                            <td class="px-4">
                                <div class="font-weight-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $calon->nama }}</div>
                                <div class="small text-muted mb-1"><i class="fas fa-envelope text-primary mr-1 opacity-75"></i> {{ $calon->email }}</div>
                                <div class="small text-muted"><i class="fas fa-phone-alt text-success mr-1 opacity-75"></i> {{ $calon->no_hp ?? '-' }}</div>
                            </td>
                            <td class="px-4">
                                <span class="badge" style="background: #e2e8f0; color: #475569; padding: 6px 12px; border-radius: 8px; font-weight: 600;">
                                    <i class="fas fa-tag mr-1 opacity-75"></i> {{ $calon->typePegawai->nama_type ?? 'Belum ditentukan' }}
                                </span>
                            </td>
                            <td class="px-4 text-muted font-weight-bold">{{ $calon->tanggal_melamar->format('d M Y') }}</td>
                            <td class="px-4 text-center">
                                @if($calon->status_seleksi === 'baru')
                                    <span class="badge badge-soft-primary">Baru</span>
                                @elseif($calon->status_seleksi === 'wawancara')
                                    <span class="badge badge-soft-warning">Wawancara</span>
                                @elseif($calon->status_seleksi === 'diterima')
                                    <span class="badge badge-soft-success">Diterima</span>
                                @else
                                    <span class="badge badge-soft-danger">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 text-center">
                                <div class="d-flex justify-content-center">
                                    @if($calon->status_seleksi !== 'diterima')
                                    <button type="button" class="btn-action btn-action-accept" title="Terima Pegawai dan Buat Akun" onclick="openModalTerima('{{ $calon->id }}', '{{ addslashes($calon->nama) }}', '{{ addslashes($calon->typePegawai->nama_type ?? 'Belum ditentukan') }}')">
                                        <i class="fas fa-user-check mr-2"></i> Terima
                                    </button>
                                    @endif
                                    
                                    <a href="{{ route('pegawaimanager.calon-pegawai.edit', $calon->id) }}" class="btn-action btn-action-edit" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('pegawaimanager.calon-pegawai.destroy', $calon->id) }}" method="POST" class="d-inline" id="form-delete-{{ $calon->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action btn-action-delete" title="Hapus Data" onclick="confirmDelete('{{ $calon->id }}', '{{ addslashes($calon->nama) }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-5 bg-light rounded" style="border: 2px dashed #cbd5e1; margin: 0 20px;">
                                    <div class="mb-3">
                                        <i class="fas fa-folder-open fa-4x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Belum Ada Pelamar</h5>
                                    <p class="text-muted mb-0">Sistem belum memiliki data calon pegawai atau pelamar yang terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($calonPegawai->hasPages())
        <div class="card-footer bg-white p-4 border-top">
            {{ $calonPegawai->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Terima Pegawai -->
<div class="modal fade" id="modalTerimaPegawai" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 550px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-check mr-2"></i> Proses Penerimaan Pegawai</h5>
                <button type="button" class="close text-white opacity-75" data-dismiss="modal" aria-label="Close" style="text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTerimaModal" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="action_type" value="status_update">
                <input type="hidden" name="status_seleksi" value="diterima">
                
                <div class="modal-body p-4 bg-light">
                    <!-- Info Pelamar -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-size: 1.2rem; background: #e2e8f0; color: #475569; border: 2px solid #cbd5e1;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 font-weight-bold text-dark" style="font-size: 1.1rem;" id="modalNamaPelamar">Nama Pelamar</h6>
                                    <div class="badge" style="background: #f1f5f9; color: #64748b; padding: 4px 8px; font-size: 0.75rem;" id="modalPosisiPelamar">Posisi</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert mb-4 border-0 shadow-sm d-flex align-items-center" style="border-radius: 12px; background: #fffbeb; border-left: 5px solid #f59e0b !important; color: #92400e;">
                        <i class="fas fa-info-circle fa-2x mr-3 text-warning"></i>
                        <div>Sistem akan membuatkan akun pengguna otomatis. Silakan tentukan Hak Akses Role dan Password akun.</div>
                    </div>

                    <!-- Pengaturan Akses -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark small text-uppercase mb-2"><i class="fas fa-shield-alt text-primary mr-1"></i> Role Sistem (Hak Akses)</label>
                                <select name="role_name" class="form-control custom-select" style="border-radius: 10px; border: 2px solid #e2e8f0; height: 45px;" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ strtolower($role->name) == 'pegawai' ? 'selected' : '' }}>{{ strtoupper($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark small text-uppercase mb-2"><i class="fas fa-key text-warning mr-1"></i> Default Password Akun</label>
                                <div class="input-group shadow-sm" style="border-radius: 10px;">
                                    <input type="text" name="password" id="modalPassword" class="form-control form-control-generate" placeholder="Password..." required>
                                    <div class="input-group-append">
                                        <button class="btn btn-generate px-4" type="button" onclick="generatePassword()" title="Generate Password Acak">
                                            <i class="fas fa-random mr-1"></i> Acak
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-3 p-2 rounded" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                    <small class="form-text text-danger font-weight-bold m-0 text-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Wajib: Catat/Salin sandi ini sebelum menekan tombol simpan!
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white d-flex justify-content-between" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm font-weight-bold" data-dismiss="modal" style="border: 1px solid #e2e8f0;">Batal</button>
                    <button type="submit" class="btn btn-gradient-success rounded-pill px-4 shadow-sm"><i class="fas fa-check-circle mr-2"></i> Konfirmasi & Terima Pegawai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generatePassword() {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#*";
    let pass = "";
    for (let i = 0; i < 8; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('modalPassword').value = pass;
    
    // Highlight the input temporarily
    const input = document.getElementById('modalPassword');
    input.style.backgroundColor = '#dcfce7';
    setTimeout(() => {
        input.style.backgroundColor = '#f8fafc';
    }, 500);
}

function openModalTerima(id, nama, posisi) {
    document.getElementById('modalNamaPelamar').innerText = nama;
    document.getElementById('modalPosisiPelamar').innerText = posisi;
    
    // Set form action
    let updateUrl = "{{ route('pegawaimanager.calon-pegawai.update', ':id') }}";
    updateUrl = updateUrl.replace(':id', id);
    document.getElementById('formTerimaModal').action = updateUrl;
    
    // Generate new password
    generatePassword();
    
    // Show modal
    $('#modalTerimaPegawai').modal('show');
}

function confirmDelete(id, nama) {
    Swal.fire({
        title: 'Hapus Pelamar?',
        html: `Anda yakin ingin menghapus lamaran <b>${nama}</b> secara permanen? Data ini tidak dapat dipulihkan.`,
        icon: 'error',
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
