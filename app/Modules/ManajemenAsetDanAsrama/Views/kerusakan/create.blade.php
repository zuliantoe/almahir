@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Manajemen Aset & Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}">Kerusakan</a></li>
            <li class="breadcrumb-item active">Lapor</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Form Lapor Kerusakan Aset" icon="fas fa-exclamation-triangle">
                <form action="{{ route('manajemenasetdanasrama.kerusakan.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="aset_id">Pilih Aset <span class="text-danger">*</span></label>
                                <select class="form-control @error('aset_id') is-invalid @enderror" id="aset_id" name="aset_id" required>
                                    <option value="" data-tanggal="">-- Pilih Aset --</option>
                                    @foreach($aset as $a)
                                        <option value="{{ $a->id }}" data-tanggal="{{ $a->tanggal_pengadaan ? \Carbon\Carbon::parse($a->tanggal_pengadaan)->format('Y-m-d') : '' }}" {{ old('aset_id', request('aset_id')) == $a->id ? 'selected' : '' }}>
                                            [{{ $a->kode_aset }}] {{ $a->nama_aset }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('aset_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_kerusakan">Tanggal Kerusakan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_kerusakan') is-invalid @enderror" id="tanggal_kerusakan" name="tanggal_kerusakan" value="{{ old('tanggal_kerusakan', date('Y-m-d')) }}" required>
                                <small class="text-muted" id="peringatan_tanggal" style="display:none;"><i class="fas fa-info-circle"></i> Tanggal tidak boleh sebelum aset diadakan.</small>
                                @error('tanggal_kerusakan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tingkat_kerusakan">Tingkat Kerusakan <span class="text-danger">*</span></label>
                                <select class="form-control @error('tingkat_kerusakan') is-invalid @enderror" id="tingkat_kerusakan" name="tingkat_kerusakan" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="ringan" {{ old('tingkat_kerusakan') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                                    <option value="sedang" {{ old('tingkat_kerusakan') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="berat" {{ old('tingkat_kerusakan') == 'berat' ? 'selected' : '' }}>Berat</option>
                                </select>
                                @error('tingkat_kerusakan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_penanganan">Status Penanganan <span class="text-danger">*</span></label>
                                <select class="form-control @error('status_penanganan') is-invalid @enderror" id="status_penanganan" name="status_penanganan" required>
                                    <option value="belum_ditangani" {{ old('status_penanganan', 'belum_ditangani') == 'belum_ditangani' ? 'selected' : '' }}>Belum Ditangani</option>
                                    <option value="sedang_ditangani" {{ old('status_penanganan') == 'sedang_ditangani' ? 'selected' : '' }}>Sedang Ditangani</option>
                                    <option value="selesai" {{ old('status_penanganan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                @error('status_penanganan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_kerusakan">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi_kerusakan') is-invalid @enderror" id="deskripsi_kerusakan" name="deskripsi_kerusakan" rows="4" required>{{ old('deskripsi_kerusakan') }}</textarea>
                        @error('deskripsi_kerusakan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Tambahan</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Laporan
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.kerusakan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function checkDateConstraints() {
            var selectedOption = $('#aset_id').find('option:selected');
            var minDate = selectedOption.data('tanggal');
            var targetInput = $('#tanggal_kerusakan');
            
            if (minDate) {
                targetInput.attr('min', minDate);
                $('#peringatan_tanggal').show().text('Harus setelah ' + minDate.split('-').reverse().join('/'));
                
                // Cek apabila tanggal yg sedang diisi itu invalid
                if (targetInput.val() && targetInput.val() < minDate) {
                    targetInput.val(minDate);
                }
            } else {
                targetInput.removeAttr('min');
                $('#peringatan_tanggal').hide();
            }
        }

        $('#aset_id').on('change', checkDateConstraints);
        
        // Panggil saat page load, misal pas redirect atau prepopulate
        if ($('#aset_id').val()) {
            checkDateConstraints();
        }
    });
</script>
@endpush
