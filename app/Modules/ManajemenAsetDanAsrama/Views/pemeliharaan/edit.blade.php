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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.pemeliharaan.index') }}">Pemeliharaan</a></li>
            <li class="breadcrumb-item active">Edit</li>
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
            <x-card title="Edit Pemeliharaan Aset" icon="fas fa-edit">
                <form action="{{ route('manajemenasetdanasrama.pemeliharaan.update', $pemeliharaan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="aset_id">Pilih Aset <span class="text-danger">*</span></label>
                                <select class="form-control @error('aset_id') is-invalid @enderror" id="aset_id" name="aset_id" required>
                                    <option value="" data-tanggal="">-- Pilih Aset --</option>
                                    @foreach($aset as $a)
                                        <option value="{{ $a->id }}" data-tanggal="{{ $a->tanggal_pengadaan ? \Carbon\Carbon::parse($a->tanggal_pengadaan)->format('Y-m-d') : '' }}" {{ old('aset_id', $pemeliharaan->aset_id) == $a->id ? 'selected' : '' }}>
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
                                <label for="tanggal_pemeliharaan">Tanggal Pemeliharaan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_pemeliharaan') is-invalid @enderror" id="tanggal_pemeliharaan" name="tanggal_pemeliharaan" value="{{ old('tanggal_pemeliharaan', $pemeliharaan->tanggal_pemeliharaan ? $pemeliharaan->tanggal_pemeliharaan->format('Y-m-d') : ($pemeliharaan->tanggal_mulai_pemeliharaan ? $pemeliharaan->tanggal_mulai_pemeliharaan->format('Y-m-d') : date('Y-m-d'))) }}" required>
                                <small class="text-muted" id="peringatan_tanggal" style="display:none;"><i class="fas fa-info-circle"></i> Tanggal tidak boleh sebelum aset diadakan.</small>
                                @error('tanggal_pemeliharaan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_pemeliharaan">Deskripsi Pemeliharaan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi_pemeliharaan') is-invalid @enderror" id="deskripsi_pemeliharaan" name="deskripsi_pemeliharaan" rows="4" required>{{ old('deskripsi_pemeliharaan', $pemeliharaan->deskripsi_pemeliharaan) }}</textarea>
                        @error('deskripsi_pemeliharaan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="biaya">Biaya (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('biaya') is-invalid @enderror" id="biaya" name="biaya" value="{{ old('biaya', $pemeliharaan->biaya) }}" min="0" required>
                        @error('biaya')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan', $pemeliharaan->catatan) }}</textarea>
                        @error('catatan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                        <a href="{{ route('manajemenasetdanasrama.pemeliharaan.index') }}" class="btn btn-secondary">
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
            var targetInput = $('#tanggal_pemeliharaan');
            
            if (minDate) {
                targetInput.attr('min', minDate);
                $('#peringatan_tanggal').show().text('Harus setelah ' + minDate.split('-').reverse().join('/'));
                
                if (targetInput.val() && targetInput.val() < minDate) {
                    targetInput.val(minDate);
                }
            } else {
                targetInput.removeAttr('min');
                $('#peringatan_tanggal').hide();
            }
        }

        $('#aset_id').on('change', checkDateConstraints);
        if ($('#aset_id').val()) checkDateConstraints();
    });
</script>
@endpush
