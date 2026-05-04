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

    <x-card title="Daftar Manajemen Role" icon="fas fa-user-shield">
        <x-slot name="tools">
            <a href="{{ route('rolepermission.create') }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm btn-animate gradient-primary border-0 font-weight-bold">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Role Baru
            </a>
        </x-slot>

        <div class="table-responsive mt-2">
            <table class="table table-hover table-premium">
                <thead>
                    <tr>
                        <th>Identitas Role</th>
                        <th>Deskripsi / Keterangan</th>
                        <th class="text-center">Akses Permission</th>
                        <th class="text-center">Pengguna</th>
                        <th class="text-center">Status Role</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" 
                                     style="width: 42px; height: 42px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); flex-shrink: 0;">
                                    <i class="fas fa-user-tag text-white" style="font-size: 1rem;"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark mb-0">{{ $role->display_name }}</div>
                                    <small class="text-muted font-italic"><code>{{ $role->name }}</code></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small" style="max-width: 250px;">
                                {{ $role->description ? Str::limit($role->description, 100) : 'Tidak ada deskripsi.' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if(in_array('*', $role->permissions ?? []))
                                <span class="badge badge-success-soft px-3 py-2 text-success border-success" style="border-radius: 20px; background: rgba(40,167,69,0.1);">
                                    <i class="fas fa-infinity mr-1"></i> Full Access ({{ $totalPermissions }})
                                </span>
                            @else
                                <div class="d-inline-block">
                                    <span class="badge badge-info-soft px-3 py-2 text-primary" style="border-radius: 20px; background: rgba(0,123,255,0.1); font-weight: 700;">
                                        {{ count($role->permissions ?? []) }} <small class="text-muted">/ {{ $totalPermissions }}</small>
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light border px-3 py-2" style="border-radius: 12px; font-weight: 600;">
                                <i class="fas fa-users mr-1 text-secondary"></i> {{ $role->users_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($role->is_system)
                                <span class="badge badge-warning-soft px-3 py-2 text-warning border border-warning" style="border-radius: 20px; background: rgba(255,193,7,0.05);" title="Role sistem tidak dapat dihapus">
                                    <i class="fas fa-lock mr-1"></i> System Protected
                                </span>
                            @else
                                <span class="badge badge-light border px-3 py-2 text-muted" style="border-radius: 20px;">
                                    <i class="fas fa-cog mr-1"></i> Custom Role
                                </span>
                            @endif
                        </td>
                        <td class="text-center py-3">
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('rolepermission.edit', $role->id) }}" 
                                   class="btn btn-outline-info btn-sm mx-1 shadow-sm px-2 btn-animate rounded-circle"
                                   style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                   title="Edit Role & Permissions">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$role->is_system)
                                <form action="{{ route('rolepermission.destroy', $role->id) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm mx-1 shadow-sm px-2 btn-delete btn-animate rounded-circle"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                            title="Hapus Role">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-outline-secondary btn-sm mx-1 px-2 rounded-circle opacity-50" 
                                        style="width: 35px; height: 35px; cursor: not-allowed;" disabled title="Role sistem terkunci">
                                    <i class="fas fa-lock"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5 bg-white">
                            <div class="mb-3">
                                <i class="fas fa-user-shield fa-4x text-gray-200"></i>
                            </div>
                            <h5 class="font-weight-bold mb-1">Daftar Role Masih Kosong</h5>
                            <p class="small">Belum ada role yang dikonfigurasi dalam sistem.</p>
                            <a href="{{ route('rolepermission.create') }}" class="btn btn-primary btn-sm mt-2 rounded-pill px-4">Buat Role Pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Permission Legend Section --}}
    <div class="card border-0 shadow-sm mt-4 overflow-hidden" style="border-radius: 15px;">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-info-circle mr-2 text-info"></i> Daftar Modul & Kapasitas Permission</h6>
        </div>
        <div class="card-body bg-light p-4">
            <div class="row">
                @foreach(\App\Services\PermissionRegistry::all() as $groupName => $modules)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="glass-card p-3 h-100 border-0 shadow-xs" style="background: rgba(255,255,255,0.7); border-radius: 12px;">
                        <h6 class="font-weight-bolder text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-layer-group mr-1 small"></i> {{ $groupName }}
                        </h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($modules as $moduleKey => $module)
                            <li class="mb-2 d-flex align-items-start">
                                <i class="fas fa-check-circle text-success mt-1 mr-2" style="font-size: 0.8rem;"></i>
                                <span class="small font-weight-bold text-dark">{{ $module['label'] }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
