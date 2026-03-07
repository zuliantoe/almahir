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
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Type Pegawai</th>
                        <th>Tanggal Masuk</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pegawaiManagers as $index => $item)

                    <tr>

                        <td>{{ $index + 1 }}</td>

                        <td>
                            {{ $item->user->name ?? $item->nama ?? '-' }}
                        </td>

                        <td>
                            {{ $item->email ?? '-' }}
                        </td>

                        <td>
                            {{ $item->no_hp ?? '-' }}
                        </td>

                        <td>
                            {{ $item->typePegawai->nama ?? '-' }}
                        </td>

                        <td>
                            {{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d-m-Y') : '-' }}
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
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus data ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger"
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
