@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <form action="{{ route('akademik.tahun-ajaran.store') }}" method="POST">
        @csrf

        <x-card title="Tambah Tahun Ajaran" type="primary">

            {{-- Informasi --}}
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Tahun ajaran yang aktif hanya boleh satu. Jika mencentang "Jadikan Aktif",
                tahun ajaran lain akan otomatis dinonaktifkan.
            </div>

            {{-- Form Input --}}
            <div class="row">
                <div class="col-md-6">
                    <x-input
                        label="Tahun Ajaran"
                        name="tahun_ajaran"
                        value="{{ old('tahun_ajaran') }}"
                        placeholder="Contoh: 2023/2024"
                        icon="fas fa-calendar"
                        required
                    />
                    <small class="text-muted">Format: YYYY/YYYY atau Ganjil/Genap</small>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="d-block">Status</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="status"
                                   name="status"
                                   value="1"
                                   {{ old('status') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status">
                                Jadikan Tahun Ajaran Aktif
                            </label>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Centang jika ini adalah tahun ajaran yang sedang berjalan
                        </small>
                    </div>
                </div>
            </div>

            {{-- Preview --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-eye"></i> Preview:</h6>
                            <p class="mb-0">
                                Tahun Ajaran: <span id="previewTahun">-</span><br>
                                Status: <span id="previewStatus">Tidak Aktif</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('akademik.tahun-ajaran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <div>
                        <button type="reset" class="btn btn-warning">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </x-slot>

        </x-card>
    </form>
</div>
@endsection

@push('js')
<script>
// Preview tahun ajaran
document.querySelector('input[name="tahun_ajaran"]').addEventListener('keyup', function() {
    document.getElementById('previewTahun').textContent = this.value || '-';
});

// Preview status
document.getElementById('status').addEventListener('change', function() {
    document.getElementById('previewStatus').textContent = this.checked ? 'Aktif' : 'Tidak Aktif';
});

// Trigger preview on load
document.querySelector('input[name="tahun_ajaran"]').dispatchEvent(new Event('keyup'));
</script>
@endpush
