@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <form action="{{ route('rolepermission.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <x-card title="Edit Konfigurasi Role" icon="fas fa-user-shield">
            <x-slot name="tools">
                <a href="{{ route('rolepermission.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm btn-animate">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </x-slot>

            @if($role->is_system)
            <div class="alert alert-warning border-0 shadow-sm rounded-pill px-4 mb-4" style="background: rgba(255,193,7,0.1); color: #856404;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-lock fa-lg mr-3"></i>
                    <div>
                        <strong>Role Sistem Terproteksi</strong>
                        <span class="small d-block">Identitas utama role ini tidak dapat diubah karena merupakan bagian dari core sistem. Anda hanya dapat menyesuaikan matriks permissions.</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="p-4 glass-card mb-4 border-0 shadow-sm" style="background: rgba(255,255,255,0.5); border-radius: 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small ml-1"><i class="fas fa-id-badge mr-1"></i> NAMA ROLE (ID SYSTEM)</label>
                            <input type="text" name="name" class="form-control" value="{{ $role->name }}" 
                                   {{ $role->is_system ? 'disabled' : '' }} required
                                   style="border-radius: 10px; text-transform: uppercase; background: {{ $role->is_system ? '#f8f9fa' : '#fff' }};">
                            @if($role->is_system)
                                <input type="hidden" name="name" value="{{ $role->name }}">
                            @endif
                            <small class="text-muted ml-1 font-italic">Hanya huruf kapital dan underscore.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small ml-1"><i class="fas fa-desktop mr-1"></i> NAMA TAMPILAN (DISPLAY)</label>
                            <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" 
                                   placeholder="Contoh: Wali Kelas" value="{{ old('display_name', $role->display_name) }}" required
                                   style="border-radius: 10px;">
                            @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="font-weight-bold text-muted small ml-1"><i class="fas fa-align-left mr-1"></i> DESKRIPSI SINGKAT</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="2" placeholder="Jelaskan fungsi atau tanggung jawab role ini..."
                              style="border-radius: 10px;">{{ old('description', $role->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center p-3 bg-white shadow-xs" style="border-radius: 12px; border-left: 4px solid #17a2b8;">
                            <div class="rounded-circle bg-info-soft d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: rgba(23,162,184,0.1);">
                                <i class="fas fa-users text-info"></i>
                            </div>
                            <div>
                                <div class="text-xs text-muted font-weight-bold">PENGGUNA AKTIF</div>
                                <div class="h6 mb-0 font-weight-bold text-dark">{{ $role->users()->count() }} User</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center p-3 bg-white shadow-xs" style="border-radius: 12px; border-left: 4px solid #28a745;">
                            <div class="rounded-circle bg-success-soft d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: rgba(40,167,69,0.1);">
                                <i class="fas fa-key text-success"></i>
                            </div>
                            <div>
                                <div class="text-xs text-muted font-weight-bold">STATUS PERMISSION</div>
                                <div class="h6 mb-0 font-weight-bold text-dark">
                                    @if(in_array('*', $role->permissions ?? []))
                                        Full Access (Unlimited)
                                    @else
                                        {{ count($role->permissions ?? []) }} Aktif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="alert alert-info border-0 shadow-sm rounded-pill px-4 m-0 flex-grow-1 mr-3" style="background: rgba(23,162,184,0.05); color: #0c5460;">
                    <i class="fas fa-check-double mr-2"></i> <strong>Matriks Hak Akses (Permissions)</strong>
                </div>
                <div class="d-flex" style="gap: 8px;">
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-xs" id="checkAll">
                        <i class="fas fa-check-circle mr-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-xs" id="uncheckAll">
                        <i class="fas fa-times-circle mr-1"></i> Hapus Semua
                    </button>
                </div>
            </div>

            @php
                $rolePermissions = $role->permissions ?? [];
                $hasAllPermissions = in_array('*', $rolePermissions);
            @endphp

            @foreach($permissionGroups as $groupName => $modules)
            <div class="card card-outline card-primary mb-4 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-folder-open text-warning mr-2"></i>{{ $groupName }}
                    </h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-muted" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="pl-4 py-3" style="width: 250px;">Nama Modul</th>
                                    <th class="text-center py-3">Lihat</th>
                                    <th class="text-center py-3">Tambah</th>
                                    <th class="text-center py-3">Edit</th>
                                    <th class="text-center py-3">Hapus</th>
                                    <th class="text-center py-3">Lainnya / Spesifik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $moduleKey => $module)
                                <tr>
                                    <td class="pl-4 py-3 align-middle">
                                        <div class="font-weight-bold text-dark">{{ $module['label'] }}</div>
                                        <code class="text-xs text-muted">{{ $moduleKey }}.*</code>
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
                                        <td class="text-center align-middle">
                                            @if(in_array("{$moduleKey}.{$action}", $module['permissions']))
                                            <div class="custom-control custom-switch custom-switch-md">
                                                <input type="checkbox" 
                                                       class="custom-control-input permission-checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $moduleKey }}.{{ $action }}"
                                                       id="perm_{{ $moduleKey }}_{{ $action }}"
                                                       {{ ($hasAllPermissions || in_array("{$moduleKey}.{$action}", old('permissions', $rolePermissions))) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="perm_{{ $moduleKey }}_{{ $action }}"></label>
                                            </div>
                                            @else
                                            <span class="text-muted opacity-25">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center align-middle py-3">
                                        @if(count($otherActions) > 0)
                                            <div class="d-flex flex-wrap justify-content-center" style="gap: 10px;">
                                                @foreach($otherActions as $other)
                                                <div class="custom-control custom-checkbox bg-white border px-3 py-1 rounded shadow-xs" style="border-radius: 8px !important;">
                                                    <input type="checkbox" 
                                                           class="custom-control-input permission-checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $other['key'] }}"
                                                           id="perm_{{ str_replace('.', '_', $other['key']) }}"
                                                           {{ ($hasAllPermissions || in_array($other['key'], old('permissions', $rolePermissions))) ? 'checked' : '' }}>
                                                    <label class="custom-control-label small font-weight-bold" for="perm_{{ str_replace('.', '_', $other['key']) }}">
                                                        {{ strtoupper($other['action']) }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted opacity-25">—</span>
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

            <div class="mt-4 p-4 bg-white border shadow-sm d-flex justify-content-between align-items-center" style="border-radius: 15px;">
                <div class="text-muted small">
                    <i class="fas fa-info-circle mr-1 text-warning"></i> Perubahan pada permissions akan langsung berdampak pada seluruh pengguna dengan role ini.
                </div>
                <div class="d-flex" style="gap: 10px;">
                    <a href="{{ route('rolepermission.index') }}" class="btn btn-light rounded-pill px-4 shadow-xs btn-animate font-weight-bold">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm btn-animate gradient-primary border-0 font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan Role
                    </button>
                </div>
            </div>
        </x-card>
    </form>
</div>

<style>
.custom-switch-md .custom-control-label::before {
    height: 1.5rem;
    width: 2.75rem;
    border-radius: 2rem;
}
.custom-switch-md .custom-control-label::after {
    width: calc(1.5rem - 4px);
    height: calc(1.5rem - 4px);
    border-radius: 2rem;
}
.custom-switch-md .custom-control-input:checked ~ .custom-control-label::after {
    transform: translateX(1.25rem);
}
.opacity-25 { opacity: 0.25; }
.shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.bg-info-soft { background-color: rgba(23,162,184,0.1); }
.bg-success-soft { background-color: rgba(40,167,69,0.1); }
</style>
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
