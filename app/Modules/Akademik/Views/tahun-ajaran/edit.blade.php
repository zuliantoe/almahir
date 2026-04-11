@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <x-card title="Edit Tahun Ajaran" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-alert type="warning" outline>
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Perhatian:</strong> Jika mengaktifkan tahun ajaran ini, tahun ajaran lain yang aktif akan otomatis dinonaktifkan.
                    </x-alert>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <x-input label="Tahun Ajaran" name="tahunajaran" 
                                     :value="old('tahunajaran', $tahunAjaran->tahunajaran)" 
                                     placeholder="Contoh: 2023/2024" 
                                     prepend="<i class='fas fa-calendar'></i>" 
                                     required />
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="d-block font-weight-bold">Status Keaktifan</label>
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" 
                                           {{ old('status', $tahunAjaran->status) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="status">Jadikan Tahun Ajaran Aktif</label>
                                </div>
                                @if($tahunAjaran->status)
                                    <small class="text-success font-weight-bold d-block mt-2">
                                        <i class="fas fa-check-circle"></i> Saat ini berstatus Aktif
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row border-top pt-3 mt-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Dibuat Pada:</small>
                            <span class="font-weight-bold">{{ $tahunAjaran->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <small class="text-muted d-block">Terakhir Diperbarui:</small>
                            <span class="font-weight-bold">{{ $tahunAjaran->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <x-btn :href="route('akademik.tahun-ajaran.index')" class="btn-secondary" icon="fas fa-arrow-left">
                            Kembali
                        </x-btn>
                        <div>
                            <x-btn type="reset" class="btn-warning text-white mr-2" icon="fas fa-undo">
                                Reset
                            </x-btn>
                            <x-btn type="submit" icon="fas fa-save">
                                Simpan Perubahan
                            </x-btn>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
