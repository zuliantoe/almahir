@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pengeluaran</h1>
        <a href="{{ route('keuangan.pengeluarans.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2 d-none d-sm-inline-block"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4 rounded-xl overflow-hidden">
                <div class="card-header py-3 bg-white border-bottom border-light">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-edit mr-2"></i> Form Edit Pengeluaran</h6>
                </div>
                <div class="card-body">
                    <form id="expenseForm" action="{{ route('keuangan.pengeluarans.update', $pengeluaran->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Tujuan Pengeluaran -->
                             <div class="col-md-6 mb-4">
                                <label for="tujuan_id" class="small font-weight-bold text-muted mb-2">
                                    Tujuan Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <select name="tujuan_id" id="tujuan_id" class="form-control bg-light border-0 shadow-sm rounded-lg @error('tujuan_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tujuan Pengeluaran --</option>
                                    @foreach ($tujuans as $tujuan)
                                        <option value="{{ $tujuan->id }}" {{ (old('tujuan_id') ?? $pengeluaran->tujuan_id) == $tujuan->id ? 'selected' : '' }}>
                                            {{ $tujuan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tujuan_id')
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
                                           value="{{ old('jumlah', number_format($pengeluaran->jumlah, 0, '', '')) }}" 
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
                                       value="{{ old('tanggal', \Carbon\Carbon::parse($pengeluaran->tanggal)->format('Y-m-d')) }}" 
                                       required>
                                @error('tanggal')
                                    <div class="invalid-feedback ml-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                             <div class="col-md-6 mb-4">
                                <label for="deskripsi" class="small font-weight-bold text-muted mb-2">
                                    Deskripsi <span class="text-muted font-weight-normal">(Opsional)</span>
                                </label>
                                <textarea name="deskripsi" 
                                          id="deskripsi" 
                                          class="form-control bg-light border-0 shadow-sm rounded-lg @error('deskripsi') is-invalid @enderror" 
                                          rows="2"
                                          placeholder="Tambahkan deskripsi pengeluaran">{{ old('deskripsi', $pengeluaran->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback ml-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-danger px-5 shadow-sm">
                                <i class="fas fa-save mr-2"></i> Perbarui Pengeluaran
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
<style>
    .rounded-xl { border-radius: 12px; }
    .rounded-lg { border-radius: 10px; }
    .bg-light { background-color: #f8f9fc !important; }
    .form-control:focus {
        background-color: #ffffff !important;
        border: 1px solid #e74a3b !important;
        box-shadow: 0 0 0 0.2rem rgba(231, 74, 59, 0.25) !important;
    }
    .input-group-text.bg-light {
        border-right: 1px solid #e3e6f0 !important;
    }
    
    .no-arrow {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: none !important;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const amountInput = document.querySelector(".amount-input");

    // Fungsi untuk memformat angka dengan pemisah ribuan
    function formatNominal(value) {
        let cleaned = value.toString().replace(/\D/g, "");
        return cleaned !== "" ? new Intl.NumberFormat("id-ID").format(cleaned) : "";
    }

    // Format value pada saat inisiasi view
    if (amountInput.value !== "") {
        amountInput.value = formatNominal(amountInput.value);
    }

    amountInput.addEventListener("input", function () {
        this.value = formatNominal(this.value);
    });

    // Blokir tanda minus "-" dan huruf "e" (scientific notation)
    amountInput.addEventListener("keydown", function (e) {
        if (e.key === "-" || e.key === "e") {
            e.preventDefault();
        }
    });

    // Sebelum submit → ubah ke angka murni tanpa titik agar tersimpan di database
    document.getElementById("expenseForm").addEventListener("submit", function () {
        const cleaned = amountInput.value.replace(/\./g, "");
        amountInput.value = cleaned;
    });
});
</script>
@endpush
