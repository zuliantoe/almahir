@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Tambah Tahun Ajaran" icon="fas fa-plus-circle" type="primary" outline>
                <form action="{{ route('akademik.tahun-ajaran.store') }}" method="POST">
                    @csrf

                    <x-alert type="info" outline>
                        <i class="fas fa-info-circle mr-1"></i>
                        Tahun ajaran yang aktif hanya boleh satu. Jika mencentang "Jadikan Aktif",
                        tahun ajaran lain akan otomatis dinonaktifkan.
                    </x-alert>

                    <div class="row mt-4">
                        <div class="col-md-5">
                            <x-input label="Tahun Ajaran" name="tahunajaran" 
                                     :value="old('tahunajaran')" 
                                     placeholder="Contoh: 2023/2024" 
                                     prepend="<i class='fas fa-calendar'></i>" 
                                     hint="Format: YYYY/YYYY" required />
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Semester</label>
                                <select name="semester" class="form-control select2" required>
                                    <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="d-block">Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="status">Jadikan Tahun Ajaran Aktif</label>
                                </div>
                                <small class="text-muted d-block mt-1">Check jika ini adalah tahun ajaran berjalan.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="card bg-light border p-3">
                             <h6 class="font-weight-bold"><i class="fas fa-eye text-primary mr-1"></i> Preview:</h6>
                             <div class="d-flex">
                                 <div class="mr-4">
                                     <small class="text-muted d-block">Tahun Ajaran:</small>
                                     <span id="previewTahun" class="font-weight-bold">-</span>
                                 </div>
                                 <div>
                                     <small class="text-muted d-block">Status:</small>
                                     <span id="previewStatus" class="badge badge-secondary">Tidak Aktif</span>
                                 </div>
                             </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.tahun-ajaran.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                            Kembali
                        </x-btn>
                        <x-btn type="reset" class="btn-warning text-white mr-2" icon="fas fa-undo">
                            Reset
                        </x-btn>
                        <x-btn type="submit" icon="fas fa-save">
                            Simpan Tahun Ajaran
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.querySelector('input[name="tahunajaran"]').addEventListener('keyup', function() {
    document.getElementById('previewTahun').textContent = this.value || '-';
});

document.getElementById('status').addEventListener('change', function() {
    let statusLabel = document.getElementById('previewStatus');
    if(this.checked) {
        statusLabel.textContent = 'Aktif';
        statusLabel.className = 'badge badge-success';
    } else {
        statusLabel.textContent = 'Tidak Aktif';
        statusLabel.className = 'badge badge-secondary';
    }
});

document.querySelector('input[name="tahunajaran"]').dispatchEvent(new Event('keyup'));
</script>
@endpush
