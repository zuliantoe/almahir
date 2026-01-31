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

    <x-card title="Daftar User" icon="fas fa-users-cog">
        <x-slot name="tools">
            <a href="{{ route('users.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah User
            </a>
        </x-slot>

        {{-- Filters --}}
        <form method="GET" action="{{ route('users.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <select name="role" class="form-control">
                        <option value="">-- Semua Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <x-input name="search" placeholder="Cari nama atau email..." :value="request('search')" />
                </div>
                <div class="col-md-2">
                    <x-btn type="submit" variant="info" icon="fas fa-search">Filter</x-btn>
                </div>
            </div>
        </form>

        {{-- Users Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Link Data</th>
                        <th>Status</th>
                        <th width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->display_name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($user->ref_type && $user->ref_id)
                                    @php
                                        $linkedType = match($user->ref_type) {
                                            'Modules\\Siswa\\Models\\Siswa' => 'Siswa',
                                            'Modules\\Guru\\Models\\Guru' => 'Guru',
                                            'Modules\\WaliMurid\\Models\\WaliMurid' => 'Wali',
                                            default => 'Other',
                                        };
                                    @endphp
                                    <span class="badge badge-success">
                                        <i class="fas fa-link mr-1"></i>{{ $linkedType }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->account_status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-{{ $user->account_status === 'active' ? 'secondary' : 'success' }} btn-sm"
                                            title="{{ $user->account_status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-{{ $user->account_status === 'active' ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>Menampilkan {{ $users->count() }} dari {{ $users->total() }} user</div>
            {{ $users->withQueryString()->links() }}
        </div>
    </x-card>
</div>
@endsection
