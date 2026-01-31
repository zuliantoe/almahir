@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <form action="{{ route('rolepermission.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <x-card title="Informasi Role" icon="fas fa-user-shield">
            @if($role->is_system)
            <x-alert type="warning" dismissible>
                <i class="fas fa-lock mr-1"></i>
                <strong>Role Sistem:</strong> Nama role tidak dapat diubah. Anda hanya dapat mengubah permissions.
            </x-alert>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Role (ID)</label>
                        @if($role->is_system)
                        <input type="text" class="form-control" value="{{ $role->name }}" disabled>
                        <small class="text-muted">Role sistem tidak dapat diubah namanya</small>
                        @else
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $role->name) }}"
                               placeholder="Contoh: WALI_KELAS"
                               required>
                        <small class="text-muted">Hanya huruf kapital dan underscore</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <x-input label="Nama Tampilan" 
                             name="display_name" 
                             placeholder="Contoh: Wali Kelas" 
                             :value="old('display_name', $role->display_name)" 
                             required />
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" 
                          class="form-control @error('description') is-invalid @enderror" 
                          rows="2" 
                          placeholder="Deskripsi singkat tentang role ini...">{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Users dengan Role Ini</span>
                            <span class="info-box-number">{{ $role->users()->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-success"><i class="fas fa-key"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Permissions</span>
                            <span class="info-box-number">
                                @if(in_array('*', $role->permissions ?? []))
                                    Semua ({{ \App\Services\PermissionRegistry::count() }})
                                @else
                                    {{ count($role->permissions ?? []) }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="Permissions" icon="fas fa-key" class="mt-3">
            <p class="text-muted mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                Pilih permissions yang dimiliki role ini. Centang semua yang diperlukan.
            </p>

            @php
                $rolePermissions = $role->permissions ?? [];
                $hasAllPermissions = in_array('*', $rolePermissions);
            @endphp

            {{-- Quick actions --}}
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-outline-success" id="checkAll">
                    <i class="fas fa-check-double mr-1"></i> Centang Semua
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="uncheckAll">
                    <i class="fas fa-times mr-1"></i> Hapus Semua
                </button>
            </div>

            @foreach($permissionGroups as $groupName => $modules)
            <div class="card card-outline card-primary mb-3">
                <div class="card-header py-2">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder mr-2"></i>{{ $groupName }}
                    </h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 200px;">Modul</th>
                                    <th class="text-center">Lihat</th>
                                    <th class="text-center">Tambah</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Hapus</th>
                                    <th class="text-center">Lainnya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $moduleKey => $module)
                                <tr>
                                    <td>
                                        <strong>{{ $module['label'] }}</strong>
                                    </td>
                                    @php
                                        $actions = ['view', 'create', 'edit', 'delete'];
                                        $otherActions = [];
                                        foreach ($module['permissions'] as $perm) {
                                            $action = explode('.', $perm)[1] ?? '';
                                            if (!in_array($action, $actions)) {
                                                $otherActions[] = ['key' => $perm, 'action' => $action];
                                            }
                                        }
                                    @endphp
                                    @foreach($actions as $action)
                                        <td class="text-center">
                                            @if(in_array("{$moduleKey}.{$action}", $module['permissions']))
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input permission-checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $moduleKey }}.{{ $action }}"
                                                       id="perm_{{ $moduleKey }}_{{ $action }}"
                                                       {{ ($hasAllPermissions || in_array("{$moduleKey}.{$action}", old('permissions', $rolePermissions))) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="perm_{{ $moduleKey }}_{{ $action }}"></label>
                                            </div>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        @if(count($otherActions) > 0)
                                            @foreach($otherActions as $other)
                                            <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                <input type="checkbox" 
                                                       class="custom-control-input permission-checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $other['key'] }}"
                                                       id="perm_{{ str_replace('.', '_', $other['key']) }}"
                                                       {{ ($hasAllPermissions || in_array($other['key'], old('permissions', $rolePermissions))) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="perm_{{ str_replace('.', '_', $other['key']) }}">
                                                    {{ ucfirst($other['action']) }}
                                                </label>
                                            </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </x-card>

        <div class="card mt-3">
            <div class="card-body d-flex justify-content-between">
                <a href="{{ route('rolepermission.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="primary" icon="fas fa-save">Simpan Perubahan</x-btn>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    
    document.getElementById('checkAll').addEventListener('click', function() {
        checkboxes.forEach(cb => cb.checked = true);
    });
    
    document.getElementById('uncheckAll').addEventListener('click', function() {
        checkboxes.forEach(cb => cb.checked = false);
    });
});
</script>
@endpush
