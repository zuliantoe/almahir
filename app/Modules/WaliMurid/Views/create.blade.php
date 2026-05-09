@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Wali Murid" icon="fas fa-user-plus">
        @if(isset($pendaftaranDiterima) && $pendaftaranDiterima->count() > 0)
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card card-outline card-success mb-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header border-0 py-2">
                        <h3 class="card-title text-success font-weight-bold"><i class="fas fa-bolt mr-1"></i>Isi dari Pendaftaran</h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="form-group mb-0">
                            <select id="auto_fill_pendaftaran_wali" class="form-control select2">
                                <option value="">-- Pilih Calon Siswa (Ambil Data Orang Tua) --</option>
                                @foreach($pendaftaranDiterima as $p)
                                    <option value="{{ $p->id }}" 
                                        data-nama_ayah="{{ $p->nama_ayah }}"
                                        data-pekerjaan_ayah="{{ $p->pekerjaan_ayah }}"
                                        data-no_hp_ayah="{{ $p->no_hp_ayah }}"
                                        data-alamat_ayah="{{ $p->alamat_ayah }}"
                                        data-nama_ibu="{{ $p->nama_ibu }}"
                                        data-pekerjaan_ibu="{{ $p->pekerjaan_ibu }}"
                                        data-no_hp_ibu="{{ $p->no_hp_ibu }}"
                                        data-alamat_ibu="{{ $p->alamat_ibu }}"
                                        data-email="{{ $p->email }}"
                                        data-siswa_id="{{ $p->siswa->id ?? '' }}"
                                    >
                                        {{ $p->nama_lengkap }} (NISN: {{ $p->nisn }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block">Pilih siswa untuk mengisi data wali secara otomatis (Default: Ayah).</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('walimurid.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <x-input name="nama" label="Nama Lengkap" placeholder="Nama wali" :value="old('nama')" required />
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hubungan <span class="text-danger">*</span></label>
                        <select name="hubungan" class="form-control @error('hubungan') is-invalid @enderror" required>
                            <option value="ayah" {{ old('hubungan') == 'ayah' ? 'selected' : '' }}>Ayah</option>
                            <option value="ibu" {{ old('hubungan') == 'ibu' ? 'selected' : '' }}>Ibu</option>
                            <option value="wali" {{ old('hubungan') == 'wali' ? 'selected' : '' }}>Wali</option>
                        </select>
                        @error('hubungan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input type="email" name="email" label="Email" placeholder="email@example.com" :value="old('email')" />
                </div>
                <div class="col-md-6">
                    <x-input name="telepon" label="Telepon" placeholder="08xxxxxxxxxx" :value="old('telepon')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-input name="pekerjaan" label="Pekerjaan" placeholder="Wiraswasta, PNS, dll" :value="old('pekerjaan')" />
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pilih Siswa (Anak) <span class="text-danger">*</span></label>
                        <select name="siswa_ids[]" class="form-control select2 @error('siswa_ids') is-invalid @enderror" multiple="multiple" data-placeholder="Pilih satu atau lebih siswa" required>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}" {{ (is_array(old('siswa_ids')) && in_array($siswa->id, old('siswa_ids'))) ? 'selected' : '' }}>
                                    {{ $siswa->nama }} ({{ $siswa->nis }})
                                </option>
                            @endforeach
                        </select>
                        @error('siswa_ids')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('walimurid.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <x-btn type="submit" variant="primary" icon="fas fa-save">Simpan</x-btn>
            </div>
        </form>
    </x-card>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('#auto_fill_pendaftaran_wali').on('change', function() {
            var selected = $(this).find(':selected');
            if (selected.val()) {
                // Default ambil data Ayah sesuai permintaan
                $('input[name="nama"]').val(selected.data('nama_ayah') || '');
                $('select[name="hubungan"]').val('ayah').trigger('change');
                $('input[name="email"]').val(selected.data('email') || '');
                $('input[name="telepon"]').val(selected.data('no_hp_ayah') || '');
                $('input[name="pekerjaan"]').val(selected.data('pekerjaan_ayah') || '');
                $('textarea[name="alamat"]').val(selected.data('alamat_ayah') || '');

                // Auto-select student (anak)
                var siswaId = selected.data('siswa_id');
                if (siswaId) {
                    $('select[name="siswa_ids[]"]').val([siswaId]).trigger('change');
                }
                
                // Visual feedback
                Swal.fire({
                    icon: 'success',
                    title: ' Berhasil!',
                    text: 'Form diisi otomatis dengan data Ayah.',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    });
</script>
@endpush
@endsection
