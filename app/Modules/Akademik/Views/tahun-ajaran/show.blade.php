@extends('layouts.app')

@section('title', 'Detail Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <x-card title="Periode Akademik" icon="fas fa-calendar-check" type="primary" outline>
                <div class="text-center py-3">
                    <h1 class="display-4 font-weight-bold mb-0 text-primary">{{ $tahunAjaran->tahunajaran }}</h1>
                    <div class="mt-2">
                        @if($tahunAjaran->status)
                            <span class="badge badge-success px-4 py-2 h5">
                                <i class="fas fa-check-circle mr-1"></i> AKTIF
                            </span>
                        @else
                            <span class="badge badge-secondary px-4 py-2 h5">
                                <i class="fas fa-times-circle mr-1"></i> TIDAK AKTIF
                            </span>
                        @endif
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-sm">
                        <tr>
                            <th class="text-muted small text-uppercase">Tahun Ajaran</th>
                            <td class="text-right font-weight-bold">{{ $tahunAjaran->tahunajaran }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Dibuat</th>
                            <td class="text-right">{{ $tahunAjaran->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small text-uppercase">Pembaruan</th>
                            <td class="text-right">{{ $tahunAjaran->updated_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <x-btn :href="route('akademik.tahun-ajaran.index')" class="btn-secondary" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.tahun-ajaran.edit', $tahunAjaran->id)" class="btn-warning text-white" icon="fas fa-edit">
                        Edit Data
                    </x-btn>
                </div>
            </x-card>
        </div>

        <div class="col-md-8">
            <div class="row">
                <div class="col-sm-6 mb-4">
                    <div class="small-box bg-info elevation-2 h-100">
                        <div class="inner">
                            <h3>{{ $tahunAjaran->kalenderAkademik()->count() }}</h3>
                            <p>Agenda Kalender Akademik</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <a href="{{ route('akademik.kalender-akademik.index', ['tahunajaran_id' => $tahunAjaran->id]) }}" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 mb-4">
                    <div class="small-box bg-success elevation-2 h-100">
                        <div class="inner">
                            <h3>{{ $tahunAjaran->jadwalPelajaran()->count() }}</h3>
                            <p>Jadwal Pelajaran Terdaftar</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span class="small-box-footer d-block p-2">Data Terintegrasi</span>
                    </div>
                </div>
                <div class="col-sm-6 mb-4">
                    <div class="small-box bg-warning elevation-2 h-100">
                        <div class="inner">
                            <h3>{{ $tahunAjaran->kurikulum()->count() }}</h3>
                            <p>Struktur Kurikulum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="small-box-footer d-block p-2 text-dark">Data Pengaturan</span>
                    </div>
                </div>
                <div class="col-sm-6 mb-4">
                    <div class="small-box bg-danger elevation-2 h-100">
                        <div class="inner">
                            <h3>{{ $tahunAjaran->rombel()->count() }}</h3>
                            <p>Rombongan Belajar</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="small-box-footer d-block p-2">Data Relasi</span>
                    </div>
                </div>
            </div>

            <x-card title="Catatan Admin" icon="fas fa-sticky-note" outline collapsible>
                <div class="p-2">
                    <p class="mb-0 text-muted">
                        Tahun ajaran ini menyimpan seluruh data transaksi akademik termasuk nilai, presensi, dan jadwal. 
                        Pastikan status aktif hanya pada satu periode berjalan untuk menjaga konsistensi data.
                    </p>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
