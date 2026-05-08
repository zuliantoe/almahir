@extends('layouts.app')

@section('title', 'Edit Uang Saku')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Uang Saku</h1>
        <a href="{{ route('keuangan.uangsakus.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2 d-none d-sm-inline-block"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4 rounded-xl overflow-hidden">
                <div class="card-header py-3 bg-white border-bottom border-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i> Form Edit Uang Saku</h6>
                </div>
                <div class="card-body">
                    <form id="uangSakuForm" action="{{ route('keuangan.uangsakus.update', $uangsaku->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Santri -->
                            <div class="col-md-6 mb-4">
                                <label for="siswa_id" class="small font-weight-bold text-muted mb-2">Santri <span class="text-danger">*</span></label>
                                <select name="siswa_id" id="siswa_id" class="form-control select2 bg-light border-0 shadow-sm rounded-lg @error('siswa_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Santri --</option>
                                    @foreach ($siswas as $siswa)
                                        <option value="{{ $siswa->id }}" {{ old('siswa_id', $uangsaku->siswa_id) == $siswa->id ? 'selected' : '' }}>
                                            {{ $siswa->nama }} ({{ $siswa->nis }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('siswa_id')
                                    <div class="invalid-feedback ml-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nominal -->
                            <div class="col-md-6 mb-4">
                                <label for="jumlah" class="small font-weight-bold text-muted mb-2">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0 shadow-sm" style="border-radius: 10px 0 0 10px; font-weight: bold;">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control bg-light border-0 shadow-sm amount-input @error('jumlah') is-invalid @enderror" 
                                           style="border-radius: 0 10px 10px 0;"
                                           id="jumlah" 
                                           name="jumlah" 
                                           placeholder="Masukkan nominal" 
                                           value="{{ old('jumlah', $uangsaku->jumlah) }}" 
                                           inputmode="numeric"
                                           required>
                                    @error('jumlah')
                                        <div class="invalid-feedback ml-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6 mb-4">
                                <label for="tanggal" class="small font-weight-bold text-muted mb-2">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control bg-light border-0 shadow-sm rounded-lg @error('tanggal') is-invalid @enderror" 
                                       id="tanggal" 
                                       name="tanggal" 
                                       value="{{ old('tanggal', $uangsaku->tanggal->format('Y-m-d')) }}" 
                                       required>
                                @error('tanggal')
                                    <div class="invalid-feedback ml-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-4">
                                <label for="status" class="small font-weight-bold text-muted mb-2">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control bg-light border-0 shadow-sm rounded-lg @error('status') is-invalid @enderror" required>
                                    <option value="Belum Diterima" {{ old('status', $uangsaku->status) == 'Belum Diterima' ? 'selected' : '' }}>Belum Diterima</option>
                                    <option value="Diterima Bendahara" {{ old('status', $uangsaku->status) == 'Diterima Bendahara' ? 'selected' : '' }}>Diterima Bendahara</option>
                                    <option value="Sudah Diterima Santri" {{ old('status', $uangsaku->status) == 'Sudah Diterima Santri' ? 'selected' : '' }}>Sudah Diterima Santri</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback ml-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12 mb-4">
                                <label for="deskripsi" class="small font-weight-bold text-muted mb-2">Deskripsi <span class="text-muted font-weight-normal">(Opsional)</span></label>
                                <textarea name="deskripsi" 
                                          id="deskripsi" 
                                          class="form-control bg-light border-0 shadow-sm rounded-lg @error('deskripsi') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Tambahkan deskripsi uang saku">{{ old('deskripsi', $uangsaku->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback ml-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm text-white">
                                <i class="fas fa-save mr-2"></i> Perbarui Uang Saku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    .rounded-xl { border-radius: 12px; }
    .rounded-lg { border-radius: 10px; }
    .bg-light { background-color: #f8f9fc !important; }
    .select2-container--bootstrap4 .select2-selection {
        background-color: #f8f9fc !important;
        border: none !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        border-radius: 10px !important;
        height: calc(1.5em + 0.75rem + 2px) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: "-- Pilih Santri --",
        allowClear: true
    });

    const amountInput = document.querySelector(".amount-input");

    function formatNominal(value) {
        let cleaned = String(value).replace(/\D/g, "");
        return cleaned !== "" ? new Intl.NumberFormat("id-ID").format(cleaned) : "";
    }

    if (amountInput.value !== "") {
        amountInput.value = formatNominal(amountInput.value);
    }

    amountInput.addEventListener("input", function () {
        this.value = formatNominal(this.value);
    });

    document.getElementById("uangSakuForm").addEventListener("submit", function () {
        const cleaned = amountInput.value.replace(/\./g, "");
        amountInput.value = cleaned;
    });
});
</script>
@endpush
