@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <x-card title="Edit User" icon="fas fa-user-edit">
            <div class="row">
                <div class="col-md-6">
                    <x-input label="Nama Lengkap" name="name" :value="old('name', $user->name)" required />
                </div>
                <div class="col-md-6">
                    <x-input label="Email" name="email" type="email" :value="old('email', $user->email)" required />
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Password:</strong> Kosongkan jika tidak ingin mengubah password.
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
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="roles[]" value="{{ $role->id }}" 
                                       id="role_{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->display_name }} <small class="text-muted">({{ $role->name }})</small>
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
            <div class="alert alert-success">
                <i class="fas fa-link mr-1"></i>
                <strong>Data Terhubung:</strong> 
                {{ $linkedData['type'] }} - <strong>{{ $linkedData['nama'] }}</strong>
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
            <div class="alert alert-secondary">
                <i class="fas fa-clock mr-1"></i>
                <strong>Last Login:</strong> {{ $user->last_login_at->format('d M Y, H:i') }} 
                ({{ $user->last_login_at->diffForHumans() }})
            </div>
            @endif

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="warning" icon="fas fa-save">Update</x-btn>
            </div>
        </x-card>
    </form>
</div>
@endsection
