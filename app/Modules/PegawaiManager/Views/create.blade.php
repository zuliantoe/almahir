@extends('layouts.app')

@section('title', $title)

@section('content')

    <div class="container-fluid">

        <x-card title="Registrasi Pegawai Baru" icon="fas fa-user-plus">

            <form action="{{ route('pegawaimanager.store') }}" method="POST">

                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                                placeholder="Masukkan nama lengkap beserta gelar" required>
                            <small class="form-text text-muted">Gunakan format Nama Lengkap + Gelar (contoh: Budi Santoso, S.Pd).</small>
                        </div>

                        <div class="form-group">
                            <label>Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="nama@sekolah.com" required>
                            <small class="form-text text-muted">Email ini akan digunakan sebagai ID login aplikasi.</small>
                        </div>

                        <div class="form-group">
                            <label>Nomor Telepon/HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}"
                                placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Hak Akses Sistem (Role) <span class="text-danger">*</span></label>
                            <select name="role_name" class="form-control" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role_name', 'PEGAWAI') == $role->name ? 'selected' : '' }}>
                                        {{ $role->display_name }} ({{ $role->name }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Menentukan menu dan fitur yang bisa diakses oleh pegawai ini.</small>
                        </div>

                        <div class="form-group">
                            <label>Kategori/Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="type_pegawai_id" class="form-control" required>
                                <option value="">-- Pilih Tipe Pegawai --</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" {{ old('type_pegawai_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->nama_type }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Klasifikasi posisi sesuai struktur kepegawaian sekolah.</small>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Mulai Tugas (TMT)</label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Alamat Domisili</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap tempat tinggal saat ini">{{ old('alamat') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 py-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    <small>Sistem akan membuatkan akun dengan password default: <strong>password123</strong></small>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Data Pegawai
                    </button>
                    <a href="{{ route('pegawaimanager.index') }}" class="btn btn-link text-secondary">
                        Batal
                    </a>
                </div>

            </form>

        </x-card>

    </div>

@endsection
