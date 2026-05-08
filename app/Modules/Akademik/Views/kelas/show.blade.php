@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Detail Informasi Kelas" icon="fas fa-school" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                             <tr>
                                <th width="40%" class="text-muted small text-uppercase font-weight-bold">Nama Kelas</th>
                                <td><strong class="h5">{{ $kelas->nama_kelas }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Total Jadwal Pelajaran</th>
                                <td>
                                    <span class="badge badge-info px-3 py-2 font-weight-bold">
                                        {{ $kelas->jadwal_pelajaran_count ?? 0 }} Jadwal
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Total Struktur Kurikulum</th>
                                <td>
                                    <span class="badge badge-primary px-3 py-2 font-weight-bold">
                                        {{ $kelas->kurikulum_count ?? 0 }} Kurikulum
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Terakhir Diperbarui</th>
                                <td><span class="text-muted">{{ $kelas->updated_at->format('d/m/Y H:i') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <x-btn :href="route('akademik.kelas.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.kelas.edit', $kelas->id)" class="btn-warning text-white" icon="fas fa-edit">
                        Edit Data
                    </x-btn>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
