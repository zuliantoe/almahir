@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0">Edit Kelas</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('akademik.kelas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card --}}
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit mr-1"></i>
                Form Edit Kelas
            </h3>
        </div>

        <form action="{{ route('akademik.kelas.update', $kelas->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="form-group">
                    <label>Nama Kelas</label>
                    <input type="text"
                           name="nama"
                           class="form-control"
                           value="{{ old('nama', $kelas->nama) }}"
                           required>
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('akademik.kelas.index') }}"
                   class="btn btn-default">
                    Batal
                </a>

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
