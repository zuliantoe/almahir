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
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt text-primary"></i></span>
                        </div>
                        <input type="text" name="tanggal_masuk" class="form-control datepicker" value="{{ old('tanggal_masuk') }}" placeholder="dd/mm/yyyy">
                    </div>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
        border: 1px solid #e1e5ef !important;
        font-family: 'Outfit', sans-serif !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".datepicker", {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            locale: "id",
            allowInput: true
        });
    });
</script>
@endpush

