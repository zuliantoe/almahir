@extends('layouts.app')

@section('title', 'Detail Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Detail Jadwal Pelajaran" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted small text-uppercase font-weight-bold">Rombongan Belajar</th>
                                <td><strong>{{ optional($jadwalPelajaran->rombel)->nama_rombel ?? '-' }} ({{ optional(optional($jadwalPelajaran->rombel)->kelas)->nama_kelas ?? '-' }})</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Hari</th>
                                <td><span class="badge badge-primary px-3 py-2 h6 mb-0">{{ $jadwalPelajaran->hari }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Mata Pelajaran</th>
                                <td><strong>[{{ optional($jadwalPelajaran->mataPelajaran)->kode ?? '-' }}] {{ optional($jadwalPelajaran->mataPelajaran)->nama ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Guru Pengajar</th>
                                <td>{{ optional($jadwalPelajaran->guru)->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Jam Pelajaran</th>
                                <td>
                                    <span class="text-info font-weight-bold">Jam Ke-{{ $jadwalPelajaran->jamke }}</span> 
                                    <span class="text-muted ml-2">({{ substr($jadwalPelajaran->jamawal, 0, 5) }} - {{ substr($jadwalPelajaran->jamakhir, 0, 5) }})</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Terakhir Diperbarui</th>
                                <td><span class="text-muted">{{ $jadwalPelajaran->updated_at->format('d/m/Y H:i') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <x-btn :href="route('akademik.jadwal-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.jadwal-pelajaran.edit', $jadwalPelajaran->id)" class="btn-warning text-white" icon="fas fa-edit">
                        Edit Data
                    </x-btn>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
