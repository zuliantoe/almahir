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
            <li class="breadcrumb-item"><a href="{{ route('manajemenasetdanasrama.penghuni.index') }}">Penghuni</a></li>
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
        <div class="col-md-10">
            <form action="{{ route('manajemenasetdanasrama.penghuni.update', $penghuni->id) }}" method="POST">
                @csrf
                @method('PUT')
                <x-card title="Edit Profil & Penempatan Penghuni" icon="fas fa-user-edit" class="card-outline card-warning shadow-lg">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center border-right">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($penghuni->siswa->nama) }}&background=random&size=150" 
                                 class="img-circle elevation-2 mb-3" style="width: 150px; border: 5px solid #fff;">
                            <h5 class="font-weight-bold text-primary mb-0">{{ $penghuni->siswa->nama }}</h5>
                            <p class="text-muted small">{{ $penghuni->siswa->nis }}</p>
                            <span class="badge badge-info px-3 py-2" style="border-radius: 6px;">{{ $penghuni->jabatan }}</span>
                        </div>
                        <div class="col-md-8 px-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">KAMAR SAAT INI <span class="text-danger">*</span></label>
                                        <select class="form-control select2 @error('kamar_id') is-invalid @enderror" id="kamar_id" name="kamar_id" required>
                                            <option value="">-- Pilih Kamar --</option>
                                            @foreach($kamar as $k)
                                                @php $isFull = $k->sisa <= 0 && $k->id != $penghuni->kamar_id; @endphp
                                                <option value="{{ $k->id }}" 
                                                        data-students='@json($k->penghuni->map(fn($p) => ["id" => $p->id, "nama" => $p->siswa->nama]))'
                                                        data-isfull="{{ $isFull ? '1' : '0' }}"
                                                        {{ old('kamar_id', $penghuni->kamar_id) == $k->id ? 'selected' : '' }}>
                                                    {{ $k->nama_kamar }} {{ $isFull ? '(PENUH)' : '(Sisa: '.$k->sisa.' slot)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">JABATAN <span class="text-danger">*</span></label>
                                        <select class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" required>
                                            <option value="Anggota" {{ old('jabatan', $penghuni->jabatan) == 'Anggota' ? 'selected' : '' }}>Anggota</option>
                                            <option value="Ketua Kamar" {{ old('jabatan', $penghuni->jabatan) == 'Ketua Kamar' ? 'selected' : '' }}>Ketua Kamar</option>
                                            <option value="Wakil Ketua Kamar" {{ old('jabatan', $penghuni->jabatan) == 'Wakil Ketua Kamar' ? 'selected' : '' }}>Wakil Ketua Kamar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="swap_container" class="alert alert-warning border-0 shadow-sm mt-2" style="display: none; border-left: 5px solid #ffc107 !important;">
                                <div class="form-group mb-0">
                                    <label for="swap_penghuni_id" class="font-weight-bold text-dark">
                                        <i class="fas fa-exchange-alt mr-1"></i> Kamar Penuh! Pilih Santri Untuk Ditukar:
                                    </label>
                                    <select class="form-control select2" id="swap_penghuni_id" name="swap_penghuni_id">
                                        <option value="">-- Pilih Santri --</option>
                                    </select>
                                    <small class="text-dark-50 mt-1 d-block">Santri yang Anda pilih akan otomatis pindah ke kamar lama <strong>{{ $penghuni->siswa->nama }}</strong>.</small>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">TANGGAL MASUK <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror" name="tanggal_masuk" value="{{ old('tanggal_masuk', $penghuni->tanggal_masuk ? $penghuni->tanggal_masuk->format('Y-m-d') : '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">TANGGAL KELUAR (OPSIONAL)</label>
                                        <input type="date" class="form-control" name="tanggal_keluar" value="{{ old('tanggal_keluar', $penghuni->tanggal_keluar ? $penghuni->tanggal_keluar->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">KETERANGAN</label>
                                <textarea class="form-control" name="keterangan" rows="2" placeholder="Catatan tambahan...">{{ old('keterangan', $penghuni->keterangan) }}</textarea>
                            </div>

                            {{-- Hidden Field Siswa ID agar tetap terkirim --}}
                            <input type="hidden" name="siswa_id" value="{{ $penghuni->siswa_id }}">
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('manajemenasetdanasrama.kamar.show', $penghuni->kamar_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail Kamar
                            </a>
                            <button type="submit" class="btn btn-warning px-5 shadow-sm font-weight-bold text-white">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 12px) !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 10px) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih --",
            allowClear: true
        });

        // Logic Tukar Kamar
        $('#kamar_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var isFull = selectedOption.data('isfull') == '1';
            var students = selectedOption.data('students');
            var currentKamarId = "{{ $penghuni->kamar_id }}";
            var selectedKamarId = $(this).val();

            if (isFull && selectedKamarId != currentKamarId) {
                // Tampilkan dropdown tukar
                $('#swap_container').fadeIn();
                $('#swap_penghuni_id').attr('required', true);
                
                // Isi dropdown tukar
                var swapDropdown = $('#swap_penghuni_id');
                swapDropdown.empty().append('<option value="">-- Pilih Santri Untuk Ditukar --</option>');
                
                if (students && students.length > 0) {
                    students.forEach(function(student) {
                        swapDropdown.append('<option value="' + student.id + '">' + student.nama + '</option>');
                    });
                }
                swapDropdown.select2({ theme: 'bootstrap4' });
            } else {
                // Sembunyikan dropdown tukar
                $('#swap_container').fadeOut();
                $('#swap_penghuni_id').attr('required', false).val(null).trigger('change');
            }
        });

        // Trigger change saat load jika kamar berbeda dari aslinya (misal pas redirect back input)
        if ($('#kamar_id').val() != "{{ $penghuni->kamar_id }}") {
            $('#kamar_id').trigger('change');
        }
    });
</script>
@endpush
