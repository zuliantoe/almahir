@extends('layouts.app')

@section('title', 'Detail Kurikulum')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Detail Kurikulum" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted small text-uppercase">Master Kurikulum</th>
                                <td><strong class="h5">{{ $kurikulum->masterKurikulum->nama_kurikulum }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase">Tingkat</th>
                                <td><span class="badge badge-primary px-3 py-2">{{ $kurikulum->tingkat->nama_tingkat }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase">Tahun Ajaran</th>
                                <td>{{ $kurikulum->tahunAjaran->tahunajaran }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase">Mata Pelajaran</th>
                                <td><strong>[{{ $kurikulum->mataPelajaran->kode }}] {{ $kurikulum->mataPelajaran->nama }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase">Kelas</th>
                                <td>{{ $kurikulum->kelas->nama_kelas }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase">Total Jam / Minggu</th>
                                <td><span class="text-info font-weight-bold">{{ $kurikulum->totaljam }} Jam</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase">KKM</th>
                                <td><span class="text-danger font-weight-bold h5 mb-0">{{ $kurikulum->kkm }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase">Terakhir Diperbarui</th>
                                <td><span class="text-muted">{{ $kurikulum->updated_at->format('d/m/Y H:i') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <x-btn :href="route('akademik.kurikulum.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.kurikulum.edit', $kurikulum->id)" class="btn-warning text-white" icon="fas fa-edit">
                        Edit Kurikulum
                    </x-btn>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
