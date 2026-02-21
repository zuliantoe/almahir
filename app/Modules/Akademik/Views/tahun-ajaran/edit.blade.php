@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <form action="{{ route('akademik.tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
        @csrf
        @method('PUT')

        <x-card title="Edit Tahun Ajaran" type="warning">

            {{-- Informasi --}}
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Perhatikan: Jika mengaktifkan tahun ajaran ini, tahun ajaran lain yang aktif akan otomatis dinonaktifkan.
            </div>

            {{-- Form Input --}}
            <div class="row">
                <div class="col-md-6">
                    <x-input
                        label="Tahun Ajaran"
                        name="tahunajaran"
                        value="{{ old('tahunajaran', $tahunAjaran->tahunajaran) }}"
                        placeholder="Contoh: 2023/2024"
                        icon="fas fa-calendar"
                        required
                    />
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="d-block">Status</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="status"
                                   name="status"
                                   value="1"
                                   {{ old('status', $tahunAjaran->status) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status">
                                Jadikan Tahun Ajaran Aktif
                            </label>
                        </div>

                        @if($tahunAjaran->status)
                            <div class="mt-2">
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Status Saat Ini: Aktif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Data --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Dibuat Pada</label>
                        <p class="form-control-plaintext">
                            {{ $tahunAjaran->created_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Terakhir Diperbarui</label>
                        <p class="form-control-plaintext">
                            {{ $tahunAjaran->updated_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('akademik.tahun-ajaran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <div>
                        <button type="reset" class="btn btn-warning">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Perbarui
                        </button>
                    </div>
                </div>
            </x-slot>

        </x-card>
    </form>
</div>
@endsection
