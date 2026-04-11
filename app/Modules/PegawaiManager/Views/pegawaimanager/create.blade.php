@extends('layouts.app')

@section('title', $title)

@section('content')

    <div class="container-fluid">

        <x-card title="Tambah Pegawai" icon="fas fa-user-plus">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pegawaimanager.store') }}" method="POST">

                @csrf

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                        placeholder="Masukkan nama pegawai" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                        placeholder="Masukkan email">
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}"
                        placeholder="Masukkan nomor HP">
                </div>
                <div class="form-group">
                    <label>User</label>

                    <select name="user_id" class="form-control" required>
                        <option value="">Pilih User</option>

                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group">
                    <label>Type Pegawai</label>

                    <select name="type_pegawai_id" class="form-control" required>

                        <option value="">Pilih Type Pegawai</option>

                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" {{ old('type_pegawai_id') == $type->id ? 'selected' : '' }}>

                                {{ $type->nama_type }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}">
                </div>

                <div class="mt-3">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>

                    <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>

                </div>

            </form>

        </x-card>

    </div>

@endsection
