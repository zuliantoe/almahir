@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Presensi" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.presensi.store') }}" method="POST">
            @csrf

            {{-- Scan Kartu Section --}}
            <div class="alert alert-info mb-4">
                <i class="fas fa-barcode mr-2"></i>
                <strong>Scan Kartu Siswa</strong> - Arahkan kartu ke scanner untuk mengisi data siswa secara otomatis
            </div>

            <div class="form-group">
                <label for="scan_id">
                    <i class="fas fa-barcode mr-1"></i> Scan ID / NIS
                </label>
                <input type="text" 
                       name="scan_id" 
                       id="scan_id" 
                       class="form-control form-control-lg" 
                       placeholder="Arahkan kartu atau masukkan NIS..."
                       autofocus>
                <small class="form-text text-muted">Fokus pada field ini dan scan kartu siswa</small>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kelas_id">Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $kelasItem)
                                <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Metode Input</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary active w-50">
                                <input type="radio" name="input_type" value="single" checked> Per Siswa
                            </label>
                            <label class="btn btn-outline-primary w-50">
                                <input type="radio" name="input_type" value="bulk"> Masal (Satu Kelas)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Siswa Section (Single) --}}
            <div id="single-input-container">
            <div class="form-group">
                <label for="id_siswa">Siswa</label>
                <select name="id_siswa" id="id_siswa" class="form-control" required>
                    <option value="">-- Pilih Kelas terlebih dahulu --</option>
                </select>
                <div id="siswa_info" class="alert alert-success mt-2" style="display: none;">
                    <small id="siswa_nama"></small>
                </div>
            </div>

            {{-- Guru Section --}}
            <div class="form-group">
                <label for="id_guru">Guru</label>
                <select name="id_guru" id="id_guru" class="form-control" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mata Pelajaran Section --}}
            <div class="form-group">
                <label for="id_mapel">Mata Pelajaran</label>
                <select name="id_mapel" id="id_mapel" class="form-control" required>
                    <option value="">-- Pilih Guru terlebih dahulu --</option>
                </select>
            </div>

            {{-- Jadwal Pelajaran Section --}}
            <div class="form-group">
                <label for="id_jadwal_pelajaran">Jadwal Pelajaran</label>
                <select name="id_jadwal_pelajaran" id="id_jadwal_pelajaran" class="form-control" required>
                    <option value="">-- Pilih Guru dan Kelas terlebih dahulu --</option>
                </select>
            </div>

            {{-- Jam Section --}}
            <div class="form-group">
                <label for="jam">Jam</label>
                <input type="time" name="jam" id="jam" class="form-control" value="{{ date('H:i') }}" required>
            </div>

                {{-- Status Section --}}
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Hadir">✓ Hadir</option>
                        <option value="Izin">⊘ Izin</option>
                        <option value="Sakit">✕ Sakit</option>
                        <option value="Alpha">✗ Alpha</option>
                    </select>
                </div>
            </div>

            {{-- Bulk Input Table (Hidden by default) --}}
            <div id="bulk-input-container" class="d-none">
                <div class="table-responsive bg-white rounded shadow-sm mb-4">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Santri</th>
                                <th width="300">
                                    Status 
                                    <button type="button" id="btn-hadir-semua" class="btn btn-xs btn-outline-success ml-2">Hadir Semua</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="bulk-siswa-body">
                            {{-- Populated by JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Kategori Section --}}
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Sekolah">Sekolah</option>
                    <option value="Pengajian">Pengajian</option>
                    <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                </select>
            </div>

            {{-- Submit Section --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save mr-1"></i> Simpan Presensi
                </button>
                <a href="{{ route('penilaiandanpresensi.presensi.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('scan_id').addEventListener('change', function() {
    const scanId = this.value.trim();
    
    if (!scanId) return;
    
    // Disable input while processing
    this.disabled = true;
    
    fetch('{{ route("penilaiandanpresensi.presensi.scan-card") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ scan_id: scanId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Set siswa value
            document.getElementById('id_siswa').value = data.data.id_siswa;
            
            // Show success message
            document.getElementById('siswa_info').style.display = 'block';
            document.getElementById('siswa_nama').textContent = '✓ Siswa ditemukan: ' + data.data.nama_siswa;
            
            // Focus to next field
            document.getElementById('id_guru').focus();
        } else {
            alert('❌ ' + data.message);
            this.value = '';
            this.focus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat membaca kartu');
        this.value = '';
        this.focus();
    })
    .finally(() => {
        this.disabled = false;
    });
});

(function() {
    const siswasData = @json($siswas);
    const jadwalsData = @json($jadwals);

    function getStudentOptions() {
        const kelasId = document.getElementById('kelas_id').value;
        if (!kelasId) {
            return '<option value="">-- Pilih Kelas terlebih dahulu --</option>';
        }

        const filteredSiswas = siswasData.filter(siswa => siswa.kelas_id == kelasId);
        if (filteredSiswas.length === 0) {
            return '<option value="">-- Tidak ada siswa di kelas ini --</option>';
        }

        let options = '<option value="">-- Pilih Siswa --</option>';
        filteredSiswas.forEach(siswa => {
            options += `<option value="${siswa.id}">${siswa.nama}</option>`;
        });
        return options;
    }

    function getMapelOptions() {
        const guruId = document.getElementById('id_guru').value;
        if (!guruId) {
            return '<option value="">-- Pilih Guru terlebih dahulu --</option>';
        }

        const usedMapelIds = new Set();
        let options = '<option value="">-- Pilih Mata Pelajaran --</option>';

        jadwalsData.forEach(jadwal => {
            if (jadwal.guru_id == guruId && jadwal.mata_pelajaran && jadwal.mata_pelajaran.id && !usedMapelIds.has(jadwal.mata_pelajaran.id)) {
                usedMapelIds.add(jadwal.mata_pelajaran.id);
                options += `<option value="${jadwal.mata_pelajaran.id}">${jadwal.mata_pelajaran.nama}</option>`;
            }
        });

        if (usedMapelIds.size === 0) {
            return '<option value="">-- Guru belum mengajar mata pelajaran apa pun --</option>';
        }

        return options;
    }

    function getJadwalOptions() {
        const kelasId = document.getElementById('kelas_id').value;
        const guruId = document.getElementById('id_guru').value;
        if (!kelasId || !guruId) {
            return '<option value="">-- Pilih kelas dan guru terlebih dahulu --</option>';
        }

        const filtered = jadwalsData.filter(jadwal => jadwal.kelas_id == kelasId && jadwal.guru_id == guruId);
        if (filtered.length === 0) {
            return '<option value="">-- Tidak ada jadwal untuk kelas dan guru ini --</option>';
        }

        let options = '<option value="">-- Pilih Jadwal Pelajaran --</option>';
        filtered.forEach(jadwal => {
            const time = `${jadwal.hari} - ${jadwal.jamawal.substring(0,5)} - ${jadwal.jamakhir.substring(0,5)}`;
            options += `<option value="${jadwal.id}">${time}</option>`;
        });
        return options;
    }

    function updateStudentOptions() {
        const siswa = document.getElementById('id_siswa');
        const currentValue = siswa.value;
        siswa.innerHTML = getStudentOptions();
        if (currentValue) {
            siswa.value = currentValue;
        }
    }

    function updateMapelOptions() {
        const mapel = document.getElementById('id_mapel');
        const currentValue = mapel.value;
        mapel.innerHTML = getMapelOptions();
        if (currentValue) {
            mapel.value = currentValue;
        }
    }

    function updateJadwalOptions() {
        const jadwal = document.getElementById('id_jadwal_pelajaran');
        const currentValue = jadwal.value;
        jadwal.innerHTML = getJadwalOptions();
        if (currentValue) {
            jadwal.value = currentValue;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateStudentOptions();
        updateMapelOptions();
        updateJadwalOptions();

        document.getElementById('kelas_id').addEventListener('change', function() {
            updateStudentOptions();
            updateJadwalOptions();
            if ($('input[name="input_type"]:checked').val() === 'bulk') {
                populateBulkTable();
            }
        });
        document.getElementById('id_guru').addEventListener('change', function() {
            updateMapelOptions();
            updateJadwalOptions();
        });

        $('input[name="input_type"]').on('change', function() {
            if ($(this).val() === 'bulk') {
                $('#single-input-container').addClass('d-none');
                $('#bulk-input-container').removeClass('d-none');
                $('#id_siswa, #status').prop('required', false);
                populateBulkTable();
            } else {
                $('#single-input-container').removeClass('d-none');
                $('#bulk-input-container').addClass('d-none');
                $('#id_siswa, #status').prop('required', true);
            }
        });

        function populateBulkTable() {
            const kelasId = document.getElementById('kelas_id').value;
            const body = document.getElementById('bulk-siswa-body');
            body.innerHTML = '';

            if (!kelasId) return;

            const filtered = siswasData.filter(s => s.kelas_id == kelasId);
            filtered.forEach((siswa, index) => {
                body.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="font-weight-bold">${siswa.nama}</div>
                            <input type="hidden" name="bulk_penilaian[${index}][id_siswa]" value="${siswa.id}">
                        </td>
                        <td>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-success btn-sm w-25 active">
                                    <input type="radio" name="bulk_penilaian[${index}][status]" value="Hadir" checked> H
                                </label>
                                <label class="btn btn-outline-warning btn-sm w-25">
                                    <input type="radio" name="bulk_penilaian[${index}][status]" value="Izin"> I
                                </label>
                                <label class="btn btn-outline-info btn-sm w-25">
                                    <input type="radio" name="bulk_penilaian[${index}][status]" value="Sakit"> S
                                </label>
                                <label class="btn btn-outline-danger btn-sm w-25">
                                    <input type="radio" name="bulk_penilaian[${index}][status]" value="Alpha"> A
                                </label>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        $('#btn-hadir-semua').on('click', function() {
            $('input[value="Hadir"]').closest('label').click();
        });
    });
})();
</script>
@endpush

@push('styles')
<style>
    .form-control {
        border-radius: 0.5rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }
    .btn {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    label {
        font-weight: 600;
        color: #495057;
    }
    .alert-info {
        border-left: 4px solid #117a8b;
        border-radius: 0.5rem;
    }
</style>
@endpush
