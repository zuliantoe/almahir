@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark"><i class="fas fa-user-cog mr-2"></i> {{ $title }}</h1>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    {{-- Left Column: Avatar & Summary --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-body box-profile">
                <div class="text-center position-relative mb-4">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ $user->avatar_url }}"
                         alt="User profile picture"
                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #007bff; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    
                    {{-- Upload Form directly tied to Avatar --}}
                    <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="custom-file mb-2 text-left">
                            <input type="file" class="custom-file-input" id="avatar" name="avatar" required accept="image/*">
                            <label class="custom-file-label" for="avatar" data-browse="Pilih">Ubah Foto...</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fas fa-upload mr-1"></i> Unggah Foto</button>
                    </form>
                </div>

                <h3 class="profile-username text-center font-weight-bold">{{ $user->name }}</h3>
                <p class="text-muted text-center mb-4">{{ $user->primary_role ?? 'Pengguna' }}</p>

                @if($user->pegawai)
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Kategori Pegawai</b> <a class="float-right text-dark font-weight-bold">{{ $user->pegawai->typePegawai->nama_type ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Tanggal Masuk</b> <a class="float-right text-dark">{{ $user->pegawai->tanggal_masuk ? \Carbon\Carbon::parse($user->pegawai->tanggal_masuk)->format('d F Y') : '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Terakhir Login</b> <a class="float-right text-dark">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '-' }}</a>
                        </li>
                    </ul>
                @else
                    <div class="alert alert-warning text-sm">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Akun Anda belum terhubung dengan data Pegawai institusi.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Settings Tabs --}}
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active font-weight-bold" href="#biodata" data-toggle="tab"><i class="fas fa-address-card mr-1"></i> Bio Data</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold text-danger" href="#security" data-toggle="tab"><i class="fas fa-shield-alt mr-1"></i> Keamanan Sandi</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    {{-- Tab 1: Biodata --}}
                    <div class="active tab-pane" id="biodata">
                        <form class="form-horizontal" action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label">Nama Lengkap</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control bg-light" id="name" value="{{ $user->name }}" readonly disabled>
                                    <small class="text-muted">Hubungi administrator jika ada kesalahan ejaan nama.</small>
                                </div>
                            </div>
                            
                            <hr>

                            <div class="form-group row">
                                <label for="email" class="col-sm-3 col-form-label">Email Login</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="no_hp" class="col-sm-3 col-form-label">No WhatsApp</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->pegawai->no_hp ?? '') }}" placeholder="Contoh: 08123456789">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="alamat" class="col-sm-3 col-form-label">Alamat Domisili</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $user->pegawai->alamat ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tab 2: Security --}}
                    <div class="tab-pane" id="security">
                        <div class="alert alert-info bg-light text-dark border-info">
                            <i class="fas fa-info-circle text-info mr-1"></i> Sangat disarankan menggunakan kombinasi huruf dan angka minimal 8 karakter.
                        </div>
                        
                        <form class="form-horizontal" action="{{ route('password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group row">
                                <label for="current_password" class="col-sm-4 col-form-label">Sandi Saat Ini (Old)</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                            </div>
                            
                            <hr>

                            <div class="form-group row">
                                <label for="password" class="col-sm-4 col-form-label">Sandi Baru (New)</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="password_confirmation" class="col-sm-4 col-form-label">Konfirmasi Sandi Baru</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-sm-8 offset-sm-4">
                                    <button type="submit" class="btn btn-danger px-4"><i class="fas fa-key mr-1"></i> Ganti Sandi Akses</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Custom File Input JS for Bootstrap 4
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
