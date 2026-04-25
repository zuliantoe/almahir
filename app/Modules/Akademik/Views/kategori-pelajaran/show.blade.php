@extends('layouts.app')

@section('title', 'Detail Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Detail Kategori Pelajaran" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted">Nama Kategori</th>
                                <td><strong>{{ $kategoriPelajaran->kategori }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Total Mata Pelajaran</th>
                                <td>
                                    <span class="badge badge-info px-3 py-2">
                                        {{ $kategoriPelajaran->mata_pelajaran_count ?? $kategoriPelajaran->mataPelajaran->count() }} Mata Pelajaran
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <hr>

                <div class="d-flex justify-content-end">
                    <x-btn :href="route('akademik.kategori-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.kategori-pelajaran.edit', $kategoriPelajaran->id)" class="btn-warning text-white" icon="fas fa-edit">
                        Edit Data
                    </x-btn>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
