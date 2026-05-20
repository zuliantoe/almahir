@extends('layouts.app')

@section('title', 'Detail Rombel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Rombel</h1>
                <p class="text-muted">Informasi lengkap rombongan belajar</p>
            </div>
            <x-btn :href="route('akademik.rombel.index')" icon="fas fa-arrow-left" class="btn-secondary shadow-sm">
                Kembali
            </x-btn>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <x-card title="Informasi Rombel" icon="fas fa-info-circle" type="primary" outline shadow>
                <div class="text-center mb-4 pt-3">
                    <div class="display-avatar mx-auto mb-3 shadow-sm d-flex align-items-center justify-content-center bg-soft-primary rounded-circle" style="width: 100px; height: 100px;">
                        <i class="fas fa-users-class fa-3x text-primary"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-1">{{ $rombel->nama_rombel }}</h4>
                    <span class="badge badge-pill badge-soft-primary px-3 py-2">
                        <i class="fas fa-door-open mr-1"></i> Kelas {{ $rombel->kelas->nama_kelas ?? '-' }}
                    </span>
                </div>

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                        <span class="text-muted small text-uppercase font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran</span>
                        <span class="font-weight-bold text-dark">{{ $rombel->tahunAjaran->tahunajaran ?? '-' }}</span>
                    </div>

                    <div class="list-group-item d-flex flex-column px-0 bg-transparent py-3">
                        <span class="text-muted small text-uppercase font-weight-bold mb-2"><i class="fas fa-user-tie mr-2"></i> Wali Kelas</span>
                        <div class="d-flex align-items-center p-2 rounded bg-light border shadow-sm">
                            <div class="avatar avatar-sm mr-3 bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold text-dark text-truncate" style="max-width: 150px;">{{ $rombel->walikelas->nama ?? 'Belum ditentukan' }}</span>
                                <small class="text-muted">NIP: {{ $rombel->walikelas->nip ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item d-flex flex-column px-0 bg-transparent border-0">
                        <span class="text-muted small text-uppercase font-weight-bold mb-1"><i class="fas fa-comment-alt mr-2"></i> Keterangan</span>
                        <p class="text-dark font-italic small mb-0">{{ $rombel->keterangan ?: 'Tidak ada keterangan tambahan.' }}</p>
                    </div>
                </div>

                <hr class="my-4">
                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                <x-btn :href="route('akademik.rombel.edit', $rombel->id)" class="btn-warning btn-block shadow-sm text-white font-weight-bold" style="border-radius: 25px;">
                    <i class="fas fa-edit mr-2"></i>Edit Informasi
                </x-btn>
                @endif
            </x-card>
        </div>

<style>
    .bg-soft-primary { background-color: rgba(0, 123, 255, 0.1); }
    .badge-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; border: 1px solid rgba(0, 123, 255, 0.2); }
    .list-group-item { border-color: rgba(0,0,0,.05); }
</style>

        <div class="col-lg-8">
            <x-card title="Daftar Siswa & Riwayat Perjalanan" icon="fas fa-user-graduate" type="info" outline>
                <ul class="nav nav-tabs mb-3" id="studentTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="active-tab" data-toggle="tab" href="#active" role="tab">
                            <i class="fas fa-check-circle mr-1 text-success"></i> Siswa Saat Ini 
                            <span class="badge badge-success ml-1">{{ $rombel->riwayatSiswa->where('status', 'aktif')->where('tahunajaran_id', $rombel->tahunajaran_id)->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="journey-tab" data-toggle="tab" href="#journey" role="tab">
                            <i class="fas fa-route mr-1 text-primary"></i> Perjalanan Rombel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="history-tab" data-toggle="tab" href="#history" role="tab">
                            <i class="fas fa-history mr-1 text-muted"></i> Log Alumni/Mutasi
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="studentTabContent">
                    {{-- Tab Siswa Saat Ini --}}
                    <div class="tab-pane fade show active" id="active" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th class="text-center">L/P</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $currentStudents = $rombel->riwayatSiswa
                                            ->where('status', 'aktif')
                                            ->where('tahunajaran_id', $rombel->tahunajaran_id);
                                        $activeCount = 0; 
                                    @endphp
                                    @foreach($currentStudents as $rs)
                                        @php $activeCount++; @endphp
                                        <tr>
                                            <td class="text-center">{{ $activeCount }}</td>
                                            <td><code>{{ $rs->siswa->nis }}</code></td>
                                            <td class="font-weight-bold text-primary">{{ $rs->siswa->nama }}</td>
                                            <td class="text-center">{{ $rs->siswa->jenis_kelamin }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-success">Aktif</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($activeCount == 0)
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted italic small">Tidak ada siswa aktif di periode berjalan.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab Perjalanan Rombel --}}
                    <div class="tab-pane fade" id="journey" role="tabpanel">
                        <div class="timeline timeline-inverse">
                            @php
                                $groupedHistory = $rombel->riwayatSiswa->groupBy('tahunajaran_id')->sortByDesc(function($items, $key) {
                                    return $key;
                                });
                            @endphp

                            @foreach($groupedHistory as $taId => $items)
                                @php $ta = $items->first()->tahunAjaran; @endphp
                                <div class="time-label">
                                    <span class="bg-info px-3">
                                        {{ $ta->tahunajaran ?? 'Unknown TA' }}
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-users bg-primary"></i>
                                    <div class="timeline-item shadow-sm border">
                                        <h3 class="timeline-header font-weight-bold">
                                            Kelas: <span class="text-primary">{{ $items->first()->kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                                            <span class="float-right text-muted small">{{ $items->count() }} Siswa Terdaftar</span>
                                        </h3>
                                        <div class="timeline-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-valign-middle mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="pl-3">Siswa</th>
                                                            <th class="text-center">Status Akhir Periode</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($items->take(5) as $item)
                                                            <tr>
                                                                <td class="pl-3">{{ $item->siswa->nama }}</td>
                                                                <td class="text-center">
                                                                    @if($item->status == 'aktif')
                                                                        <span class="badge badge-success">Aktif</span>
                                                                    @elseif($item->status == 'naik')
                                                                        <span class="badge badge-warning">Naik Kelas</span>
                                                                    @elseif($item->status == 'lulus')
                                                                        <span class="badge badge-primary">Lulus</span>
                                                                    @else
                                                                        <span class="badge badge-secondary">{{ $item->status }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        @if($items->count() > 5)
                                                            <tr>
                                                                <td colspan="2" class="text-center small text-muted">... dan {{ $items->count() - 5 }} siswa lainnya</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Tab Log Alumni/Mutasi --}}
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Tahun Lulus/Keluar</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $alumni = $rombel->riwayatSiswa->whereIn('status', ['lulus', 'keluar']);
                                        $historyCount = 0; 
                                    @endphp
                                    @foreach($alumni as $rs)
                                        @php $historyCount++; @endphp
                                        <tr class="text-muted bg-light">
                                            <td class="text-center">{{ $historyCount }}</td>
                                            <td>{{ $rs->siswa->nama }}</td>
                                            <td>{{ $rs->tahunAjaran->tahunajaran ?? '-' }}</td>
                                            <td class="text-center">
                                                @if($rs->status == 'lulus')
                                                    <span class="badge badge-primary px-2"><i class="fas fa-graduation-cap mr-1"></i> Lulus</span>
                                                @else
                                                    <span class="badge badge-danger px-2"><i class="fas fa-door-open mr-1"></i> Keluar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($historyCount == 0)
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted italic small">Belum ada data alumni atau siswa keluar.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
