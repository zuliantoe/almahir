@extends('layouts.app')

@section('title', 'Edit Kurikulum')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <x-card title="Edit Data Kurikulum" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.kurikulum.update', $kurikulum->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Master Kurikulum</label>
                            <select name="master_kurikulum_id" class="form-control select2 @error('master_kurikulum_id') is-invalid @enderror">
                                <option value="">Pilih Master Kurikulum</option>
                                @foreach($masterKurikulums as $mk)
                                    <option value="{{ $mk->id }}" {{ (old('master_kurikulum_id', $kurikulum->master_kurikulum_id) == $mk->id) ? 'selected' : '' }}>
                                        {{ $mk->nama_kurikulum }}
                                    </option>
                                @endforeach
                            </select>
                            @error('master_kurikulum_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tingkat</label>
                            <select name="tingkat_id" class="form-control select2 @error('tingkat_id') is-invalid @enderror">
                                <option value="">Pilih Tingkat</option>
                                @foreach($tingkats as $tingkat)
                                    <option value="{{ $tingkat->id }}" {{ (old('tingkat_id', $kurikulum->tingkat_id) == $tingkat->id) ? 'selected' : '' }}>
                                        {{ $tingkat->nama_tingkat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tahun Ajaran</label>
                            <select name="tahunajaran_id" class="form-control select2 @error('tahunajaran_id') is-invalid @enderror">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ (old('tahunajaran_id', $kurikulum->tahunajaran_id) == $ta->id) ? 'selected' : '' }}>
                                        {{ $ta->tahunajaran }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahunajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Kelas (Opsional)</label>
                            <select name="kelas_id" class="form-control select2 @error('kelas_id') is-invalid @enderror">
                                <option value="">Semua Kelas di Tingkat Ini</option>
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id }}" data-tingkat="{{ $kelas->tingkat_id }}" {{ (old('kelas_id', $kurikulum->kelas_id) == $kelas->id) ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control select2 @error('mapel_id') is-invalid @enderror">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" {{ (old('mapel_id', $kurikulum->mapel_id) == $mapel->id) ? 'selected' : '' }}>
                                        [{{ $mapel->kode }}] {{ $mapel->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <x-input label="Total Jam / Minggu" name="totaljam" type="number" 
                                     :value="old('totaljam', $kurikulum->totaljam)" 
                                     placeholder="Contoh: 4" />
                        </div>

                        <div class="col-md-3 mb-3">
                            <x-input label="KKM" name="kkm" type="number" 
                                     :value="old('kkm', $kurikulum->kkm)" 
                                     placeholder="Contoh: 75" />
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.kurikulum.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                            Batal
                        </x-btn>
                        <x-btn type="submit" class="btn-warning text-white" icon="fas fa-save">
                            Perbarui Kurikulum
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize select2 on this view
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true
        });

        // Dynamic Kelas filtering based on selected Tingkat
        var $tingkat = $('select[name="tingkat_id"]');
        var $kelas = $('select[name="kelas_id"]');
        if ($tingkat.length && $kelas.length) {
            var originalOptions = $kelas.find('option').clone();
            
            function filterKelas() {
                var tingkatId = $tingkat.val();
                var currentVal = $kelas.val();
                
                $kelas.empty();
                
                originalOptions.each(function() {
                    var $opt = $(this);
                    if ($opt.val() === "" || $opt.data('tingkat') == tingkatId) {
                        $kelas.append($opt.clone());
                    }
                });
                
                // Restore value if it's still available in the filtered list
                if ($kelas.find('option[value="' + currentVal + '"]').length) {
                    $kelas.val(currentVal);
                } else {
                    $kelas.val("");
                }
                
                $kelas.trigger('change.select2');
            }
            
            $tingkat.on('change', filterKelas);
            
            // Run initially if there is a selected value
            if ($tingkat.val()) {
                filterKelas();
            }
        }
    });
</script>
@endsection
