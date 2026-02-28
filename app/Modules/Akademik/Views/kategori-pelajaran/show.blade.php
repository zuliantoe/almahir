@extends('layouts.app')

@section('title', 'Detail Kategori Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Kategori Pelajaran</h3>
                    <div class="card-tools">
                        <span class="badge badge-info">
                            {{ $kategoriPelajaran->mata_pelajaran_count ?? $kategoriPelajaran->mataPelajaran->count() }} Mata Pelajaran
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <p class="form-control-static font-weight-bold">
                            {{ $kategoriPelajaran->kategori }}
                        </p>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('akademik.kategori-pelajaran.edit', $kategoriPelajaran->id) }}"
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('akademik.kategori-pelajaran.index') }}"
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
