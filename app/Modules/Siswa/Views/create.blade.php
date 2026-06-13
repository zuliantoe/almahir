@extends('layouts.app')

@section('title', $title ?? 'Tambah Siswa')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title ?? 'Tambah Siswa Baru' }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('siswa.index') }}">Siswa</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pendaftaran_id" id="hidden_pendaftaran_id" value="">

        @if(isset($pendaftaranDiterima) && $pendaftaranDiterima->count() > 0)
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card card-outline card-success mb-0">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-magic"></i> Isi Otomatis dari Data Pendaftaran</h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="form-group mb-0">
                            <select id="auto_fill_pendaftaran" class="form-control select2">
                                <option value="">-- Pilih Calon Siswa (Status: Diterima) --</option>
                                @foreach($pendaftaranDiterima as $p)
                                    <option value="{{ $p->id }}"
                                        data-nama="{{ $p->nama_lengkap }}"
                                        data-nis="{{ $p->nisn }}"
                                        data-tempat_lahir="{{ $p->tempat_lahir }}"
                                        data-tanggal_lahir="{{ $p->tanggal_lahir }}"
                                        data-jenis_kelamin="{{ $p->jenis_kelamin }}"
                                        data-telepon="{{ $p->no_hp_ayah }}"
                                        data-alamat="{{ $p->alamat }}"
                                    >
                                        {{ $p->nama_lengkap }} (NISN: {{ $p->nisn }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">Memilih data di atas akan otomatis mengisi form di bawah ini sesuai data pendaftaran.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            {{-- Left Column: Personal Info --}}
            <div class="col-md-8">
                <x-card title="Informasi Pribadi" type="primary">
                    <div class="row">
                        <div class="col-md-6">
                            <x-input
                                label="NIS (Nomor Induk Siswa)"
                                name="nis"
                                placeholder="Masukkan NIS"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-input
                                label="Nama Lengkap"
                                name="nama"
                                placeholder="Masukkan nama lengkap"
                                required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input
                                label="Email User"
                                name="email"
                                type="email"
                                placeholder="siswa@email.com"
                                required />
                        </div>
                        <div class="col-md-6">
                            <x-input
                                label="Tanggal Lahir"
                                name="tanggal_lahir"
                                type="date"
                                required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input
                                label="Tempat Lahir"
                                name="tempat_lahir"
                                placeholder="Kota kelahiran"
                                required />
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <x-input
                        label="No. Telepon"
                        name="telepon"
                        placeholder="08xxxxxxxxxx"
                        required />

                    <div class="form-group">
                        <label>Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                  rows="3" placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </x-card>
            </div>

            {{-- Right Column: Academic Info --}}
            <div class="col-md-4">
                <x-card title="Informasi Akademik" type="info">


                    <div class="form-group">
                        <label>Tahun Masuk (Tahun Ajaran) <span class="text-danger">*</span></label>
                        <select name="tahun_masuk" class="form-control @error('tahun_masuk') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @if(isset($tahunAjaran))
                                @foreach($tahunAjaran as $ta)
                                    <option value="{{ $ta->tahunajaran }}" {{ old('tahun_masuk') == $ta->tahunajaran ? 'selected' : '' }}>
                                        {{ $ta->tahunajaran }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('tahun_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Foto Siswa</label>
                        <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" name="foto" id="foto" accept="image/*" onchange="previewImage(this)">
                            <label class="custom-file-label" for="foto">Pilih file...</label>
                        </div>
                        <div class="mb-2 text-center" id="preview-container">
                            <img id="foto-preview" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23cbd5e1%22%20stroke-width%3D%221%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Ccircle%20cx%3D%2212%22%20cy%3D%228%22%20r%3D%225%22%2F%3E%3Cpath%20d%3D%22M20%2021a8%208%200%200%200-16%200%22%2F%3E%3C%2Fsvg%3E" alt="Preview Foto" class="img-thumbnail" style="max-height: 200px; width: 150px; object-fit: cover; background-color: #f8f9fa;">
                        </div>
                        <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                    </div>
                </x-card>

                {{-- Action Buttons --}}
                <x-card>
                    <div class="d-grid gap-2">
                        <x-btn type="submit" class="btn-primary btn-block" icon="fas fa-save">
                            Simpan Data
                        </x-btn>
                        <x-btn class="btn-secondary btn-block" icon="fas fa-arrow-left" href="{{ route('siswa.index') }}">
                            Kembali
                        </x-btn>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Custom file input label update
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Pilih file...';
        this.nextElementSibling.innerText = fileName;
    });

    // Image preview function
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('foto-preview').src = e.target.result;
                document.getElementById('preview-container').classList.remove('d-none');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto-fill form from Pendaftaran
    $(document).ready(function() {
        $('#auto_fill_pendaftaran').on('change', function() {
            var selected = $(this).find(':selected');
            if (selected.val()) {
                // Update inputs
                $('input[name="nama"]').val(selected.data('nama') || '');
                $('input[name="nis"]').val(selected.data('nis') || '');
                $('input[name="tempat_lahir"]').val(selected.data('tempat_lahir') || '');
                $('input[name="tanggal_lahir"]').val(selected.data('tanggal_lahir') || '');
                $('input[name="telepon"]').val(selected.data('telepon') || '');
                $('textarea[name="alamat"]').val(selected.data('alamat') || '');

                // Update select
                var jkSelect = $('select[name="jenis_kelamin"]');
                jkSelect.val(selected.data('jenis_kelamin') || '').trigger('change');

                // Add or update hidden input for pendaftaran_id
                $('#hidden_pendaftaran_id').val(selected.val());

                // Visual feedback
                Swal.fire({
                    icon: 'success',
                    title: 'Data Terisi!',
                    text: 'Form berhasil diisi menggunakan data pendaftaran.',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                $('#hidden_pendaftaran_id').val('');
            }
        });
    });
</script>
@endpush
