@extends('layouts.app')

@section('title', $title)

@section('content')
<style>
    .glass-panel-card {
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .metric-card {
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.04) !important;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        transition: all 0.3s ease;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }
    .input-icon-wrapper {
        position: relative;
    }
    .input-icon-wrapper i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
        font-size: 0.9rem;
    }
    .input-icon-wrapper .form-control {
        padding-left: 42px !important;
    }
    .table-alpa-row {
        background-color: rgba(220, 53, 69, 0.03) !important;
    }
    .table-alpa-row:hover {
        background-color: rgba(220, 53, 69, 0.05) !important;
    }
    .employee-avatar {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid py-3">

    {{-- Stats Cards --}}
    <div class="row mb-2">
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="card metric-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #17a2b8 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold" style="letter-spacing: 0.5px;">Total Pegawai</span>
                        <div class="h2 font-weight-bolder mb-0 text-dark mt-1" style="font-family: 'Outfit', sans-serif;">{{ $stats['total'] }}</div>
                    </div>
                    <div class="rounded-circle p-3 text-info d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; background: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="card metric-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #28a745 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold" style="letter-spacing: 0.5px;">Hadir Fisik</span>
                        <div class="h2 font-weight-bolder mb-0 text-success mt-1" style="font-family: 'Outfit', sans-serif;">{{ $stats['hadir'] }}</div>
                    </div>
                    <div class="rounded-circle p-3 text-success d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; background: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="card metric-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #ffc107 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold" style="letter-spacing: 0.5px;">Izin / Sakit</span>
                        <div class="h2 font-weight-bolder mb-0 text-warning mt-1" style="font-family: 'Outfit', sans-serif;">{{ $stats['izin'] }}</div>
                    </div>
                    <div class="rounded-circle p-3 text-warning d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; background: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-envelope-open-text fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 mb-4">
            <div class="card metric-card hover-elevate p-3 border-0 h-100" style="border-left: 5px solid #dc3545 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small font-weight-bold" style="letter-spacing: 0.5px;">Tanpa Keterangan</span>
                        <div class="h2 font-weight-bolder mb-0 text-danger mt-1" style="font-family: 'Outfit', sans-serif;">{{ $stats['alpa'] }}</div>
                    </div>
                    <div class="rounded-circle p-3 text-danger d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; background: rgba(220, 53, 69, 0.1);">
                        <i class="fas fa-user-times fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="card border-0 shadow-sm glass-panel-card" style="overflow: hidden;">
        <div class="card-header border-0 p-4 d-flex flex-wrap justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
            <h4 class="card-title text-white font-weight-bold mb-0 py-1">
                <i class="fas fa-calendar-alt mr-2"></i> Daftar Kehadiran: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
            </h4>
            <div class="card-tools py-1">
                <a href="{{ route('absensi.manage.export', ['date' => $selectedDate, 'search' => request('search')]) }}" class="btn btn-light text-success btn-sm rounded-pill px-4 shadow-sm btn-animate font-weight-bold py-2">
                    <i class="fas fa-file-excel mr-1"></i> Export Laporan (CSV)
                </a>
            </div>
        </div>
        
        <div class="card-body p-4 bg-light-gradient">
        
            {{-- Filters Panel --}}
            <div class="card border-0 p-3 mb-4 shadow-sm" style="border-radius: 12px; background: white;">
                <form action="{{ route('absensi.manage.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="form-group mb-0 text-left">
                                <label class="small font-weight-bold text-muted mb-2"><i class="fas fa-calendar-day mr-1"></i> Tanggal Pantauan</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" name="date" class="form-control border-premium" value="{{ $selectedDate }}" onchange="this.form.submit()" style="border-radius: 10px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="form-group mb-0 text-left">
                                <label class="small font-weight-bold text-muted mb-2"><i class="fas fa-user mr-1"></i> Cari Nama Pegawai</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="search" class="form-control border-premium" placeholder="Ketik nama untuk mencari..." value="{{ request('search') }}" style="border-radius: 10px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-animate btn-block rounded-pill shadow-sm py-2 font-weight-bold">
                                <i class="fas fa-filter mr-1"></i> Cari & Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table List --}}
            <div class="table-responsive bg-white rounded shadow-sm border-0" style="border-radius: 12px !important; overflow: hidden;">
                <table class="table table-premium table-hover mb-0" style="background: transparent;">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center border-0 text-muted" style="width: 70px;">No</th>
                            <th class="border-0 text-left text-muted">Pegawai</th>
                            <th class="text-center border-0 text-muted">Jam Masuk</th>
                            <th class="text-center border-0 text-muted">Jam Pulang</th>
                            <th class="text-center border-0 text-muted">Status</th>
                            <th class="text-center border-0 text-muted">Aksi / Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $index => $item)
                        <tr class="@if($item->status == 'ALPA') table-alpa-row @endif">
                            <td class="text-center align-middle">{{ $rekap->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item->pegawai->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($item->pegawai->nama).'&background=4361ee&color=fff' }}" 
                                         class="employee-avatar mr-3 border">
                                    <div class="text-left">
                                        <div class="font-weight-bold text-dark">{{ $item->pegawai->nama }}</div>
                                        <small class="text-muted">{{ $item->pegawai->typePegawai->nama_type ?? 'Pegawai' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle font-weight-bold text-dark">
                                @if($item->jam_masuk !== '-')
                                    <span class="badge badge-light border px-2 py-1" style="font-size: 0.85rem;">
                                        <i class="far fa-clock text-success mr-1"></i> {{ $item->jam_masuk }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle font-weight-bold text-dark">
                                @if($item->jam_pulang !== '-')
                                    <span class="badge badge-light border px-2 py-1" style="font-size: 0.85rem;">
                                        <i class="far fa-clock text-danger mr-1"></i> {{ $item->jam_pulang }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $badgeClass = match($item->status) {
                                        'TEPAT WAKTU', 'HADIR' => 'badge-soft-success',
                                        'TERLAMBAT' => 'badge-soft-warning',
                                        'SAKIT', 'IZIN', 'CUTI', 'DINAS LUAR' => 'badge-soft-info',
                                        default => 'badge-soft-danger'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2 shadow-sm rounded-pill" style="min-width: 100px; font-size: 0.8rem; font-weight: 700;">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center align-items-center">
                                    @if($item->izin_id)
                                        <a href="{{ route('perizinan.show', $item->izin_id) }}" class="btn btn-xs btn-outline-info rounded-pill px-3 shadow-sm mr-2 py-1 font-weight-bold">
                                            <i class="fas fa-envelope-open-text mr-1"></i> Detail Izin
                                        </a>
                                    @elseif($item->status == 'ALPA')
                                        <span class="text-danger small font-weight-bold mr-3"><i class="fas fa-user-times mr-1"></i> Tidak Hadir</span>
                                    @elseif($item->status == 'LIBUR')
                                        <span class="text-muted small font-italic mr-3">Hari Libur</span>
                                    @else
                                        <span class="text-success small font-weight-bold mr-3"><i class="fas fa-check-double mr-1"></i> Hadir Fisik</span>
                                    @endif
                                    
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 shadow-sm py-1 font-weight-bold" 
                                            onclick="openAbsenManual('{{ $item->pegawai->id }}', '{{ addslashes($item->pegawai->nama) }}', '{{ $item->jam_masuk != '-' ? $item->jam_masuk : '' }}', '{{ $item->jam_pulang != '-' ? $item->jam_pulang : '' }}', '{{ $item->status }}')">
                                        <i class="fas fa-edit mr-1"></i> Edit Absen
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted bg-white">
                                <div class="py-4">
                                    <i class="fas fa-user-clock fa-3x mb-3 opacity-30"></i>
                                    <h5 class="font-weight-bold text-dark">Data Tidak Ditemukan</h5>
                                    <p class="small text-muted mb-0">Tidak ada data pegawai yang cocok dengan kriteria pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rekap->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $rekap->links() }}
            </div>
            @endif
        
        </div>
    </div>
</div>

<!-- Modal Absensi Manual -->
<div class="modal fade" id="modalAbsenManual" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3 d-flex align-items-center">
                <h5 class="modal-title font-weight-bold mb-0"><i class="fas fa-user-edit mr-2"></i> Input / Edit Absensi Manual</h5>
                <button type="button" class="close text-white opacity-75 ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('absensi.manage.store-manual') }}" method="POST">
                @csrf
                <input type="hidden" name="pegawai_id" id="manualPegawaiId">
                <input type="hidden" name="tanggal" value="{{ $selectedDate }}">
                
                <div class="modal-body p-4 bg-light">
                    <!-- Info Pegawai Card -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: white;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px; background: rgba(67, 97, 238, 0.1);">
                                    <i class="fas fa-user-circle fa-2x"></i>
                                </div>
                                <div class="text-left">
                                    <h6 class="mb-0 font-weight-bold text-dark" id="manualNamaPegawai">Nama Pegawai</h6>
                                    <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Fields Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 14px; background: white;">
                        <div class="card-body p-3">
                            <!-- Status Kehadiran -->
                            <div class="form-group mb-3 text-left">
                                <label class="font-weight-bold text-muted small text-uppercase mb-2"><i class="fas fa-info-circle text-primary mr-1"></i> Status Kehadiran</label>
                                <select name="status" id="manualStatus" class="form-control custom-select" style="border-radius: 10px; font-weight: 500;" onchange="toggleTimeInputs(this.value)" required>
                                    <option value="TEPAT WAKTU">TEPAT WAKTU</option>
                                    <option value="TERLAMBAT">TERLAMBAT</option>
                                    <option value="SAKIT">SAKIT</option>
                                    <option value="IZIN">IZIN</option>
                                    <option value="ALPA">ALPA</option>
                                </select>
                            </div>

                            <!-- Jam Masuk & Pulang (Conditional) -->
                            <div class="row" id="timeInputsRow">
                                <div class="col-6">
                                    <div class="form-group mb-3 text-left">
                                        <label class="font-weight-bold text-muted small text-uppercase mb-2"><i class="fas fa-sign-in-alt text-success mr-1"></i> Jam Masuk</label>
                                        <input type="time" name="jam_masuk" id="manualJamMasuk" class="form-control" style="border-radius: 10px; font-weight: 500;">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group mb-3 text-left">
                                        <label class="font-weight-bold text-muted small text-uppercase mb-2"><i class="fas fa-sign-out-alt text-danger mr-1"></i> Jam Pulang</label>
                                        <input type="time" name="jam_pulang" id="manualJamPulang" class="form-control" style="border-radius: 10px; font-weight: 500;">
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan / Alasan -->
                            <div class="form-group mb-0 text-left">
                                <label class="font-weight-bold text-muted small text-uppercase mb-2"><i class="fas fa-sticky-note text-warning mr-1"></i> Keterangan / Alasan</label>
                                <textarea name="keterangan" id="manualKeterangan" class="form-control" rows="2" style="border-radius: 10px;" placeholder="Tulis alasan absensi manual/keterangan izin..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-3 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm btn-animate"><i class="fas fa-save mr-1"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: 'var(--primary-color)',
        });
    @endif

    function toggleTimeInputs(status) {
        const row = document.getElementById('timeInputsRow');
        const jamMasuk = document.getElementById('manualJamMasuk');
        const jamPulang = document.getElementById('manualJamPulang');

        if (status === 'TEPAT WAKTU' || status === 'TERLAMBAT') {
            row.style.display = 'flex';
            jamMasuk.required = true;
            if (!jamMasuk.value) {
                jamMasuk.value = status === 'TEPAT WAKTU' ? '07:30' : '08:15';
            }
            if (!jamPulang.value) {
                jamPulang.value = '16:00';
            }
        } else {
            row.style.display = 'none';
            jamMasuk.required = false;
        }
    }

    function openAbsenManual(id, nama, jamMasuk, jamPulang, status) {
        document.getElementById('manualPegawaiId').value = id;
        document.getElementById('manualNamaPegawai').innerText = nama;
        
        const statusSelect = document.getElementById('manualStatus');
        
        let currentStatus = status;
        if (status !== 'TEPAT WAKTU' && status !== 'TERLAMBAT' && status !== 'SAKIT' && status !== 'IZIN' && status !== 'ALPA') {
            currentStatus = 'TEPAT WAKTU'; // Default fallback mapping
        }
        
        statusSelect.value = currentStatus;
        
        // Clean values for time inputs
        document.getElementById('manualJamMasuk').value = jamMasuk.substring(0, 5);
        document.getElementById('manualJamPulang').value = jamPulang.substring(0, 5);
        document.getElementById('manualKeterangan').value = ''; // Reset keterangan
        
        toggleTimeInputs(currentStatus);
        
        $('#modalAbsenManual').modal('show');
    }
</script>
@endpush
