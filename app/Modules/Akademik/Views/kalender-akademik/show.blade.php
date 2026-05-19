@extends('layouts.app')

@section('title', 'Detail Kegiatan Akademik')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Detail Kegiatan Akademik" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted small text-uppercase font-weight-bold">Tahun Ajaran & Semester</th>
                                <td><strong>{{ $kalenderAkademik->tahunAjaran->tahunajaran }} ({{ $kalenderAkademik->semester }})</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Jenis Kegiatan</th>
                                <td><span class="badge badge-info px-3 py-2">{{ $kalenderAkademik->jenisKegiatan->jeniskegiatan }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Nama Kegiatan</th>
                                <td><strong class="h5">{{ $kalenderAkademik->nama_kegiatan }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Rentang Waktu</th>
                                <td>
                                    <span class="text-dark">
                                        <i class="far fa-calendar-alt text-primary mr-1"></i> {{ date('d/m/Y', strtotime($kalenderAkademik->tanggal_awal)) }} 
                                        <span class="mx-2 text-muted">sampai</span> 
                                        <i class="far fa-calendar-check text-success mr-1"></i> {{ date('d/m/Y', strtotime($kalenderAkademik->tanggal_akhir)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Deskripsi</th>
                                <td>
                                    <div class="p-2 bg-light rounded shadow-sm" style="min-height: 50px;">
                                        {!! nl2br(e($kalenderAkademik->deskripsi)) ?: '<em class="text-muted">Tidak ada deskripsi</em>' !!}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Terakhir Diperbarui</th>
                                <td><span class="text-muted">{{ $kalenderAkademik->updated_at->format('d/m/Y H:i') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <x-btn :href="route('akademik.kalender-akademik.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.kalender-akademik.edit', $kalenderAkademik->id)" class="btn-warning text-white" icon="fas fa-edit">
                        Edit Kegiatan
                    </x-btn>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
