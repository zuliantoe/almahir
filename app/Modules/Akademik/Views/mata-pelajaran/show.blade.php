@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-modern">
                <div class="card-header bg-gradient-blue">
                    <h3 class="card-title text-white"><i class="fas fa-info-circle mr-2"></i> Detail Mata Pelajaran</h3>
                </div>
                
                <div class="card-body">
                    <table class="table table-borderless table-striped">
                        <tbody>
                            <tr>
                                <th width="35%" class="text-muted">Kode Mata Pelajaran</th>
                                <td><span class="badge badge-light badge-modern h6 mb-0">{{ $mataPelajaran->kode }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Nama Mata Pelajaran</th>
                                <td class="font-weight-bold text-dark h5">{{ $mataPelajaran->nama }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Kategori</th>
                                <td>
                                    @if(isset($mataPelajaran->kategori->kategori))
                                        <span class="badge badge-info badge-modern">{{ $mataPelajaran->kategori->kategori }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white text-right">
                    <a href="{{ route('akademik.mata-pelajaran.index') }}" class="btn btn-secondary btn-modern mr-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('akademik.mata-pelajaran.edit', $mataPelajaran) }}" class="btn btn-warning btn-modern text-white">
                        <i class="fas fa-edit"></i> Edit Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
