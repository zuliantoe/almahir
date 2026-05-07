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
            <x-card title="Informasi Utama" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th class="text-muted small text-uppercase" width="40%">Nama Rombel</th>
                            <td class="font-weight-bold">: {{ $rombel->nama_rombel }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Kelas</th>
                            <td>: {{ $rombel->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Tahun Ajaran</th>
                            <td>: {{ $rombel->tahunAjaran->tahunajaran ?? '-' }} ({{ $rombel->tahunAjaran->semester ?? '-' }})</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Wali Kelas</th>
                            <td>: {{ $rombel->walikelas->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase" width="40%">Jumlah Siswa</th>
                            <td>
                                : <span class="font-weight-bold text-primary">{{ $rombel->riwayatSiswa->where('status', 'aktif')->count() }} Aktif</span> 
                                <span class="text-muted small">/ {{ $rombel->riwayatSiswa->count() }} Total</span>
                            </td>
                        </tr>
                    </table>
                </div>
                @if($rombel->keterangan)
                    <hr>
                    <label class="text-muted small text-uppercase font-weight-bold">Keterangan:</label>
                    <p class="mb-0 small">{{ $rombel->keterangan }}</p>
                @endif
                
                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                <div class="mt-4">
                    <x-btn :href="route('akademik.rombel.edit', $rombel->id)" class="btn-warning btn-block text-white" icon="fas fa-edit">
                        Edit Rombel
                    </x-btn>
                </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card title="Daftar Siswa & Riwayat" icon="fas fa-user-graduate" type="info" outline>
                <ul class="nav nav-tabs mb-3" id="studentTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="active-tab" data-toggle="tab" href="#active" role="tab">
                            <i class="fas fa-check-circle mr-1 text-success"></i> Siswa Aktif 
                            <span class="badge badge-success ml-1">{{ $rombel->riwayatSiswa->where('status', 'aktif')->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="history-tab" data-toggle="tab" href="#history" role="tab">
                            <i class="fas fa-history mr-1 text-muted"></i> Riwayat 
                            <span class="badge badge-secondary ml-1">{{ $rombel->riwayatSiswa->where('status', '!=', 'aktif')->count() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="studentTabContent">
                    {{-- Tab Siswa Aktif --}}
                    <div class="tab-pane fade show active" id="active" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th class="text-center">L/P</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $activeCount = 0; @endphp
                                    @foreach($rombel->riwayatSiswa->where('status', 'aktif') as $rs)
                                        @php $activeCount++; @endphp
                                        <tr>
                                            <td class="text-center">{{ $activeCount }}</td>
                                            <td><code>{{ $rs->siswa->nis }}</code></td>
                                            <td class="font-weight-bold text-primary">{{ $rs->siswa->nama }}</td>
                                            <td class="text-center">{{ $rs->siswa->jenis_kelamin }}</td>
                                        </tr>
                                    @endforeach
                                    @if($activeCount == 0)
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted italic small">Tidak ada siswa aktif di rombel ini.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab Riwayat --}}
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $historyCount = 0; @endphp
                                    @foreach($rombel->riwayatSiswa->where('status', '!=', 'aktif') as $rs)
                                        @php $historyCount++; @endphp
                                        <tr class="text-muted bg-light">
                                            <td class="text-center">{{ $historyCount }}</td>
                                            <td><code>{{ $rs->siswa->nis }}</code></td>
                                            <td>{{ $rs->siswa->nama }}</td>
                                            <td class="text-center">
                                                @if($rs->status == 'lulus')
                                                    <span class="badge badge-primary px-2"><i class="fas fa-graduation-cap mr-1"></i> Lulus</span>
                                                @elseif($rs->status == 'naik')
                                                    <span class="badge badge-warning px-2"><i class="fas fa-arrow-up mr-1"></i> Naik Kelas</span>
                                                @else
                                                    <span class="badge badge-secondary px-2">{{ ucfirst($rs->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($historyCount == 0)
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted italic small">Belum ada data riwayat (lulus/naik).</td>
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
