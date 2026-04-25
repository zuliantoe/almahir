@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-card title="Detail Mata Pelajaran" icon="fas fa-info-circle" type="primary" outline>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted">Kode Mata Pelajaran</th>
                                <td><span class="badge badge-light border">{{ $mataPelajaran->kode }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Nama Mata Pelajaran</th>
                                <td><strong>{{ $mataPelajaran->nama }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Kategori</th>
                               <td>
                                    @if(isset($mataPelajaran->kategori->kategori))
                                        <span class="badge badge-info">{{ $mataPelajaran->kategori->kategori }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <hr>

                <div class="d-flex justify-content-end">
                    <x-btn :href="route('akademik.mata-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                        Kembali
                    </x-btn>
                    <x-btn :href="route('akademik.mata-pelajaran.edit', $mataPelajaran)" class="btn-warning text-white" icon="fas fa-edit">
                        Edit Data
                    </x-btn>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
