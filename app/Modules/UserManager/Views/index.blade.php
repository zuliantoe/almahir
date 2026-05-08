@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="card border-0 shadow-lg mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header gradient-primary border-0 p-4 d-flex justify-content-between align-items-center">
            <h3 class="card-title text-white font-weight-bold mb-0">
                <i class="fas fa-users-cog mr-2"></i> Daftar User
            </h3>
            <a href="{{ route('users.create') }}" class="btn btn-light text-primary btn-sm rounded-pill px-4 py-2 shadow-sm btn-animate font-weight-bold">
                <i class="fas fa-plus mr-1"></i> Tambah User Baru
            </a>
        </div>

        <div class="card-body p-4 bg-light">
            <div class="glass-card p-4">

        {{-- Filters --}}
        <div class="p-3 mb-4 bg-white rounded border shadow-xs">
            <form method="GET" action="{{ route('users.index') }}" class="m-0">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">Filter Role</label>
                            <select name="role" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">Cari Nama / Email</label>
                            <input type="text" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-info btn-block rounded-pill px-4 py-2 shadow-sm btn-animate font-weight-bold">
                            <i class="fas fa-search mr-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="table-responsive bg-white rounded shadow-sm border-0">
            <table class="table table-premium table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 text-muted align-middle">Nama User</th>
                        <th class="border-0 text-muted align-middle">Email</th>
                        <th class="border-0 text-muted align-middle">Hak Akses (Role)</th>
                        <th class="border-0 text-muted align-middle text-nowrap">Keterkaitan Data</th>
                        <th class="border-0 text-muted text-center align-middle">Status</th>
                        <th class="border-0 text-muted text-center align-middle" width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="align-middle">
                            <td class="align-middle font-weight-bold text-dark text-nowrap">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center mr-3 font-weight-bold flex-shrink-0" style="width: 40px; height: 40px; background: rgba(0, 123, 255, 0.1);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td class="align-middle text-muted">{{ $user->email }}</td>
                            <td class="align-middle">
                                @foreach($user->roles as $role)
                                    <span class="badge badge-primary px-3 py-2 shadow-sm rounded-pill mb-1 d-inline-block" style="font-size: 0.75rem;">
                                        {{ $role->display_name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="align-middle text-nowrap">
                                @if($user->ref_type && $user->ref_id)
                                    @php
                                        $linkedType = match($user->ref_type) {
                                            'Modules\\Siswa\\Models\\Siswa' => 'Siswa',
                                            'Modules\\Guru\\Models\\Guru' => 'Guru',
                                            'Modules\\WaliMurid\\Models\\WaliMurid' => 'Wali',
                                            default => 'Lainnya',
                                        };
                                    @endphp
                                    <span class="badge badge-info px-3 py-2 shadow-sm rounded-pill">
                                        <i class="fas fa-link mr-1"></i> {{ $linkedType }}
                                    </span>
                                @else
                                    <span class="badge badge-light text-muted px-3 py-2 rounded-pill border">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if($user->account_status === 'active')
                                    <span class="badge badge-success px-3 py-2 shadow-sm rounded-pill"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-secondary px-3 py-2 shadow-sm rounded-pill"><i class="fas fa-minus-circle mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center align-items-center flex-nowrap" style="gap: 8px;">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm btn-animate" title="Edit Data">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    
                                    <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" class="btn btn-{{ $user->account_status === 'active' ? 'secondary' : 'success' }} btn-sm rounded-pill px-3 shadow-sm btn-animate"
                                                title="{{ $user->account_status === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            <i class="fas fa-{{ $user->account_status === 'active' ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0 p-0" 
                                          onsubmit="return confirm('Apakah Anda sangat yakin ingin menghapus permanen data user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm btn-animate" title="Hapus Permanen">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-2"></i>
                                <p>Tidak ada data user.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 border-top pt-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small font-weight-bold">
                    Menampilkan {{ $users->count() }} dari total {{ $users->total() }} user terdaftar
                </div>
                <div>
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
        
            </div>
        </div>
    </div>
</div>
@endsection
