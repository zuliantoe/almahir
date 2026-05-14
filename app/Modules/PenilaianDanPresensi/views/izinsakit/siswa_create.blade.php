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
                <div class="card-header bg-primary py-4 border-0">
                    <h4 class="mb-0 text-white font-weight-bold text-center">
                        <i class="fas fa-paper-plane mr-2"></i> {{ $title }}
                    </h4>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('penilaiandanpresensi.izinsakit.siswa.store') }}" method="POST" enctype="multipart/form-data" id="izinForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark d-block mb-3">Jenis Pengajuan</label>
                                    <div class="row px-2">
                                        <div class="col-6 p-1">
                                            <div class="jenis-card p-3 text-center" onclick="selectJenis('Izin')" id="card-izin">
                                                <input type="radio" name="jenis" value="Izin" id="radio-izin" class="d-none" checked>
                                                <div class="icon-wrapper mb-2">
                                                    <i class="fas fa-info-circle fa-2x"></i>
                                                </div>
                                                <span class="font-weight-bold">Izin</span>
                                            </div>
                                        </div>
                                        <div class="col-6 p-1">
                                            <div class="jenis-card p-3 text-center" onclick="selectJenis('Sakit')" id="card-sakit">
                                                <input type="radio" name="jenis" value="Sakit" id="radio-sakit" class="d-none">
                                                <div class="icon-wrapper mb-2">
                                                    <i class="fas fa-hand-holding-medical fa-2x"></i>
                                                </div>
                                                <span class="font-weight-bold">Sakit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark">Tipe Pengajuan</label>
                                    <select name="tipe_izin" id="tipe_izin" class="form-control select2-modern" required>
                                        <option value="Harian" {{ $request->tipe == 'Harian' ? 'selected' : '' }}>Harian (Penuh)</option>
                                        <option value="Per Matpel" {{ $request->tipe == 'Per Matpel' ? 'selected' : '' }}>Per Mata Pelajaran</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="matpel-container" class="form-group mb-4 {{ $request->tipe == 'Per Matpel' ? '' : 'd-none' }}">
                            <label class="font-weight-bold text-dark">Mata Pelajaran</label>
                            <select name="mapel_id" id="mapel_id" class="form-control select2-modern">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" {{ $request->mapel_id == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark" id="label_tgl_mulai">Tanggal Mulai</label>
                                    <input type="date" name="tgl_mulai" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6" id="tgl_selesai_container">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark">Tanggal Selesai</label>
                                    <input type="date" name="tgl_selesai" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Keterangan / Alasan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan pengajuan Anda..." required></textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Bukti Pendukung (Foto/Surat Dokter)</label>
                            
                            <div class="custom-file mb-3">
                                <input type="file" name="bukti_foto" class="custom-file-input" id="bukti_foto" accept="image/*">
                                <label class="custom-file-label" for="bukti_foto">Pilih file atau ambil foto...</label>
                            </div>

                            <div class="text-center mb-3">
                                <span class="text-muted small">- ATAU -</span>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm px-4" style="border-radius: 50px;" id="startCamera">
                                    <i class="fas fa-camera mr-2"></i> Ambil Foto Langsung
                                </button>
                            </div>

                            {{-- Camera Preview --}}
                            <div id="camera-container" class="mt-3 d-none position-relative">
                                <video id="video" width="100%" height="auto" autoplay playsinline class="rounded shadow-sm" style="background: #000;"></video>
                                <div class="camera-controls mt-3">
                                    <button type="button" class="btn btn-primary btn-lg btn-block mb-2" id="capture" style="border-radius: 12px;">
                                        <i class="fas fa-circle mr-2"></i> Ambil Foto
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-block" id="switchCamera" style="border-radius: 12px;">
                                        <i class="fas fa-sync mr-2"></i> Ganti Kamera (Depan/Belakang)
                                    </button>
                                </div>
                                <canvas id="canvas" class="d-none"></canvas>
                            </div>

                            {{-- Image Preview --}}
                            <div id="preview-container" class="mt-3 d-none">
                                <div class="bg-light p-3 rounded shadow-sm border text-center">
                                    <div class="position-relative d-inline-block">
                                        <img id="preview-image" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                    <div class="mt-3 d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-outline-primary px-4 mr-2" onclick="$('#startCamera').click()" style="border-radius: 12px;">
                                            <i class="fas fa-redo mr-2"></i> Retake Foto
                                        </button>
                                        <button type="button" class="btn btn-danger px-4" id="removePreview" style="border-radius: 12px;">
                                            <i class="fas fa-trash mr-2"></i> Hapus Foto
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="captured_image" id="captured_image">
                            </div>
                        </div>

                        <hr class="my-5">

                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm" style="border-radius: 15px; font-weight: 700;">
                            <i class="fas fa-paper-plane mr-2"></i> KIRIM PENGAJUAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .jenis-card {
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .jenis-card:hover {
        border-color: #4361ee;
        background: rgba(67, 97, 238, 0.05);
    }
    .jenis-card#card-izin.active {
        border-color: #0dcaf0;
        background: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }
    .jenis-card#card-sakit.active {
        border-color: #dc3545;
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .jenis-card.active .icon-wrapper i {
        transform: scale(1.1);
    }
    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.1);
    }
</style>
@endsection

@push('scripts')
<script>
    function selectJenis(jenis) {
        $('.jenis-card').removeClass('active');
        if (jenis === 'Izin') {
            $('#card-izin').addClass('active');
            $('#radio-izin').prop('checked', true);
        } else {
            $('#card-sakit').addClass('active');
            $('#radio-sakit').prop('checked', true);
        }
    }

    $(document).ready(function() {
        selectJenis('Izin'); // Default

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
                    stopCamera();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Camera Functionality
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureButton = document.getElementById('capture');
        const startCameraButton = document.getElementById('startCamera');
        const switchCameraButton = document.getElementById('switchCamera');
        const capturedInput = document.getElementById('captured_image');
        let stream = null;
        let currentFacingMode = 'environment'; // Default back camera

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        async function startCamera(facingMode) {
            stopCamera();
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: facingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                video.srcObject = stream;
                $('#camera-container').removeClass('d-none');
            } catch (err) {
                Swal.fire('Error', 'Gagal mengakses kamera: ' + err.message, 'error');
            }
        }

        startCameraButton.addEventListener('click', function() {
            $('#preview-container').addClass('d-none');
            startCamera(currentFacingMode);
        });

        switchCameraButton.addEventListener('click', function() {
            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            startCamera(currentFacingMode);
        });

        captureButton.addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            $('#preview-image').attr('src', dataUrl);
            capturedInput.value = dataUrl;
            
            $('#preview-container').removeClass('d-none');
            $('#camera-container').addClass('d-none');
            stopCamera();
        });

        $('#removePreview').click(function() {
            $('#preview-container').addClass('d-none');
            $('#bukti_foto').val('');
            $('.custom-file-label').html('Pilih file atau ambil foto...');
            capturedInput.value = '';
            $('#preview-image').attr('src', '#');
        });
    });
</script>
@endpush
