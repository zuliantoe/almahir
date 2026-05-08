@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    {{-- Alerts handled globally via SweetAlert2 --}}

    <x-card title="Daftar Pegawai" icon="fas fa-users">

        <x-slot name="tools">
            @can('guru.create')
            <a href="{{ route('pegawaimanager.import') }}" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm mr-2 btn-animate font-weight-bold text-dark" title="Import data masal dari CSV">
                <i class="fas fa-cloud-upload-alt mr-1"></i> Import CSV
            </a>
            @endcan

            @can('guru.view')
            <a href="{{ route('pegawaimanager.export') }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm mr-2 btn-animate" title="Unduh data dalam format CSV/Excel">
                <i class="fas fa-file-excel mr-1"></i> Export Data
            </a>
            @endcan
            
            @can('guru.create')
            <a href="{{ route('pegawaimanager.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm btn-animate gradient-primary border-0">
                <i class="fas fa-plus mr-1"></i> Tambah Pegawai
            </a>
            @endcan
        </x-slot>

        {{-- Filter & Search Section: Glassmorphism Layout --}}
        <div class="p-4 mb-4 glass-card">
            <form action="{{ route('pegawaimanager.index') }}" method="GET">
                <div class="row align-items-end">
                    
                    {{-- Pencarian --}}
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group mb-0">
                            <label class="text-xs font-weight-bold ml-1 text-muted"><i class="fas fa-search mr-1"></i> Cari Nama / Email</label>
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Ketik kata kunci pencarian..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    {{-- Tipe --}}
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group mb-0">
                            <label class="text-xs font-weight-bold ml-1 text-muted"><i class="fas fa-tag mr-1"></i> Tipe Pegawai</label>
                            <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Semua Kategori --</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->nama_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group mb-0">
                            <label class="text-xs font-weight-bold ml-1 text-muted"><i class="fas fa-shield-alt mr-1"></i> Role</label>
                            <select name="role" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Semua Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="col-lg-2 col-md-2 d-flex">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill mr-1 shadow-sm btn-animate gradient-primary border-0">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('pegawaimanager.index') }}" class="btn btn-default btn-sm shadow-sm btn-animate bg-white" title="Kembali ke awal">
                            <i class="fas fa-sync-alt text-muted"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <div class="table-responsive mt-2">

            <table class="table table-hover table-premium">

                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Identitas Pegawai</th>
                        <th class="text-center">Hak Akses Sistem</th>
                        <th class="text-center">Kategori / Tipe</th>
                        <th class="text-center">Status Akun</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pegawaiManagers as $index => $item)

                    <tr>

                        <td class="text-center">{{ $index + 1 }}</td>

                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $item->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($item->nama).'&background=0D8ABC&color=fff&size=40' }}" 
                                     class="img-circle elevation-1 mr-3" 
                                     style="width: 45px; height: 45px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); object-fit: cover;">
                                <div>
                                    <div class="font-weight-bold text-dark mb-0">{{ $item->nama }}</div>
                                    <small class="text-muted"><i class="far fa-envelope mr-1"></i> {{ $item->email }}</small>
                                </div>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="badge badge-light border px-3 py-2 text-xs" style="border-radius: 20px; font-weight: 600;">
                                <i class="fas fa-shield-alt mr-1 text-success"></i> {{ $item->user->primary_role ?? 'Pegawai' }}
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="badge badge-info-soft px-3 py-1 text-primary" style="font-weight: 600; border-radius: 8px; background-color: #e3f2fd;">
                                {{ $item->typePegawai->nama_type ?? '-' }}
                            </span>
                        </td>

                        <td class="text-center">
                            @php $status = $item->user->account_status ?? 'inactive'; @endphp
                            @if($status === 'active')
                                <span class="badge px-3 py-2" style="background: rgba(40,167,69,0.1); color: #1e7e34; border-radius: 20px; font-weight: 600;" title="Akun aktif dan dapat login">
                                    <i class="fas fa-circle mr-1" style="font-size: 0.45rem; vertical-align: middle;"></i> Aktif
                                </span>
                            @else
                                <span class="badge px-3 py-2" style="background: rgba(220,53,69,0.1); color: #c82333; border-radius: 20px; font-weight: 600;" title="Akun nonaktif, tidak dapat login">
                                    <i class="fas fa-circle mr-1" style="font-size: 0.45rem; vertical-align: middle;"></i> Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="text-center py-3">
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('pegawaimanager.show', $item->id) }}"
                                   class="btn btn-outline-primary btn-sm mx-1 shadow-sm px-2 btn-animate rounded-circle"
                                   style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                   title="Lihat Detail Profil">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @can('guru.edit')
                                <a href="{{ route('pegawaimanager.edit', $item->id) }}"
                                   class="btn btn-outline-info btn-sm mx-1 shadow-sm px-2 btn-animate rounded-circle"
                                   style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                

                                @if(auth()->user()->hasRole(['SUPER_ADMIN', 'STAF_TU']))
                                <form action="{{ route('pegawaimanager.destroy', $item->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm mx-1 shadow-sm px-2 btn-delete btn-animate rounded-circle"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                            title="Hapus Data">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="6" class="text-center text-muted py-5 bg-white">
                            <div class="mb-3">
                                <i class="fas fa-search-minus fa-4x text-gray-300"></i>
                            </div>
                            @if(request()->anyFilled(['search', 'type', 'role']))
                                <h5 class="font-weight-bold mb-1">Hasil Pencarian Tidak Ditemukan</h5>
                                <p class="small">Coba gunakan kata kunci lain atau bersihkan filter Anda.</p>
                                <a href="{{ route('pegawaimanager.index') }}" class="btn btn-primary btn-sm mt-2 rounded-pill px-4">Bersihkan Filter</a>
                            @else
                                <h5 class="font-weight-bold mb-1">Daftar Pegawai Masih Kosong</h5>
                                <p class="small">Silakan tambahkan data pegawai pertama Anda untuk memulai.</p>
                                @can('guru.create')
                                <a href="{{ route('pegawaimanager.create') }}" class="btn btn-primary btn-sm mt-2 rounded-pill px-4">Tambah Pegawai</a>
                                @endcan
                            @endif
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>

        </div>

        {{-- Pagination Links --}}
        @if($pegawaiManagers->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center py-3">
                <div class="text-muted small font-italic">
                    Menampilkan <strong>{{ $pegawaiManagers->firstItem() }}</strong> sampai <strong>{{ $pegawaiManagers->lastItem() }}</strong> 
                    dari <strong>{{ $pegawaiManagers->total() }}</strong> total data pegawai.
                </div>
                <div class="pagination-sm">
                    {{ $pegawaiManagers->links() }}
                </div>
            </div>
        @endif

    </x-card>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    });
</script>
@endpush
