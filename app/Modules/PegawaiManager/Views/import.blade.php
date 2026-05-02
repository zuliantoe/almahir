@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="mb-4">
                <a href="{{ route('pegawaimanager.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm btn-animate">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>

            <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header gradient-primary border-0 p-4">
                    <h3 class="card-title text-white font-weight-bold mb-0">
                        <i class="fas fa-file-import mr-2"></i> Import Data Pegawai Masal
                    </h3>
                </div>
                
                <div class="card-body p-4 bg-light">
                    
                    <div class="alert alert-info border-0 shadow-sm" style="border-radius: 10px;">
                        <h5><i class="icon fas fa-info"></i> Petunjuk Import:</h5>
                        <ul class="mb-0 pl-3">
                            <li>Gunakan fitur <b>Export Data</b> di halaman sebelumnya untuk mendapatkan *template* (struktur) kolom yang benar.</li>
                            <li>Buka file tersebut di Microsoft Excel, isi data pegawai baru di baris terbawah.</li>
                            <li>Saat menyimpan, pastikan Anda menggunakan menu <b>Save As -> CSV (Comma delimited) (*.csv)</b>.</li>
                            <li>Abaikan baris pertama (Header), sistem akan otomatis melewatinya.</li>
                            <li>Format CSV bisa menggunakan pemisah Koma <code>,</code> ataupun Titik Koma <code>;</code>.</li>
                        </ul>
                    </div>

                    <div class="glass-card p-4 mt-4 text-center">
                        <form action="{{ route('pegawaimanager.process_import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="form-group mb-4">
                                <label class="d-block mb-3 font-weight-bold">Unggah File CSV (.csv)</label>
                                
                                <div class="custom-file" style="max-width: 400px; margin: 0 auto;">
                                    <input type="file" name="file" class="custom-file-input form-control-premium" id="customFile" accept=".csv" required>
                                    <label class="custom-file-label text-left" for="customFile">Pilih file CSV...</label>
                                </div>
                                <small class="d-block mt-2 text-muted">Maksimal ukuran file: 2MB.</small>
                            </div>

                            <hr>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm btn-animate gradient-primary border-0 rounded-pill font-weight-bold">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i> Mulai Proses Import
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Menampilkan nama file di input custom file Bootstrap
    $('.custom-file-input').on('change', function() { 
        let fileName = $(this).val().split('\\').pop(); 
        $(this).next('.custom-file-label').addClass("selected").html(fileName); 
    });
</script>
@endpush
@endsection
