@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card border-0 shadow-lg mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header gradient-primary border-0 p-4">
                <h3 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-user-edit mr-2"></i> Edit Data User: {{ $user->name }}
                </h3>
            </div>
            
            <div class="card-body p-4 bg-light">
                <div class="glass-card p-4">
            <div class="row">
                <div class="col-md-6">
                    <x-input label="Nama Lengkap" name="name" :value="old('name', $user->name)" required />
                </div>
                <div class="col-md-6">
                    <x-input label="Email" name="email" type="email" :value="old('email', $user->email)" required />
                </div>
            </div>

            <div class="alert alert-info rounded shadow-sm border-0 border-left border-info p-3 mb-4" style="border-left-width: 5px !important; background: rgba(23, 162, 184, 0.05);">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x text-info mr-3"></i>
                    <div>
                        <strong class="text-info d-block mb-1">Informasi Password</strong>
                        <span class="text-muted">Kosongkan kolom password di bawah ini jika Anda tidak ingin mengubah password akun ini.</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input label="Password Baru (Opsional)" name="password" type="password" placeholder="Minimal 8 karakter" />
                </div>
                <div class="col-md-6">
                    <x-input label="Konfirmasi Password" name="password_confirmation" type="password" placeholder="Ulangi password" />
                </div>
            </div>

            <div class="form-group">
                <label>Role <span class="text-danger">*</span></label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4">
                            <div class="form-check p-3 bg-white rounded border shadow-sm mb-2 hover-elevate">
                                <input type="checkbox" class="form-check-input ml-1 mt-2" name="roles[]" value="{{ $role->id }}" 
                                       id="role_{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label ml-4 font-weight-bold" for="role_{{ $role->id }}">
                                    {{ $role->display_name }} <br><span class="badge badge-light text-muted">{{ $role->name }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('roles')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Linked Data Info --}}
            @if($linkedData)
            <div class="alert alert-success rounded shadow-sm border-0 border-left border-success p-3 mb-4 mt-3" style="border-left-width: 5px !important; background: rgba(40, 167, 69, 0.05);">
                <div class="d-flex align-items-center">
                    <div class="bg-success-light text-success rounded-circle p-3 d-flex align-items-center justify-content-center mr-3" style="background: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-link fa-lg"></i>
                    </div>
                    <div>
                        <strong class="text-success d-block mb-1">Data Pegawai/Siswa Terhubung</strong>
                        <span class="text-muted">User ini terhubung dengan profil <b>{{ $linkedData['type'] }}</b> bernama <b class="text-dark">{{ $linkedData['nama'] }}</b>.</span>
                    </div>
                </div>
                <input type="hidden" name="ref_type" value="{{ $user->ref_type }}">
                <input type="hidden" name="ref_id" value="{{ $user->ref_id }}">
            </div>
            @else
            <input type="hidden" name="ref_type" value="">
            <input type="hidden" name="ref_id" value="">
            @endif

            <div class="form-group">
                <label>Status Akun <span class="text-danger">*</span></label>
                <select name="account_status" class="form-control @error('account_status') is-invalid @enderror" required>
                    <option value="active" {{ old('account_status', $user->account_status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('account_status', $user->account_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('account_status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($user->last_login_at)
            <div class="p-3 bg-white rounded border mt-4 d-flex align-items-center">
                <i class="fas fa-clock text-secondary mr-3 fa-2x"></i>
                <div>
                    <div class="text-muted small font-weight-bold text-uppercase">Terakhir Login Ke Sistem</div>
                    <div class="font-weight-bolder">{{ $user->last_login_at->format('d M Y, H:i') }} <span class="text-muted font-weight-normal">({{ $user->last_login_at->diffForHumans() }})</span></div>
                </div>
            </div>
            @endif

            <hr class="mt-4 mb-4">
            <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary rounded-pill px-4 py-2 shadow-sm btn-animate font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
                <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 shadow-sm btn-animate font-weight-bold" style="color: #fff !important; text-shadow: 0px 1px 2px rgba(0,0,0,0.2);">
                    <i class="fas fa-save mr-2"></i> Update Data User
                </button>
            </div>
            
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
