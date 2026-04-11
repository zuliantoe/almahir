@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('akademik.tahun-ajaran.store') }}" method="POST">
                @csrf

                <div class="card card-modern">
                    <div class="card-header bg-gradient-blue">
                        <h3 class="card-title text-white">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Tahun Ajaran
                        </h3>
                    </div>

                    <div class="card-body">
                        {{-- Informasi --}}
                        <div class="alert alert-info border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #f1f8ff; color: #0056b3;">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tahun ajaran yang aktif hanya boleh satu. Jika mencentang "Jadikan Aktif",
                            tahun ajaran lain akan otomatis dinonaktifkan.
                        </div>

                        {{-- Form Input --}}
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Tahun Ajaran</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white"><i class="fas fa-calendar text-muted"></i></span>
                                        </div>
                                        <input type="text"
                                               name="tahunajaran"
                                               class="form-control form-control-modern"
                                               value="{{ old('tahunajaran') }}"
                                               placeholder="Contoh: 2023/2024"
                                               required>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Format: YYYY/YYYY atau Ganjil/Genap</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-0 bg-light p-3 border rounded" style="border-radius: 0.5rem !important;">
                                    <label class="d-block font-weight-bold mb-2">Status</label>
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="status"
                                               name="status"
                                               value="1"
                                               {{ old('status') ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-normal" for="status">
                                            Jadikan Tahun Ajaran Aktif
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle"></i>
                                        Centang jika ini adalah tahun ajaran yang sedang berjalan
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card bg-light border-0 shadow-sm" style="border-radius: 0.5rem;">
                                    <div class="card-body py-3">
                                        <h6 class="font-weight-bold text-dark"><i class="fas fa-eye text-primary mr-1"></i> Preview:</h6>
                                        <div class="d-flex mt-2">
                                            <div class="mr-4">
                                                <small class="text-muted d-block">Tahun Ajaran:</small>
                                                <span id="previewTahun" class="font-weight-bold">-</span>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Status:</small>
                                                <span id="previewStatus" class="badge badge-secondary badge-modern">Tidak Aktif</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="card-footer bg-white border-0 py-3 text-right">
                        <a href="{{ route('akademik.tahun-ajaran.index') }}" class="btn btn-secondary btn-modern mr-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="reset" class="btn btn-warning btn-modern text-white mr-2">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-save"></i> Simpan Tahun Ajaran
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
// Preview tahun ajaran
document.querySelector('input[name="tahunajaran"]').addEventListener('keyup', function() {
    document.getElementById('previewTahun').textContent = this.value || '-';
});

// Preview status
document.getElementById('status').addEventListener('change', function() {
    let statusLabel = document.getElementById('previewStatus');
    if(this.checked) {
        statusLabel.textContent = 'Aktif';
        statusLabel.className = 'badge badge-success badge-modern';
    } else {
        statusLabel.textContent = 'Tidak Aktif';
        statusLabel.className = 'badge badge-secondary badge-modern';
    }
});

// Trigger preview on load
document.querySelector('input[name="tahunajaran"]').dispatchEvent(new Event('keyup'));
</script>
@endpush
