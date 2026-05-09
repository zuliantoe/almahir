@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Breadcrumb/Back button --}}
            <div class="mb-4">
                <a href="{{ route('penilaiandanpresensi.izinsakit.siswa.index') }}" class="text-muted font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Riwayat
                </a>
            </div>

            <div class="card border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="card-header bg-warning py-4 border-0">
                    <h4 class="mb-0 text-dark font-weight-bold text-center">
                        <i class="fas fa-edit mr-2"></i> {{ $title }}
                    </h4>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('penilaiandanpresensi.izinsakit.siswa.update', $izinSakit->id) }}" method="POST" enctype="multipart/form-data" id="izinForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark">Jenis Pengajuan</label>
                                    <div class="d-flex mt-2">
                                        <div class="custom-control custom-radio mr-4">
                                            <input type="radio" id="jenisIzin" name="jenis" value="Izin" class="custom-control-input" {{ $izinSakit->jenis == 'Izin' ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-info" for="jenisIzin">
                                                <i class="fas fa-info-circle mr-1"></i> Izin
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="jenisSakit" name="jenis" value="Sakit" class="custom-control-input" {{ $izinSakit->jenis == 'Sakit' ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-danger" for="jenisSakit">
                                                <i class="fas fa-hand-holding-medical mr-1"></i> Sakit
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark">Tipe Pengajuan</label>
                                    <select name="tipe_izin" id="tipe_izin" class="form-control select2-modern" required>
                                        <option value="Harian" {{ $izinSakit->tipe_izin == 'Harian' ? 'selected' : '' }}>Harian (Penuh)</option>
                                        <option value="Per Matpel" {{ $izinSakit->tipe_izin == 'Per Matpel' ? 'selected' : '' }}>Per Mata Pelajaran</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="matpel-container" class="form-group mb-4 {{ $izinSakit->tipe_izin == 'Per Matpel' ? '' : 'd-none' }}">
                            <label class="font-weight-bold text-dark">Mata Pelajaran</label>
                            <select name="mapel_id" id="mapel_id" class="form-control select2-modern">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" {{ $izinSakit->mapel_id == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark" id="label_tgl_mulai">{{ $izinSakit->tipe_izin == 'Per Matpel' ? 'Tanggal' : 'Tanggal Mulai' }}</label>
                                    <input type="date" name="tgl_mulai" class="form-control" value="{{ $izinSakit->tgl_mulai }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 {{ $izinSakit->tipe_izin == 'Per Matpel' ? 'd-none' : '' }}" id="tgl_selesai_container">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark">Tanggal Selesai</label>
                                    <input type="date" name="tgl_selesai" class="form-control" value="{{ $izinSakit->tgl_selesai }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Keterangan / Alasan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan pengajuan Anda..." required>{{ $izinSakit->keterangan }}</textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Bukti Pendukung (Foto/Surat Dokter)</label>
                            
                            @if($izinSakit->bukti_foto)
                                <div class="mb-3 text-center">
                                    <p class="small text-muted mb-1">Bukti Saat Ini:</p>
                                    <img src="{{ asset('storage/' . $izinSakit->bukti_foto) }}" alt="Bukti" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                                </div>
                            @endif

                            <div class="custom-file mb-3">
                                <input type="file" name="bukti_foto" class="custom-file-input" id="bukti_foto" accept="image/*">
                                <label class="custom-file-label" for="bukti_foto">Ubah file atau ambil foto baru...</label>
                            </div>

                            <div class="text-center mb-3">
                                <span class="text-muted small">- ATAU -</span>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm px-4" style="border-radius: 50px;" id="startCamera">
                                    <i class="fas fa-camera mr-2"></i> Ambil Foto Baru
                                </button>
                            </div>

                            {{-- Camera Preview --}}
                            <div id="camera-container" class="mt-3 d-none">
                                <video id="video" width="100%" height="auto" autoplay class="rounded shadow-sm"></video>
                                <button type="button" class="btn btn-danger btn-block mt-2" id="capture">
                                    <i class="fas fa-circle mr-1"></i> Tangkap Gambar
                                </button>
                                <canvas id="canvas" class="d-none"></canvas>
                            </div>

                            {{-- Image Preview --}}
                            <div id="preview-container" class="mt-3 d-none text-center">
                                <img id="preview-image" src="#" alt="Preview" class="img-fluid rounded shadow-sm mb-2" style="max-height: 300px;">
                                <input type="hidden" name="captured_image" id="captured_image">
                                <button type="button" class="btn btn-sm btn-link text-danger" id="removePreview">
                                    <i class="fas fa-times mr-1"></i> Batal Ubah Foto
                                </button>
                            </div>
                        </div>

                        <hr class="my-5">

                        <button type="submit" class="btn btn-warning btn-lg btn-block shadow-sm" style="border-radius: 15px; font-weight: 700;">
                            <i class="fas fa-save mr-2"></i> SIMPAN PERUBAHAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-control-label::before, .custom-control-label::after {
        top: 0.2rem;
        width: 1.5rem;
        height: 1.5rem;
    }
    .custom-control-input:checked ~ .custom-control-label::before {
        border-color: #4361ee;
        background-color: #4361ee;
    }
    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.1);
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tipe Izin Toggle
        $('#tipe_izin').change(function() {
            if ($(this).val() === 'Per Matpel') {
                $('#matpel-container').removeClass('d-none');
                $('#tgl_selesai_container').addClass('d-none');
                $('#label_tgl_mulai').text('Tanggal');
            } else {
                $('#matpel-container').addClass('d-none');
                $('#tgl_selesai_container').removeClass('d-none');
                $('#label_tgl_mulai').text('Tanggal Mulai');
            }
        });

        // File Input Label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
            
            // Preview
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-image').attr('src', e.target.result);
                    $('#preview-container').removeClass('d-none');
                    $('#camera-container').addClass('d-none');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Camera Functionality
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureButton = document.getElementById('capture');
        const startCameraButton = document.getElementById('startCamera');
        const capturedInput = document.getElementById('captured_image');
        let stream = null;

        startCameraButton.addEventListener('click', async function() {
            $('#camera-container').removeClass('d-none');
            $('#preview-container').addClass('d-none');
            
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                video.srcObject = stream;
            } catch (err) {
                Swal.fire('Error', 'Gagal mengakses kamera: ' + err.message, 'error');
            }
        });

        captureButton.addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const dataUrl = canvas.toDataURL('image/jpeg');
            $('#preview-image').attr('src', dataUrl);
            capturedInput.value = dataUrl;
            
            $('#preview-container').removeClass('d-none');
            $('#camera-container').addClass('d-none');
            
            // Stop stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        $('#removePreview').click(function() {
            $('#preview-container').addClass('d-none');
            $('#bukti_foto').val('');
            $('.custom-file-label').html('Ubah file atau ambil foto baru...');
            capturedInput.value = '';
        });
    });
</script>
@endpush
