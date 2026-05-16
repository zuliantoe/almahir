@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" dismissible />
    @endif
    @if(session('error'))
        <x-alert type="danger" :message="session('error')" dismissible />
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #4895ef 100%);">
                <div class="card-body p-4 text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="font-weight-bold mb-1"><i class="fas fa-calendar-check mr-2"></i> Daftar Presensi</h3>
                        <p class="mb-0 opacity-75">Rekapitulasi kehadiran santri secara real-time</p>
                    </div>
                    <div class="ml-auto text-right">
                        <div class="badge badge-light p-2 mb-2 shadow-sm text-primary" style="border-radius: 10px; font-weight: 800;">
                            <i class="fas fa-calendar-check mr-1"></i> TA: {{ $activeTahunAjaran->tahunajaran ?? '-' }}
                        </div>
                        <br>
                        <div class="bg-white px-4 py-2 text-primary font-weight-bold shadow-sm" style="border-radius: 50px; opacity: 0.9; display: inline-block;">
                            <i class="fas fa-chart-pie mr-1"></i> Mode Rekapan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Summary --}}
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100" style="border-radius: 15px; background: #fff;">
                <div class="card-body p-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-0">{{ $stats['Hadir'] }}</h3>
                    <p class="text-muted small mb-0">Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100" style="border-radius: 15px; background: #fff;">
                <div class="card-body p-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255, 152, 0, 0.1);">
                        <i class="fas fa-user-clock text-warning"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-0">{{ $stats['Telat'] }}</h3>
                    <p class="text-muted small mb-0">Telat</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100" style="border-radius: 15px; background: #fff;">
                <div class="card-body p-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-hand-paper text-warning"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-0">{{ $stats['Izin'] }}</h3>
                    <p class="text-muted small mb-0">Izin</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100" style="border-radius: 15px; background: #fff;">
                <div class="card-body p-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-heartbeat text-info"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-0">{{ $stats['Sakit'] }}</h3>
                    <p class="text-muted small mb-0">Sakit</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100" style="border-radius: 15px; background: #fff;">
                <div class="card-body p-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(220, 53, 69, 0.1);">
                        <i class="fas fa-times text-danger"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-0">{{ $stats['Alpha'] }}</h3>
                    <p class="text-muted small mb-0">Alpha</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-12 mb-3">
            <div class="card border-0 shadow-sm text-center h-100" style="border-radius: 15px; background: linear-gradient(135deg, #1e1e2d 0%, #33334d 100%);">
                <div class="card-body p-3 text-white d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <p class="mb-0 opacity-75 small">Total</p>
                        <h2 class="font-weight-bold mb-0">{{ $stats['total'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-filter mr-2 text-primary"></i> Filter Presensi</h5>
                <button type="button" class="btn btn-light btn-sm" data-toggle="collapse" data-target="#filterBody">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div id="filterBody" class="collapse show">
            <div class="card-body pt-0">
                <form method="GET" class="row">
                    @if(auth()->user()->ref_type !== \Modules\Siswa\Models\Siswa::class)
                    <div class="col-md-2 mb-2">
                        <select name="rombel_id" class="form-control select2-modern">
                            <option value="">Semua Rombel</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>{{ $rombel->nama_rombel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="mapel_id" class="form-control select2-modern">
                            <option value="">Semua Mapel</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2 mb-2">
                        <select name="status" class="form-control select2-modern">
                            <option value="">Semua Status</option>
                            <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="Telat" {{ request('status') == 'Telat' ? 'selected' : '' }}>Telat</option>
                            <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                            <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Alpha" {{ request('status') == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="kategori" class="form-control select2-modern">
                            <option value="">Semua Kategori</option>
                            <option value="Sekolah" {{ request('kategori') == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                            <option value="Pengajian" {{ request('kategori') == 'Pengajian' ? 'selected' : '' }}>Pengajian</option>
                            <option value="Ekstrakurikuler" {{ request('kategori') == 'Ekstrakurikuler' ? 'selected' : '' }}>Ekstrakurikuler</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}" placeholder="Tanggal">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th class="border-0 px-4">Siswa</th>
                            <th class="border-0">Mata Pelajaran</th>
                            <th class="border-0">Jam</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Kategori</th>
                            <th class="border-0 px-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensis as $item)
                        <tr>
                            <td class="px-4">
                                <div class="font-weight-bold text-dark">{{ $item->siswa->nama ?? '-' }}</div>
                                @php
                                    $badgeClass = 'badge-success';
                                    if($item->status == 'Telat') $badgeClass = 'badge-warning';
                                    if($item->status == 'Izin') $badgeClass = 'badge-info';
                                    if($item->status == 'Sakit') $badgeClass = 'badge-primary';
                                    if($item->status == 'Alpha') $badgeClass = 'badge-danger';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2" style="border-radius: 8px;">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark font-weight-500">{{ $item->mataPelajaran->nama ?? '-' }}</div>
                                <small class="text-muted">{{ $item->guru->nama ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-light px-2 py-1"><i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}</span>
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'Hadir' => 'badge-success',
                                        'Izin' => 'badge-warning',
                                        'Sakit' => 'badge-info',
                                        'Alpha' => 'badge-danger',
                                    ];
                                    $class = $statusClasses[$item->status] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $class }} px-3 py-1" style="border-radius: 6px; min-width: 70px;">{{ $item->status }}</span>
                            </td>
                            <td><span class="text-muted small font-weight-bold">{{ strtoupper($item->kategori) }}</span></td>
                            <td class="px-4">
                                <div class="text-dark" style="font-size: 0.85rem;">{{ $item->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-20"></i>
                                <p class="text-muted">Belum ada data presensi siswa hari ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Menampilkan <strong>{{ $presensis->count() }}</strong> data</span>
                {{ $presensis->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function hapusData(url) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        document.getElementById('deleteForm').action = url;
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
