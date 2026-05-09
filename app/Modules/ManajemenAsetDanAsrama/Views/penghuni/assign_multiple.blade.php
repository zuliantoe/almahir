@extends('layouts.app')

@section('title', $title)

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0" style="font-weight: 700;">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.index') }}">Asrama</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.kamar.index') }}">Kamar</a></li>
            <li class="breadcrumb-item active">Input Penghuni</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <form action="{{ route('manajemenasetdanasrama.penghuni.store-multiple', $kamar->id) }}" method="POST">
                @csrf
                <x-card :title="'Input Penghuni Kamar: ' . $kamar->nama_kamar" icon="fas fa-users">
                    <div class="alert alert-info shadow-sm border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x mr-3"></i>
                            <div>
                                <strong>Informasi Kamar:</strong> Kapasitas kamar ini adalah <strong>{{ $kamar->kapasitas }} orang</strong>. 
                                Anda dapat mengisi hingga {{ $kamar->kapasitas }} santri sekaligus.
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 bg-light p-3 rounded border">
                        <div class="col-md-4">
                            <label>Tanggal Masuk Kamar <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-8">
                            <p class="text-muted small mt-4 pt-2">
                                <i class="fas fa-magic mr-1 text-primary"></i> 
                                Tips: Pilih santri di satu dropdown, maka santri tersebut akan otomatis hilang dari pilihan di dropdown lainnya secara realtime.
                            </p>
                        </div>
                    </div>

                    <div id="penghuni-container">
                        @for($i = 0; $i < $kamar->kapasitas; $i++)
                        <div class="card mb-3 border-left-primary shadow-sm resident-row">
                            <div class="card-body py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto text-center pr-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            {{ $i + 1 }}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="small font-weight-bold">Pilih Santri</label>
                                        <select name="siswa_id[]" class="form-control select2 student-select" data-placeholder="-- Cari Nama Santri --">
                                            <option value=""></option>
                                            @foreach($siswa as $s)
                                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small font-weight-bold">Jabatan</label>
                                        <select name="jabatan[]" class="form-control">
                                            <option value="Anggota" {{ $i > 1 ? 'selected' : '' }}>Anggota</option>
                                            <option value="Ketua Kamar" {{ $i == 0 ? 'selected' : '' }}>Ketua Kamar</option>
                                            <option value="Wakil Ketua Kamar" {{ $i == 1 ? 'selected' : '' }}>Wakil Ketua Kamar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small font-weight-bold">Keterangan (Opsional)</label>
                                        <input type="text" name="keterangan[]" class="form-control" placeholder="Catatan...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('manajemenasetdanasrama.kamar.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Batal / Lewati
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save mr-1"></i> Simpan Semua Penghuni
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </form>
        </div>
    </div>
</div>

<style>
    .border-left-primary { border-left: 4px solid #007bff !important; }
    .select2-container--bootstrap4 .select2-selection--single { height: 38px !important; }
</style>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        function initSelect2() {
            $('.select2').select2({
                theme: 'bootstrap4',
                allowClear: true
            });
        }

        initSelect2();

        // Logic Real-time Filtering
        $(document).on('change', '.student-select', function() {
            updateAvailableStudents();
        });

        function updateAvailableStudents() {
            // Ambil semua ID yang sudah dipilih
            var selectedIds = [];
            $('.student-select').each(function() {
                var val = $(this).val();
                if (val) selectedIds.push(val);
            });

            // Update setiap dropdown
            $('.student-select').each(function() {
                var currentDropdown = $(this);
                var currentValue = currentDropdown.val();

                // Reset semua option dulu (tampilkan semua)
                currentDropdown.find('option').prop('disabled', false);

                // Disable option yang sudah dipilih di dropdown LAIN
                selectedIds.forEach(function(id) {
                    if (id != currentValue) {
                        currentDropdown.find('option[value="' + id + '"]').prop('disabled', true);
                    }
                });

                // Refresh Select2 buat update tampilan disabled
                currentDropdown.select2({
                    theme: 'bootstrap4',
                    allowClear: true
                });
            });
        }
    });
</script>
@endpush
