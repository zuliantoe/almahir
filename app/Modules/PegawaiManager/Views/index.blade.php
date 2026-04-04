@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    {{-- Alerts handled globally via SweetAlert2 --}}

    <x-card title="Daftar Pegawai" icon="fas fa-users">

        <x-slot name="tools">
            <a href="{{ route('pegawaimanager.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Pegawai
            </a>
        </x-slot>

        <div class="table-responsive">

            <table class="table table-hover table-striped">

                <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Pegawai & Peran</th>
                        <th>Informasi Kontak</th>
                        <th>Tipe Pegawai</th>
                        <th>Domisili</th>
                        <th>TMT</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pegawaiManagers as $index => $item)

                    <tr>

                        <td class="text-center">{{ $index + 1 }}</td>

                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $item->user->avatar_url ?? '' }}" class="img-circle elevation-2 mr-2" style="width: 40px; height: 40px; border: 2px solid #ddd; object-fit: cover;">
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $item->nama }}</div>
                                    <span class="badge badge-success text-xs" style="font-weight: 500; letter-spacing: 0.3px;">
                                        <i class="fas fa-shield-alt mr-1"></i> {{ $item->user->primary_role ?? 'Pegawai' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="text-sm">
                                <div class="mb-1">
                                    <i class="fas fa-envelope text-muted mr-1" style="width: 15px;"></i> {{ $item->email ?? '-' }}
                                </div>
                                <div>
                                    <i class="fas fa-phone-alt text-muted mr-1" style="width: 15px;"></i> {{ $item->no_hp ?? '-' }}
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge-info px-2 py-1" style="font-weight: 500;">
                                {{ $item->typePegawai->nama_type ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <small class="text-muted text-wrap d-block" style="max-width: 180px; line-height: 1.4;">
                                {{ $item->alamat ?? '-' }}
                            </small>
                        </td>

                        <td class="text-sm text-center">
                            <span class="text-muted">{{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '-' }}</span>
                        </td>

                        <td class="text-center">

                            <div class="btn-group btn-group-sm">

                                <a href="{{ route('pegawaimanager.edit', $item->id) }}"
                                   class="btn btn-info"
                                   title="Edit">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <form action="{{ route('pegawaimanager.destroy', $item->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                            class="btn btn-danger btn-delete"
                                            title="Hapus">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">

                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>

                            Belum ada data.
                            <a href="{{ route('pegawaimanager.create') }}">
                                Tambah data pertama
                            </a>.

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </x-card>

</div>
@endsection
