@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pencatatan Otomatis</h1>
        <a href="{{ route('keuangan.pencatatanotomatis.index') }}" class="btn btn-secondary shadow-sm rounded-lg">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2 d-none d-sm-inline-block"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-lg">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4 rounded-xl overflow-hidden">
        <div class="card-header py-3 bg-white border-bottom border-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-2"></i> Form Edit Pencatatan</h6>
        </div>
        <div class="card-body">
            <form id="otomatisForm" action="{{ route('keuangan.pencatatanotomatis.update', $pencatatan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="tipe" class="small font-weight-bold text-muted mb-2">Tipe Transaksi <span class="text-danger">*</span></label>
                        <select name="tipe" id="tipe" class="form-control bg-light border-0 shadow-sm rounded-lg @error('tipe') is-invalid @enderror" required>
                            <option value="pemasukan" {{ old('tipe', $pencatatan->tipe) == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ old('tipe', $pencatatan->tipe) == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4" id="sumber_wrapper" style="display: none;">
                        <label for="sumber_id" class="small font-weight-bold text-muted mb-2">Sumber Pemasukan <span class="text-danger">*</span></label>
                        <select name="sumber_id" id="sumber_id" class="form-control bg-light border-0 shadow-sm rounded-lg @error('sumber_id') is-invalid @enderror">
                            <option value="">-- Pilih Sumber Pemasukan --</option>
                            @foreach($sumbers as $sumber)
                                <option value="{{ $sumber->id }}" {{ old('sumber_id', $pencatatan->sumber_id) == $sumber->id ? 'selected' : '' }}>{{ $sumber->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-4" id="tujuan_wrapper" style="display: none;">
                        <label for="tujuan_id" class="small font-weight-bold text-muted mb-2">Tujuan Pengeluaran <span class="text-danger">*</span></label>
                        <select name="tujuan_id" id="tujuan_id" class="form-control bg-light border-0 shadow-sm rounded-lg @error('tujuan_id') is-invalid @enderror">
                            <option value="">-- Pilih Tujuan Pengeluaran --</option>
                            @foreach($tujuans as $tujuan)
                                <option value="{{ $tujuan->id }}" {{ old('tujuan_id', $pencatatan->tujuan_id) == $tujuan->id ? 'selected' : '' }}>{{ $tujuan->nama }}</option>
                            @endforeach
                        </select>
                    </div>

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
                                   value="{{ old('jumlah', $pencatatan->jumlah) }}" 
                                   inputmode="numeric"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="frekuensi" class="small font-weight-bold text-muted mb-2">Frekuensi <span class="text-danger">*</span></label>
                        <select name="frekuensi" id="frekuensi" class="form-control bg-light border-0 shadow-sm rounded-lg @error('frekuensi') is-invalid @enderror" required>
                            <option value="sekali" {{ old('frekuensi', $pencatatan->frekuensi) == 'sekali' ? 'selected' : '' }}>Sekali Saja</option>
                            <option value="harian" {{ old('frekuensi', $pencatatan->frekuensi) == 'harian' ? 'selected' : '' }}>Setiap Hari</option>
                            <option value="bulanan" {{ old('frekuensi', $pencatatan->frekuensi) == 'bulanan' ? 'selected' : '' }}>Setiap Bulan</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="tanggal_mulai" class="small font-weight-bold text-muted mb-2">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal_mulai" 
                               id="tanggal_mulai" 
                               class="form-control bg-light border-0 shadow-sm rounded-lg @error('tanggal_mulai') is-invalid @enderror" 
                               value="{{ old('tanggal_mulai', $pencatatan->tanggal_mulai->format('Y-m-d')) }}" 
                               required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="waktu_eksekusi" class="small font-weight-bold text-muted mb-2">Jam Eksekusi (WIB) <span class="text-danger">*</span></label>
                        <input type="time" 
                               name="waktu_eksekusi" 
                               id="waktu_eksekusi" 
                               class="form-control bg-light border-0 shadow-sm rounded-lg @error('waktu_eksekusi') is-invalid @enderror" 
                               value="{{ old('waktu_eksekusi', \Carbon\Carbon::parse($pencatatan->waktu_eksekusi)->format('H:i')) }}" 
                               required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="is_active" class="small font-weight-bold text-muted mb-2">Status Aktif <span class="text-danger">*</span></label>
                        <select name="is_active" id="is_active" class="form-control bg-light border-0 shadow-sm rounded-lg" required>
                            <option value="1" {{ old('is_active', $pencatatan->is_active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $pencatatan->is_active) ? '' : 'selected' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="deskripsi" class="small font-weight-bold text-muted mb-2">Deskripsi <span class="text-muted font-weight-normal">(Opsional)</span></label>
                        <textarea name="deskripsi" 
                                  id="deskripsi" 
                                  class="form-control bg-light border-0 shadow-sm rounded-lg @error('deskripsi') is-invalid @enderror" 
                                  rows="3"
                                  placeholder="Tambahkan deskripsi pencatatan otomatis">{{ old('deskripsi', $pencatatan->deskripsi) }}</textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-light mr-3 px-4 rounded-lg">Reset</button>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-lg">
                        <i class="fas fa-save mr-2"></i> Perbarui Pencatatan Otomatis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .rounded-xl { border-radius: 12px; }
    .rounded-lg { border-radius: 10px; }
    .bg-light { background-color: #f8f9fc !important; }
    .form-control:focus {
        background-color: #ffffff !important;
        border: 1px solid #4e73df !important;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
    }
    .input-group-text.bg-light {
        border-right: 1px solid #e3e6f0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipeSelect = document.getElementById('tipe');
        const sumberWrapper = document.getElementById('sumber_wrapper');
        const tujuanWrapper = document.getElementById('tujuan_wrapper');
        const sumberSelect = document.getElementById('sumber_id');
        const tujuanSelect = document.getElementById('tujuan_id');
        const amountInput = document.querySelector(".amount-input");
        const form = document.getElementById("otomatisForm");

        // Toggle required & visibility of Sumber/Tujuan
        function toggleKategori() {
            if (tipeSelect.value === 'pemasukan') {
                sumberWrapper.style.display = 'block';
                tujuanWrapper.style.display = 'none';
                tujuanSelect.value = '';
                sumberSelect.setAttribute('required', 'required');
                tujuanSelect.removeAttribute('required');
            } else if (tipeSelect.value === 'pengeluaran') {
                sumberWrapper.style.display = 'none';
                tujuanWrapper.style.display = 'block';
                sumberSelect.value = '';
                tujuanSelect.setAttribute('required', 'required');
                sumberSelect.removeAttribute('required');
            } else {
                sumberWrapper.style.display = 'none';
                tujuanWrapper.style.display = 'none';
                sumberSelect.removeAttribute('required');
                tujuanSelect.removeAttribute('required');
            }
        }

        tipeSelect.addEventListener('change', toggleKategori);
        toggleKategori(); // Run on load in case of old input

        // Format Nominal to Thousands Separator (ID)
        function formatNominal(value) {
            let cleaned = value.replace(/\D/g, "");
            return cleaned !== "" ? new Intl.NumberFormat("id-ID").format(cleaned) : "";
        }

        // Format on load
        if (amountInput.value !== "") {
            // Strip .00 decimals if any
            let val = amountInput.value.split('.')[0];
            amountInput.value = formatNominal(val);
        }

        amountInput.addEventListener("input", function () {
            this.value = formatNominal(this.value);
        });

        // Block minus sign and "e"
        amountInput.addEventListener("keydown", function (e) {
            if (e.key === "-" || e.key === "e") {
                e.preventDefault();
            }
        });

        // Clean formatting dots before submitting form
        form.addEventListener("submit", function () {
            const cleaned = amountInput.value.replace(/\./g, "");
            amountInput.value = cleaned;
        });
    });
</script>
@endpush
@endsection
