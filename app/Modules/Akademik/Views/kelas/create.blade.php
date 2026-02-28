@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="container-fluid">

    {{-- Content Header --}}
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0">Tambah Kelas</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('akademik.kelas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Form --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-school mr-1"></i>
                Form Tambah Kelas
            </h3>
        </div>

        <form action="{{ route('akademik.kelas.store') }}" method="POST">
            @csrf

            <div class="card-body">

                <div class="row">

                    {{-- Nama Kelas --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Kelas</label>
                            <input type="text"
                                   name="namakelas"
                                   class="form-control"
                                   placeholder="Contoh: X IPA 1"
                                   value="{{ old('namakelas') }}"
                                   required>
                        </div>
                    </div>

                    {{-- Jenjang --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenjang</label>
                            <select name="jenjang" class="form-control" required>
                                <option value="">-- Pilih Jenjang --</option>
                                <option value="X" {{ old('jenjang') == 'X' ? 'selected' : '' }}>X</option>
                                <option value="XI" {{ old('jenjang') == 'XI' ? 'selected' : '' }}>XI</option>
                                <option value="XII" {{ old('jenjang') == 'XII' ? 'selected' : '' }}>XII</option>
                            </select>
                        </div>
                    </div>

                    {{-- Wali Kelas --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Wali Kelas</label>
                            <select name="guru_id" class="form-control">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($guru ?? [] as $g)
                                    <option value="{{ $g->id }}"
                                        {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer text-right">
                <a href="{{ route('akademik.kelas.index') }}" class="btn btn-default">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
