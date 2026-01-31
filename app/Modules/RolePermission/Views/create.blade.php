@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <form action="{{ route('rolepermission.store') }}" method="POST">
        @csrf
        
        <x-card title="Informasi Role" icon="fas fa-user-shield">
            <div class="row">
                <div class="col-md-6">
                    <x-input label="Nama Role (ID)" 
                             name="name" 
                             placeholder="Contoh: WALI_KELAS" 
                             :value="old('name')" 
                             required
                             help="Hanya huruf kapital dan underscore. Contoh: WALI_KELAS, KEPALA_SEKOLAH" />
                </div>
                <div class="col-md-6">
                    <x-input label="Nama Tampilan" 
                             name="display_name" 
                             placeholder="Contoh: Wali Kelas" 
                             :value="old('display_name')" 
                             required />
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" 
                          class="form-control @error('description') is-invalid @enderror" 
                          rows="2" 
                          placeholder="Deskripsi singkat tentang role ini...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </x-card>

        <x-card title="Permissions" icon="fas fa-key" class="mt-3">
            <p class="text-muted mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                Pilih permissions yang dimiliki role ini. Centang semua yang diperlukan.
            </p>

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
                                                       class="custom-control-input" 
                                                       name="permissions[]" 
                                                       value="{{ $moduleKey }}.{{ $action }}"
                                                       id="perm_{{ $moduleKey }}_{{ $action }}"
                                                       {{ in_array("{$moduleKey}.{$action}", old('permissions', [])) ? 'checked' : '' }}>
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
                                                       class="custom-control-input" 
                                                       name="permissions[]" 
                                                       value="{{ $other['key'] }}"
                                                       id="perm_{{ str_replace('.', '_', $other['key']) }}"
                                                       {{ in_array($other['key'], old('permissions', [])) ? 'checked' : '' }}>
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
                <x-btn type="submit" variant="primary" icon="fas fa-save">Simpan Role</x-btn>
            </div>
        </div>
    </form>
</div>
@endsection
