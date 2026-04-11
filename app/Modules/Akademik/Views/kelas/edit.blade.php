@extends('layouts.app')

@section('title', 'Edit Kelas')

@include('akademik::components.style')

@section('content')
<div class="container-fluid py-4">

    {{-- Error --}}
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
        <div class="col-md-6">
            <div class="card card-modern border-top-0">
                <div class="card-header bg-gradient-purple">
                    <h3 class="card-title text-white">
                        <i class="fas fa-edit mr-1"></i>
                        Form Edit Kelas
                    </h3>
                </div>

                <form action="{{ route('akademik.kelas.update', $kelas->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Nama Kelas</label>
                            <input type="text"
                                   name="nama"
                                   class="form-control form-control-modern"
                                   value="{{ old('nama', $kelas->nama) }}"
                                   required>
                        </div>
                        
                        <div class="alert alert-info" style="border-radius: 0.5rem; border:none; background-color: #f1f8ff; color: #0056b3;">
                            <i class="fas fa-info-circle"></i> Untuk mengedit Jenjang atau Wali Kelas, masuk ke pengaturan tingkat lanjut kelas.
                        </div>

                    </div>

                    <div class="card-footer bg-white text-right border-0 py-3">
                        <a href="{{ route('akademik.kelas.index') }}" class="btn btn-secondary btn-modern mr-2">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-warning btn-modern text-white">
                            <i class="fas fa-save mr-1"></i> Update Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
