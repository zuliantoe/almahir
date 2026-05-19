@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Izin/Sakit" icon="fas fa-plus-circle">
        <form action="{{ route('penilaiandanpresensi.izinsakit.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="siswa_id">Siswa <span class="text-danger">*</span></label>
                <select name="siswa_id" id="siswa_id" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="rombel_id">Rombel <span class="text-danger">*</span></label>
                <select name="rombel_id" id="rombel_id" class="form-control select2" required>
                    <option value="">-- Pilih Rombel --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ old('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jenis">Jenis <span class="text-danger">*</span></label>
                <select name="jenis" id="jenis" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Izin" {{ old('jenis')=='Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ old('jenis')=='Sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
            </div>

            <div id="dynamic-fields" style="display: none;">
                <div class="form-group">
                    <label for="keterangan">Keterangan / Alasan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                </div>
                
                <div class="form-group">
                    <label for="bukti_foto">Bukti (Surat Dokter / Foto)</label>
                    <div class="d-flex align-items-start">
                        <div class="flex-grow-1 mr-2">
                            <input type="file" name="bukti_foto" id="bukti_foto" class="form-control-file" accept="image/*">
                            <small class="text-muted">Maksimal ukuran file: 2MB. Di HP, pilih 'Kamera' untuk jepret langsung.</small>
                        </div>
                        <button type="button" id="btn-camera" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-camera mr-1"></i> Gunakan Kamera
                        </button>
                    </div>
                    
                    <div id="camera-container" class="mt-3 d-none">
                        <div class="position-relative bg-dark rounded overflow-hidden" style="max-width: 400px;">
                            <video id="video" width="100%" autoplay playsinline></video>
                            <canvas id="canvas" class="d-none"></canvas>
                            <div class="p-2 text-center">
                                <button type="button" id="btn-capture" class="btn btn-success btn-sm">
                                    <i class="fas fa-camera"></i> Ambil Foto
                                </button>
                                <button type="button" id="btn-switch-camera" class="btn btn-info btn-sm">
                                    <i class="fas fa-sync"></i> Putar Kamera
                                </button>
                                <button type="button" id="btn-cancel-camera" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="preview-container" class="mt-3 d-none">
                        <label class="small font-weight-bold text-muted">Preview Bukti:</label>
                        <div class="position-relative" style="max-width: 200px;">
                            <img id="preview-image" src="#" alt="Preview" class="img-fluid rounded shadow-sm border">
                            <button type="button" id="btn-remove-preview" class="btn btn-danger btn-xs position-absolute" style="top: -10px; right: -10px; border-radius: 50%;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="hidden" name="captured_image" id="captured_image">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="tgl_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control" value="{{ old('tgl_mulai', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="tgl_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control" value="{{ old('tgl_selesai', date('Y-m-d')) }}" required>
            </div>



            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('penilaiandanpresensi.izinsakit.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection

@push('styles')
<style>
    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .btn {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    x-card {
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.05);
    }
    label {
        font-weight: 600;
        color: #495057;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jenisSelect = document.getElementById('jenis');
        const dynamicFields = document.getElementById('dynamic-fields');

        function toggleFields() {
            if (jenisSelect.value === 'Izin' || jenisSelect.value === 'Sakit') {
                dynamicFields.style.display = 'block';
            } else {
                dynamicFields.style.display = 'none';
            }
        }

        jenisSelect.addEventListener('change', toggleFields);
        toggleFields();

        // Camera Logic
        const btnCamera = document.getElementById('btn-camera');
        const cameraContainer = document.getElementById('camera-container');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const btnCapture = document.getElementById('btn-capture');
        const btnCancelCamera = document.getElementById('btn-cancel-camera');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const capturedImageInput = document.getElementById('captured_image');
        const btnRemovePreview = document.getElementById('btn-remove-preview');
        const fileInput = document.getElementById('bukti_foto');

        const btnSwitchCamera = document.getElementById('btn-switch-camera');

        let stream = null;
        let currentFacingMode = 'environment';

        async function startCamera(facingMode) {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            try {
                currentFacingMode = facingMode;
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode } });
                video.srcObject = stream;
                cameraContainer.classList.remove('d-none');
                btnCamera.classList.add('d-none');
            } catch (err) {
                console.error("Error accessing camera: ", err);
                Swal.fire('Opps!', 'Gagal mengakses kamera. Pastikan izin kamera diberikan.', 'error');
            }
        }

        btnCamera.addEventListener('click', () => startCamera('environment'));

        btnSwitchCamera.addEventListener('click', () => {
            const newMode = currentFacingMode === 'user' ? 'environment' : 'user';
            startCamera(newMode);
        });

        btnCapture.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = canvas.toDataURL('image/jpeg');
            previewImage.src = imageData;
            capturedImageInput.value = imageData;
            
            previewContainer.classList.remove('d-none');
            stopCamera();
        });

        btnCancelCamera.addEventListener('click', stopCamera);

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            cameraContainer.classList.add('d-none');
            btnCamera.classList.remove('d-none');
        }

        btnRemovePreview.addEventListener('click', () => {
            previewContainer.classList.add('d-none');
            previewImage.src = '#';
            capturedImageInput.value = '';
            fileInput.value = '';
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                    capturedImageInput.value = ''; // Reset captured image if file is chosen
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush
