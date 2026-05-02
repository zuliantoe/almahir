@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header gradient-primary border-0 p-4">
            <h3 class="card-title text-white font-weight-bold mb-0">
                <i class="fas fa-user-edit mr-2"></i> Perbarui Profil Pegawai
            </h3>
        </div>
        <div class="card-body p-4 bg-light">
            <div class="glass-card p-4">
                <form action="{{ route('pegawaimanager.update', $pegawaiManager->id) }}" method="POST">
                    @csrf
                    @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3"><i class="fas fa-id-card mr-2"></i>Informasi Akun & Akses</h5>
                    
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="nama" class="form-control form-control-premium" value="{{ old('nama', $pegawaiManager->nama) }}" 
                                placeholder="Nama lengkap beserta gelar..." required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="email" class="form-control form-control-premium" value="{{ old('email', $pegawaiManager->email) }}" 
                                placeholder="nama@sekolah.com" required>
                        </div>
                        <small class="text-muted">Email ini digunakan sebagai ID login ke sistem (Siakad).</small>
                    </div>

                    <div class="form-group">
                        <label>Hak Akses Sistem (Role) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                            </div>
                            <select name="role_name" class="form-control form-control-premium" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role_name', $pegawaiManager->user->getRoleNames()[0] ?? 'PEGAWAI') == $role->name ? 'selected' : '' }}>
                                        {{ $role->display_name }} ({{ $role->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">Menentukan fitur apa saja yang bisa dibuka oleh pegawai ini.</small>
                    </div>
                </div>

                <div class="col-md-6 border-left">
                    <h5 class="text-primary mb-3"><i class="fas fa-briefcase mr-2"></i>Data Pribadi & Tugas</h5>
                    
                    <div class="form-group">
                        <label>Kategori/Tipe Pegawai <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-tags"></i></span>
                            </div>
                            <select name="type_pegawai_id" class="form-control form-control-premium" required>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('type_pegawai_id', $pegawaiManager->type_pegawai_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->nama_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon/HP</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                            </div>
                            <input type="text" name="no_hp" class="form-control form-control-premium" value="{{ old('no_hp', $pegawaiManager->no_hp) }}"
                                placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Mulai Tugas (TMT)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" name="tanggal_masuk" class="form-control form-control-premium" value="{{ old('tanggal_masuk', $pegawaiManager->tanggal_masuk ? $pegawaiManager->tanggal_masuk->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <hr>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt mr-1"></i> Alamat Domisili Lengkap</label>
                        <textarea name="alamat" class="form-control form-control-premium" rows="3" placeholder="Masukkan alamat lengkap tempat tinggal saat ini...">{{ old('alamat', $pegawaiManager->alamat) }}</textarea>
                    </div>
                </div>
            </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm btn-animate gradient-primary border-0 rounded-pill">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary px-4 shadow-sm btn-animate rounded-pill ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
