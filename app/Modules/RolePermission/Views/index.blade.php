@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <x-card title="Daftar Role" icon="fas fa-user-shield">
        <x-slot name="tools">
            <a href="{{ route('rolepermission.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Role
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Role</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Permissions</th>
                        <th class="text-center">Users</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>
                            <strong>{{ $role->display_name }}</strong>
                            <br><small class="text-muted"><code>{{ $role->name }}</code></small>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($role->description, 60) ?? '-' }}</small>
                        </td>
                        <td class="text-center">
                            @if(in_array('*', $role->permissions ?? []))
                                <span class="badge badge-success">
                                    <i class="fas fa-infinity mr-1"></i> All ({{ $totalPermissions }})
                                </span>
                            @else
                                <span class="badge badge-info">
                                    {{ count($role->permissions ?? []) }} / {{ $totalPermissions }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary">{{ $role->users_count }}</span>
                        </td>
                        <td class="text-center">
                            @if($role->is_system)
                                <span class="badge badge-warning" title="Role sistem tidak dapat dihapus">
                                    <i class="fas fa-lock mr-1"></i> System
                                </span>
                            @else
                                <span class="badge badge-light">Custom</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('rolepermission.edit', $role->id) }}" 
                                   class="btn btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$role->is_system)
                                <form action="{{ route('rolepermission.destroy', $role->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus role {{ $role->display_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-secondary" disabled title="Role sistem tidak dapat dihapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-user-shield fa-3x mb-3 d-block"></i>
                            Belum ada role. <a href="{{ route('rolepermission.create') }}">Tambah role pertama</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Permission Legend --}}
    <x-card title="Keterangan Permission" icon="fas fa-info-circle" class="mt-3">
        <div class="row">
            @foreach(\App\Services\PermissionRegistry::all() as $groupName => $modules)
            <div class="col-md-3 mb-3">
                <h6 class="font-weight-bold text-primary">{{ $groupName }}</h6>
                <ul class="list-unstyled small">
                    @foreach($modules as $moduleKey => $module)
                    <li>
                        <i class="fas fa-check-circle text-success mr-1"></i>
                        {{ $module['label'] }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </x-card>
</div>
@endsection
