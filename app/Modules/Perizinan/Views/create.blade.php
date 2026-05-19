@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- ===== FORM CARD ===== --}}
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-lg" style="border-radius:15px;overflow:hidden;">
                <div class="card-header gradient-primary border-0 p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3"
                             style="width:48px;height:48px;background:rgba(255,255,255,.2);">
                            <i class="fas fa-paper-plane fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="card-title text-white font-weight-bold mb-0">Form Pengajuan Izin</h4>
                            <p class="text-white-50 small mb-0">Isi formulir di bawah dengan lengkap dan benar</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 bg-light">
                    {{-- Validation errors --}}
                    @if($errors->any())
                    <div class="alert alert-danger rounded-lg mb-4 border-0 shadow-sm">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Mohon perbaiki kesalahan berikut:</h6>
                        <ul class="mb-0 pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('perizinan.store') }}" method="POST" enctype="multipart/form-data" id="formPengajuan">
                        @csrf

                        {{-- Jenis Izin --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-tag text-primary mr-1"></i> Jenis Perizinan <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                @php
                                    $jenisList = [
                                        ['val'=>'izin',       'label'=>'Izin Pribadi',  'icon'=>'fa-hand-paper',      'color'=>'#ffc107'],
                                        ['val'=>'sakit',      'label'=>'Sakit',          'icon'=>'fa-briefcase-medical','color'=>'#dc3545'],
                                        ['val'=>'cuti',       'label'=>'Cuti Tahunan',   'icon'=>'fa-umbrella-beach',  'color'=>'#007bff'],
                                        ['val'=>'dinas luar', 'label'=>'Dinas Luar',     'icon'=>'fa-briefcase',       'color'=>'#17a2b8'],
                                    ];
                                @endphp
                                @foreach($jenisList as $j)
                                <div class="col-6 col-md-3 mb-2">
                                    <label class="jenis-card d-block text-center p-3 rounded border cursor-pointer {{ old('jenis_izin') == $j['val'] ? 'selected' : '' }}"
                                           style="cursor:pointer;transition:all .2s;" onclick="selectJenis('{{ $j['val'] }}')">
                                        <input type="radio" name="jenis_izin" value="{{ $j['val'] }}"
                                               id="jenis_{{ str_replace(' ', '_', $j['val']) }}" style="display:none;"
                                               {{ old('jenis_izin') == $j['val'] ? 'checked' : '' }}>
                                        <i class="fas {{ $j['icon'] }} fa-2x mb-2 d-block" style="color:{{ $j['color'] }};"></i>
                                        <span class="small font-weight-bold text-dark">{{ $j['label'] }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('jenis_izin') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">
                                        <i class="fas fa-calendar-alt text-primary mr-1"></i> Tanggal Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="tanggal_mulai" id="tgl_mulai"
                                           class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                           value="{{ old('tanggal_mulai') }}" required onchange="hitungDurasi()">
                                    @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">
                                        <i class="fas fa-calendar-check text-success mr-1"></i> Tanggal Selesai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="tanggal_selesai" id="tgl_selesai"
                                           class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                           value="{{ old('tanggal_selesai') }}" required onchange="hitungDurasi()">
                                    @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Durasi info (real-time) --}}
                        <div id="infoDurasi" class="alert alert-info border-0 rounded-lg shadow-sm mb-3 py-2 px-3" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Durasi yang diajukan: <strong id="txtDurasi">0</strong> hari
                                </span>
                                <span id="badgeImpact" class="badge p-2"></span>
                            </div>
                            <div id="warnCuti" class="text-danger mt-2 font-weight-bold small" style="display:none;"></div>
                        </div>

                        {{-- Alasan --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-comment-alt text-warning mr-1"></i> Alasan / Keterangan <span class="text-danger">*</span>
                            </label>
                            <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror"
                                      rows="4" placeholder="Jelaskan alasan pengajuan Anda secara singkat dan jelas..." required>{{ old('alasan') }}</textarea>
                            @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Lampiran --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-paperclip text-secondary mr-1"></i> Lampiran / Bukti
                                <small class="text-muted font-weight-normal">(Opsional · Max 2MB · JPG, PNG)</small>
                            </label>
                            <div class="custom-file">
                                <input type="file" name="bukti" class="custom-file-input @error('bukti') is-invalid @enderror"
                                       id="fileBukti" accept="image/*" onchange="previewFile(this)">
                                <label class="custom-file-label" for="fileBukti">Pilih file...</label>
                            </div>
                            @error('bukti') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div id="previewImg" class="mt-2" style="display:none;">
                                <img id="imgPreview" src="" class="img-fluid rounded border shadow-sm" style="max-height:200px;">
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('perizinan.index') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-animate">
                                <i class="fas fa-arrow-left mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow btn-animate font-weight-bold gradient-primary border-0" id="btnSubmit">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== INFO SIDEBAR ===== --}}
        <div class="col-md-4">
            {{-- Sisa cuti card --}}
            @if(isset($sisaCuti))
            @php
                $colorCuti = $sisaCuti >= 7 ? '#28a745' : ($sisaCuti >= 4 ? '#ffc107' : '#dc3545');
                $pct = round(($sisaCuti / 12) * 100);
            @endphp
            <div class="glass-card p-4 border-0 mb-4" style="border-top:5px solid {{ $colorCuti }} !important;">
                <h6 class="font-weight-bold mb-3" style="color:{{ $colorCuti }};"><i class="fas fa-umbrella-beach mr-2"></i>Sisa Jatah Cuti</h6>
                <div class="d-flex align-items-end mb-2">
                    <span class="display-4 font-weight-bolder mr-2" style="color:{{ $colorCuti }};line-height:1;">{{ $sisaCuti }}</span>
                    <span class="text-muted mb-1">/ 12 Hari</span>
                </div>
                <div class="progress mb-2" style="height:8px;border-radius:10px;background:#eee;">
                    <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $colorCuti }};border-radius:10px;transition:width .5s;"></div>
                </div>
                <small class="text-muted">{{ 12 - $sisaCuti }} hari telah terpakai di tahun {{ date('Y') }}</small>
            </div>
            @endif

            {{-- Ketentuan --}}
            <div class="glass-card p-4 border-0 mb-4" style="border-top:5px solid #007bff !important;">
                <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Ketentuan Pengajuan</h6>
                <ul class="mb-0 pl-0" style="list-style:none;">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1 flex-shrink-0"></i>
                        <span class="small text-muted">Pastikan <b>Tanggal Mulai</b> & <b>Tanggal Selesai</b> diisi dengan benar.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1 flex-shrink-0"></i>
                        <span class="small text-muted">Izin <b>Sakit</b> &gt; 1 hari wajib melampirkan <b>Surat Keterangan Dokter</b>.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-exclamation-circle text-warning mr-2 mt-1 flex-shrink-0"></i>
                        <span class="small text-muted">Jatah <b>Cuti</b> maksimal <b>12 hari/tahun</b> dan reset setiap awal tahun.</span>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="fas fa-user-check text-info mr-2 mt-1 flex-shrink-0"></i>
                        <span class="small text-muted">Persetujuan ditinjau oleh <b>Kepala Sekolah</b> atau <b>Admin TU</b>.</span>
                    </li>
                </ul>
            </div>

            {{-- Quick link --}}
            <a href="{{ route('perizinan.index') }}" class="btn btn-block btn-outline-secondary rounded-pill btn-animate shadow-sm">
                <i class="fas fa-list mr-2"></i> Lihat Riwayat Saya
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Pilih jenis izin via card
function selectJenis(val) {
    // Reset semua kartu
    document.querySelectorAll('.jenis-card').forEach(el => {
        el.style.background = '';
        el.style.borderColor = '';
        el.style.boxShadow = '';
        el.classList.remove('selected');
    });

    // Cari radio berdasarkan value (bukan ID) — ini lebih aman
    const radio = document.querySelector(`input[name="jenis_izin"][value="${val}"]`);
    if (radio) {
        radio.checked = true;
        const card = radio.closest('.jenis-card');
        if (card) {
            card.style.background = '#e8f4fd';
            card.style.borderColor = '#007bff';
            card.style.boxShadow = '0 0 0 3px rgba(0,123,255,.2)';
            card.classList.add('selected');
        }
    }

    // tampilkan peringatan cuti jika perlu
    hitungDurasi();
}

// Hitung durasi real-time
function hitungDurasi() {
    const mulai   = document.getElementById('tgl_mulai').value;
    const selesai = document.getElementById('tgl_selesai').value;
    const sisaCuti = {{ $sisaCuti ?? 999 }};

    if (mulai && selesai) {
        const d1 = new Date(mulai), d2 = new Date(selesai);
        const diff = Math.round((d2 - d1) / 86400000) + 1;
        if (diff > 0) {
            document.getElementById('infoDurasi').style.display = '';
            document.getElementById('txtDurasi').textContent = diff;

            const jenis = document.querySelector('input[name="jenis_izin"]:checked');
            const warnEl = document.getElementById('warnCuti');
            const impactEl = document.getElementById('badgeImpact');
            
            if (jenis) {
                if (jenis.value === 'cuti') {
                    impactEl.className = 'badge badge-primary p-2';
                    impactEl.innerHTML = '<i class="fas fa-umbrella-beach mr-1"></i> Potong Kuota Cuti';
                    
                    if (diff > sisaCuti) {
                        warnEl.textContent = `⚠ Melebihi sisa jatah cuti Anda (${sisaCuti} hari)!`;
                        warnEl.style.display = '';
                    } else {
                        warnEl.style.display = 'none';
                    }
                } else if (jenis.value === 'izin' || jenis.value === 'sakit') {
                    impactEl.className = 'badge badge-warning p-2 text-dark';
                    impactEl.innerHTML = '<i class="fas fa-coins mr-1"></i> Potong Gaji';
                    warnEl.style.display = 'none';
                } else {
                    impactEl.className = 'badge badge-success p-2';
                    impactEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Dibayar Penuh';
                    warnEl.style.display = 'none';
                }
            }
        } else {
            document.getElementById('infoDurasi').style.display = 'none';
        }
    }
}

// Preview file
function previewFile(input) {
    const label = input.nextElementSibling;
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('previewImg').style.display = '';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Form submit dengan validasi client-side
document.addEventListener('DOMContentLoaded', function() {
    // Init selected state dari old value
    const checked = document.querySelector('input[name="jenis_izin"]:checked');
    if (checked) selectJenis(checked.value);

    // Intercept form submit
    document.getElementById('formPengajuan').addEventListener('submit', function(e) {
        const jenis = document.querySelector('input[name="jenis_izin"]:checked');

        if (!jenis) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Jenis Perizinan Belum Dipilih',
                text: 'Silakan pilih salah satu jenis perizinan (Izin, Sakit, Cuti, atau Dinas Luar) terlebih dahulu.',
                confirmButtonColor: '#4361ee'
            });
            return false;
        }

        const mulai = document.getElementById('tgl_mulai').value;
        const selesai = document.getElementById('tgl_selesai').value;

        if (!mulai || !selesai) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal Belum Lengkap',
                text: 'Silakan isi Tanggal Mulai dan Tanggal Selesai.',
                confirmButtonColor: '#4361ee'
            });
            return false;
        }

        const d1 = new Date(mulai), d2 = new Date(selesai);
        if (d2 < d1) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.',
                confirmButtonColor: '#d33'
            });
            return false;
        }

        // Cek kuota cuti di client side
        const sisaCuti = {{ $sisaCuti ?? 999 }};
        if (jenis.value === 'cuti' && sisaCuti < 999) {
            const diff = Math.round((d2 - d1) / 86400000) + 1;
            if (diff > sisaCuti) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Kuota Tidak Mencukupi',
                    html: `Sisa jatah cuti Anda tahun ini adalah <strong>${sisaCuti} hari</strong>, namun Anda mengajukan <strong>${diff} hari</strong>.`,
                    confirmButtonColor: '#d33'
                });
                return false;
            }
        }

        // Semua valid, tampilkan loading
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';
    });
});
</script>
<style>
.jenis-card:hover {
    background: #f0f7ff !important;
    border-color: #007bff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,.15) !important;
}
</style>
@endpush
@endsection
