@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <form action="{{ route('rolepermission.store') }}" method="POST">
        @csrf
        
        <x-card title="Konfigurasi Role Baru" icon="fas fa-user-shield">
            <x-slot name="tools">
                <a href="{{ route('rolepermission.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm btn-animate">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </x-slot>

            <div class="p-4 glass-card mb-4 border-0 shadow-sm" style="background: rgba(255,255,255,0.5); border-radius: 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small ml-1"><i class="fas fa-id-badge mr-1"></i> NAMA ROLE (ID SYSTEM)</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="CONTOH: WALI_KELAS" value="{{ old('name') }}" required
                                   style="border-radius: 10px; text-transform: uppercase;">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted ml-1 font-italic">Hanya huruf kapital dan underscore. Contoh: WALI_KELAS</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small ml-1"><i class="fas fa-desktop mr-1"></i> NAMA TAMPILAN (DISPLAY)</label>
                            <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" 
                                   placeholder="Contoh: Wali Kelas" value="{{ old('display_name') }}" required
                                   style="border-radius: 10px;">
                            @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-bold text-muted small ml-1"><i class="fas fa-align-left mr-1"></i> DESKRIPSI SINGKAT</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="2" placeholder="Jelaskan fungsi atau tanggung jawab role ini..."
                              style="border-radius: 10px;">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm rounded-pill px-4 mb-4" style="background: rgba(23,162,184,0.1); color: #0c5460;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-key fa-lg mr-3"></i>
                    <div>
                        <strong class="d-block">Matriks Hak Akses (Permissions)</strong>
                        <span class="small">Tentukan modul mana saja yang dapat diakses dan aksi apa saja yang diperbolehkan untuk role ini.</span>
                    </div>
                </div>
            </div>

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
                                                       class="custom-control-input" 
                                                       name="permissions[]" 
                                                       value="{{ $moduleKey }}.{{ $action }}"
                                                       id="perm_{{ $moduleKey }}_{{ $action }}"
                                                       {{ in_array("{$moduleKey}.{$action}", old('permissions', [])) ? 'checked' : '' }}>
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
                                                           class="custom-control-input" 
                                                           name="permissions[]" 
                                                           value="{{ $other['key'] }}"
                                                           id="perm_{{ str_replace('.', '_', $other['key']) }}"
                                                           {{ in_array($other['key'], old('permissions', [])) ? 'checked' : '' }}>
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
                    <i class="fas fa-info-circle mr-1"></i> Pastikan role yang dibuat sesuai dengan kebutuhan operasional sistem.
                </div>
                <div class="d-flex" style="gap: 10px;">
                    <a href="{{ route('rolepermission.index') }}" class="btn btn-light rounded-pill px-4 shadow-xs btn-animate font-weight-bold">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm btn-animate gradient-primary border-0 font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Data Role
                    </button>
                </div>
            </div>
        </x-card>
    </form>
</div>

<style>
/* Custom larger switch if needed */
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
</style>
@endsection
