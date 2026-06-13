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
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 700; border-radius: 20px; padding: 6px 14px; }
    .badge-soft-primary { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; border-radius: 20px; padding: 6px 14px; }

    .btn-action {
        width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 12px !important; transition: all 0.3s ease; border: none; margin: 0 3px;
        text-decoration: none !important;
    }
    .btn-action-view { background: #e0f2fe; color: #0284c7; }
    .btn-action-view:hover { background: #0284c7; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3); }
    .btn-action-print { background: #f1f5f9; color: #475569; }
    .btn-action-print:hover { background: #475569; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(71, 85, 105, 0.3); }
    .btn-action-edit { background: #fef08a; color: #a16207; }
    .btn-action-edit:hover { background: #a16207; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(161, 98, 7, 0.3); }
    .btn-action-reset { background: #e2e8f0; color: #334155; }
    .btn-action-reset:hover { background: #334155; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(51, 65, 85, 0.3); }
    .btn-action-status-off { background: #ffedd5; color: #c2410c; }
    .btn-action-status-off:hover { background: #c2410c; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(194, 65, 12, 0.3); }
    .btn-action-status-on { background: #dcfce7; color: #15803d; }
    .btn-action-status-on:hover { background: #15803d; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(21, 128, 61, 0.3); }
    .btn-action-delete { background: #fee2e2; color: #b91c1c; }
    .btn-action-delete:hover { background: #b91c1c; color: white; transform: translateY(-3px); box-shadow: 0 4px 10px rgba(185, 28, 28, 0.3); }

    .search-panel {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }
    
    .btn-gradient-primary { background: linear-gradient(135deg, #4361ee, #4cc9f0); color: white; border: none; transition: all 0.3s ease; padding: 8px 20px;}
    .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4); color: white; }
    .btn-gradient-success { background: linear-gradient(135deg, #06d6a0, #2dc653); color: white; border: none; transition: all 0.3s ease; padding: 8px 20px;}
    .btn-gradient-success:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(6, 214, 160, 0.4); color: white; }
    .btn-gradient-warning { background: linear-gradient(135deg, #ffd60a, #ff9f1c); color: #333; border: none; transition: all 0.3s ease; padding: 8px 20px;}
    .btn-gradient-warning:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(255, 159, 28, 0.4); color: #000; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Alerts handled globally via SweetAlert2 --}}

    @if(session('new_password'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 p-4 animate__animated animate__fadeIn" role="alert" style="border-radius: 16px; background: linear-gradient(135deg, #06d6a0, #2dc653); color: white;">
        <div class="d-flex align-items-center">
            <div class="mr-4" style="font-size: 2.5rem; background: rgba(255,255,255,0.2); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                <i class="fas fa-key animate__animated animate__pulse animate__infinite"></i>
            </div>
            <div>
                <h5 class="font-weight-bold mb-1">Password Baru Berhasil Dibuat</h5>
                <p class="mb-0 small">Password default akun pegawai telah dibuat. Silakan salin password ini sebelum halaman di-refresh/ditutup:</p>
                <div class="mt-3 d-flex align-items-center flex-wrap">
                    <strong class="bg-white text-dark px-4 py-2 mr-3 shadow-sm" style="font-size: 1.3rem; letter-spacing: 2px; font-family: monospace; border-radius: 10px;" id="bannerPasswordText">{{ session('new_password') }}</strong>
                    <button class="btn btn-light btn-sm text-dark px-4 py-2 shadow-sm" style="border-radius: 10px; font-weight: bold;" onclick="copyBannerPassword()">
                        <i class="fas fa-copy mr-1"></i> Salin Password
                    </button>
                    <span id="bannerCopySuccess" class="text-white ml-3 small font-weight-bold" style="display: none; background: rgba(0,0,0,0.2); padding: 5px 12px; border-radius: 20px;"><i class="fas fa-check mr-1"></i> Tersalin!</span>
                </div>
            </div>
        </div>
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close" style="outline: none; text-shadow: none; opacity: 0.8; padding: 1.5rem;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card border-0 glass-panel-card mb-4">
        <div class="card-header bg-white p-4 border-0 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-users text-primary mr-2"></i> Daftar Pegawai</h5>
            </div>
            <div class="mt-3 mt-sm-0">
                @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->can('guru.create'))
                <a href="{{ route('pegawaimanager.import') }}" class="btn btn-gradient-warning rounded-pill shadow-sm mr-2 font-weight-bold" title="Import data masal dari CSV">
                    <i class="fas fa-cloud-upload-alt mr-1"></i> Import CSV
                </a>
                @endif

                @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->can('guru.view'))
                <a href="{{ route('pegawaimanager.export') }}" class="btn btn-gradient-success rounded-pill shadow-sm mr-2 font-weight-bold" title="Unduh data dalam format CSV/Excel">
                    <i class="fas fa-file-excel mr-1"></i> Export Data
                </a>
                @endif
                
                @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->can('guru.create'))
                <a href="{{ route('pegawaimanager.create') }}" class="btn btn-gradient-primary rounded-pill shadow-sm font-weight-bold">
                    <i class="fas fa-plus mr-1"></i> Tambah Pegawai
                </a>
                @endif
            </div>
        </div>

        <div class="card-body p-4 pt-0">
            {{-- Filter & Search Section --}}
            <div class="search-panel mb-4">
                <form action="{{ route('pegawaimanager.index') }}" method="GET">
                    <div class="row align-items-end">
                        
                        {{-- Pencarian --}}
                        <div class="col-lg-4 col-md-4 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label class="text-xs font-weight-bold ml-1 text-muted text-uppercase"><i class="fas fa-search mr-1"></i> Cari Nama / Email</label>
                                <input type="text" name="search" class="form-control" style="border-radius: 10px; border: 1px solid #cbd5e1; padding: 0.6rem 1rem;" 
                                       placeholder="Ketik kata kunci pencarian..." value="{{ request('search') }}">
                            </div>
                        </div>
                        
                        {{-- Tipe --}}
                        <div class="col-lg-3 col-md-3 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label class="text-xs font-weight-bold ml-1 text-muted text-uppercase"><i class="fas fa-tag mr-1"></i> Tipe Pegawai</label>
                                <select name="type" class="form-control" style="border-radius: 10px; border: 1px solid #cbd5e1; height: 43px;" onchange="this.form.submit()">
                                    <option value="">-- Semua Kategori --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                            {{ $type->nama_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Role --}}
                        <div class="col-lg-3 col-md-3 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label class="text-xs font-weight-bold ml-1 text-muted text-uppercase"><i class="fas fa-shield-alt mr-1"></i> Role</label>
                                <select name="role" class="form-control" style="border-radius: 10px; border: 1px solid #cbd5e1; height: 43px;" onchange="this.form.submit()">
                                    <option value="">-- Semua Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="col-lg-2 col-md-2 d-flex">
                            <button type="submit" class="btn btn-gradient-primary flex-fill mr-2" style="border-radius: 10px; height: 43px;">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('pegawaimanager.index') }}" class="btn btn-light shadow-sm" style="border-radius: 10px; height: 43px; display: flex; align-items: center; justify-content: center; width: 43px;" title="Bersihkan Filter">
                                <i class="fas fa-sync-alt text-muted"></i>
                            </a>
                        </div>

                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;" class="text-center">No</th>
                            <th>Identitas Pegawai</th>
                            <th class="text-center">Hak Akses Sistem</th>
                            <th class="text-center">Kategori / Tipe</th>
                            <th class="text-center">Status Akun</th>
                            <th class="text-center" style="width: 250px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pegawaiManagers as $index => $item)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">{{ $pegawaiManagers->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="position: relative; display: inline-block;">
                                        <img src="{{ $item->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($item->nama).'&background=4361ee&color=fff&size=50' }}" 
                                             class="rounded-circle mr-3" 
                                             style="width: 50px; height: 50px; border: 3px solid #e2e8f0; object-fit: cover;">
                                        @if(($item->user->account_status ?? 'inactive') === 'active')
                                            <span class="bg-success rounded-circle" style="position: absolute; bottom: 0; right: 12px; width: 14px; height: 14px; border: 2px solid #fff;"></span>
                                        @else
                                            <span class="bg-danger rounded-circle" style="position: absolute; bottom: 0; right: 12px; width: 14px; height: 14px; border: 2px solid #fff;"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $item->nama }}</div>
                                        <div class="text-muted small"><i class="fas fa-envelope text-primary mr-1 opacity-75"></i> {{ $item->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge badge-soft-primary">
                                    <i class="fas fa-shield-alt mr-1"></i> {{ $item->user->primary_role ?? 'Pegawai' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge badge-soft-info">
                                    {{ $item->typePegawai->nama_type ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                @php $status = $item->user->account_status ?? 'inactive'; @endphp
                                @if($status === 'active')
                                    <span class="badge badge-soft-success">Aktif</span>
                                @else
                                    <span class="badge badge-soft-danger">Nonaktif</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('pegawaimanager.show', $item->id) }}" class="btn-action btn-action-view" title="Lihat Detail Profil">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('pegawaimanager.print-card', $item->id) }}" target="_blank" class="btn-action btn-action-print" title="Cetak Kartu QR Pegawai">
                                        <i class="fas fa-qrcode"></i>
                                    </a>

                                    @can('guru.edit')
                                    <a href="{{ route('pegawaimanager.edit', $item->id) }}" class="btn-action btn-action-edit" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    
                                    @if(auth()->user()->hasRole(['SUPER_ADMIN', 'STAF_TU']))
                                    <form action="{{ route('pegawaimanager.reset-password', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="button" class="btn-action btn-action-reset btn-confirm-reset" title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>

                                    @php $itemStatus = $item->user->account_status ?? 'inactive'; @endphp
                                    <form action="{{ route('pegawaimanager.toggle-status', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-action {{ $itemStatus === 'active' ? 'btn-action-status-off' : 'btn-action-status-on' }}" title="{{ $itemStatus === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            <i class="fas {{ $itemStatus === 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if(auth()->user()->hasRole(['SUPER_ADMIN', 'STAF_TU']))
                                    <form action="{{ route('pegawaimanager.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action btn-action-delete btn-delete" title="Hapus Data" data-name="{{ $item->nama }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-5 bg-light rounded" style="border: 2px dashed #cbd5e1;">
                                    <div class="mb-3">
                                        <i class="fas fa-search-minus fa-4x text-muted opacity-50"></i>
                                    </div>
                                    @if(request()->anyFilled(['search', 'type', 'role']))
                                        <h5 class="font-weight-bold text-dark mb-2">Hasil Pencarian Tidak Ditemukan</h5>
                                        <p class="text-muted mb-4">Coba gunakan kata kunci lain atau bersihkan filter Anda.</p>
                                        <a href="{{ route('pegawaimanager.index') }}" class="btn btn-gradient-primary rounded-pill px-4">Bersihkan Filter</a>
                                    @else
                                        <h5 class="font-weight-bold text-dark mb-2">Daftar Pegawai Masih Kosong</h5>
                                        <p class="text-muted mb-4">Silakan tambahkan data pegawai pertama Anda untuk memulai.</p>
                                        @can('guru.create')
                                        <a href="{{ route('pegawaimanager.create') }}" class="btn btn-gradient-primary rounded-pill px-4"><i class="fas fa-plus mr-2"></i> Tambah Pegawai</a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Links --}}
        @if($pegawaiManagers->hasPages())
            <div class="card-footer bg-white p-4 border-top d-flex flex-wrap justify-content-between align-items-center">
                <div class="text-muted small font-weight-bold mb-3 mb-md-0">
                    Menampilkan <span class="text-primary">{{ $pegawaiManagers->firstItem() }}</span> sampai <span class="text-primary">{{ $pegawaiManagers->lastItem() }}</span> 
                    dari total <span class="text-primary">{{ $pegawaiManagers->total() }}</span> pegawai.
                </div>
                <div>
                    {{ $pegawaiManagers->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('new_password'))
            // Auto-copy to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText("{{ session('new_password') }}").then(function() {
                    console.log('Password otomatis tersalin.');
                }).catch(function(err) {
                    console.error('Gagal menyalin secara otomatis: ', err);
                });
            }

            Swal.fire({
                title: 'Password Direset!',
                html: '<p class="text-muted mb-4">Password login default baru untuk pegawai adalah:</p><strong style="font-size: 1.8em; letter-spacing: 3px; background: #f8fafc; padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; color: #1e293b;" id="newPasswordText">{{ session('new_password') }}</strong><br><br><button class="btn btn-gradient-primary rounded-pill px-4 py-2 mt-2" onclick="copyPasswordToClipboard()"><i class="fas fa-copy mr-2"></i> Salin Password</button><br><small class="text-success font-weight-bold d-block mt-3 bg-success-light p-2 rounded"><i class="fas fa-check-circle mr-1"></i>Password telah disalin otomatis ke clipboard</small>',
                icon: 'success',
                confirmButtonText: 'Tutup',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-light rounded-pill px-4 py-2 font-weight-bold mt-3 shadow-sm border'
                },
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        @endif

        // Reset Password SweetAlert Confirmation
        $(document).on('click', '.btn-confirm-reset', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            
            Swal.fire({
                title: 'Reset Password?',
                html: "Masukkan password baru untuk pegawai ini.<br><small class='text-muted'>Biarkan kosong untuk menghasilkan password acak secara otomatis.</small>",
                input: 'text',
                inputPlaceholder: 'Ketik password baru...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset Password',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-gradient-primary rounded-pill px-4 mx-2',
                    cancelButton: 'btn btn-light rounded-pill px-4 mx-2 border',
                    input: 'form-control rounded-pill text-center mx-auto mt-3 w-75'
                },
                inputValidator: (value) => {
                    if (value && value.trim().length < 6) {
                        return 'Password minimal 6 karakter!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let newPassword = result.value;
                    form.find('input[name="new_password"]').remove();
                    if (newPassword && newPassword.trim() !== '') {
                        form.append(`<input type="hidden" name="new_password" value="${newPassword.trim()}">`);
                    }
                    form.submit();
                }
            });
        });

        // Delete SweetAlert Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let name = $(this).data('name');
            
            Swal.fire({
                title: 'Hapus Pegawai?',
                html: `Anda yakin ingin menghapus data <b>${name}</b> secara permanen? Seluruh data terkait (termasuk user login) akan ikut terhapus.`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4 mx-2',
                    cancelButton: 'btn btn-light rounded-pill px-4 mx-2 border'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Fungsi salin password ke clipboard
    window.copyPasswordToClipboard = function() {
        const text = document.getElementById('newPasswordText').innerText;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                const btn = document.querySelector('.swal2-html-container button');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i> Tersalin!';
                    btn.className = 'btn btn-gradient-success rounded-pill px-4 py-2 mt-2';
                    setTimeout(() => {
                        btn.innerHTML = '<i class="fas fa-copy mr-2"></i> Salin Password';
                        btn.className = 'btn btn-gradient-primary rounded-pill px-4 py-2 mt-2';
                    }, 2000);
                }
            }).catch(function(err) {
                alert('Gagal menyalin: ' + err);
            });
        }
    };

    window.copyBannerPassword = function() {
        const text = document.getElementById('bannerPasswordText').innerText;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                const successText = document.getElementById('bannerCopySuccess');
                if (successText) {
                    successText.style.display = 'inline-block';
                    setTimeout(() => {
                        successText.style.display = 'none';
                    }, 2000);
                }
            });
        }
    };
</script>
@endpush
