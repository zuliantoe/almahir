@extends('layouts.app')

@section('title', 'Tambah Kelas')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 0.5rem; border:none; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-modern">
                <div class="card-header bg-gradient-blue">
                    <h3 class="card-title text-white">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Form Tambah Kelas
                    </h3>
                </div>

                <form action="{{ route('akademik.kelas.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            {{-- Nama Kelas --}}
                            <div class="col-md-6 mb-4">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Nama Kelas</label>
                                    <input type="text"
                                           name="namakelas"
                                           class="form-control form-control-modern"
                                           placeholder="Contoh: X IPA 1"
                                           value="{{ old('namakelas') }}"
                                           required>
                                </div>
                            </div>

                            {{-- Jenjang --}}
                            <div class="col-md-6 mb-4">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Jenjang</label>
                                    <select name="jenjang" class="form-control form-control-modern" required>
                                        <option value="">-- Pilih Jenjang --</option>
                                        <option value="X" {{ old('jenjang') == 'X' ? 'selected' : '' }}>Kelas X</option>
                                        <option value="XI" {{ old('jenjang') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                        <option value="XII" {{ old('jenjang') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Wali Kelas --}}
                            <div class="col-md-12 mb-2">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Wali Kelas</label>
                                    <select name="guru_id" class="form-control form-control-modern">
                                        <option value="">-- Pilih Guru Wali Kelas --</option>
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
                    <div class="card-footer bg-white text-right py-3 border-0">
                        <a href="{{ route('akademik.kelas.index') }}" class="btn btn-secondary btn-modern mr-2">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
