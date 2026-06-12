@extends('layouts.app')

@section('title', 'Edit Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <x-card title="Edit Jadwal Pelajaran" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.jadwal-pelajaran.update', $jadwalPelajaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Rombongan Belajar (Rombel)</label>
                            <select name="rombel_id" class="form-control @error('rombel_id') is-invalid @enderror">
                                <option value="">Pilih Rombel</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}" {{ (old('rombel_id', $jadwalPelajaran->rombel_id) == $rombel->id) ? 'selected' : '' }}>
                                        {{ $rombel->nama_rombel }} ({{ $rombel->kelas->nama_kelas }})
                                    </option>
                                @endforeach
                            </select>
                            @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" {{ (old('mapel_id', $jadwalPelajaran->mapel_id) == $mapel->id) ? 'selected' : '' }}>
                                        [{{ $mapel->kode }}] {{ $mapel->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Guru Pengajar</label>
                            <select name="guru_id" class="form-control @error('guru_id') is-invalid @enderror">
                                <option value="">Pilih Guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ (old('guru_id', $jadwalPelajaran->guru_id) == $guru->id) ? 'selected' : '' }}>
                                        {{ $guru->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Hari</label>
                            <select name="hari" class="form-control @error('hari') is-invalid @enderror">
                                <option value="">Pilih Hari</option>
                                @foreach($hariList as $hari)
                                    <option value="{{ $hari }}" {{ (old('hari', $jadwalPelajaran->hari) == $hari) ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                            @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Master Jam Pelajaran <span class="text-danger">*</span></label>
                            <select name="master_jam_pelajaran_id"
                                    class="form-control @error('master_jam_pelajaran_id') is-invalid @enderror" required>
                                <option value="">Pilih Master Jam</option>
                                @foreach($masterJams as $mj)
                                    <option value="{{ $mj->id }}"
                                        {{ (old('master_jam_pelajaran_id', $selectedMasterJamId ?? null) == $mj->id) ? 'selected' : '' }}
                                        data-jamawal="{{ $mj->jamawal }}"
                                        data-jamakhir="{{ $mj->jamakhir }}"
                                        data-jamke="{{ $mj->jamke }}"
                                        data-hari="{{ $mj->hari }}"
                                        data-istirahat="{{ $mj->is_istirahat ? 1 : 0 }}"
                                    >
                                        {{ $mj->jamke }} ({{ \Carbon\Carbon::parse($mj->jamawal)->format('H:i') }}-{{ \Carbon\Carbon::parse($mj->jamakhir)->format('H:i') }}) {{ $mj->is_istirahat ? '[ISTIRAHAT]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('master_jam_pelajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            <input type="hidden" name="jamke" value="{{ old('jamke', $jadwalPelajaran->jamke) }}">
                            <input type="hidden" name="jamawal" value="{{ old('jamawal', substr($jadwalPelajaran->jamawal, 0, 5)) }}">
                            <input type="hidden" name="jamakhir" value="{{ old('jamakhir', substr($jadwalPelajaran->jamakhir, 0, 5)) }}">
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.jadwal-pelajaran.index')" class="btn-secondary mr-2" icon="fas fa-times">
                            Batal
                        </x-btn>
                        <x-btn type="submit" class="btn-warning text-white" icon="fas fa-save">
                            Perbarui Data
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const masterSelect = document.querySelector('select[name="master_jam_pelajaran_id"]');
        const hariSelect = document.querySelector('select[name="hari"]');
        if (!masterSelect || !hariSelect) return;

        const updateFields = () => {
            const selected = masterSelect.options[masterSelect.selectedIndex];
            if (!selected) return;

            const jamke = selected.dataset.jamke || '';
            const jamawal = selected.dataset.jamawal || '';
            const jamakhir = selected.dataset.jamakhir || '';

            const jamkeInput = document.querySelector('input[name="jamke"]');
            const jamawalInput = document.querySelector('input[name="jamawal"]');
            const jamakhirInput = document.querySelector('input[name="jamakhir"]');

            if (jamkeInput) jamkeInput.value = jamke;
            if (jamawalInput) jamawalInput.value = jamawal;
            if (jamakhirInput) jamakhirInput.value = jamakhir;
        };

        const filterMasterJamByHari = () => {
            if (!masterSelect.originalOptions) {
                masterSelect.originalOptions = Array.from(masterSelect.options);
            }

            const selectedHari = hariSelect.value;
            const currentSelectedValue = masterSelect.value;

            masterSelect.innerHTML = '';

            const filtered = masterSelect.originalOptions.filter((opt, index) => {
                if (index === 0) return true; // Keep placeholder "Pilih Master Jam"
                const optHari = opt.getAttribute('data-hari');
                const optIstirahat = opt.getAttribute('data-istirahat');
                return optHari === selectedHari && optIstirahat === '0';
            });

            filtered.forEach(opt => {
                masterSelect.appendChild(opt);
            });

            const hasValue = filtered.some(opt => opt.value === currentSelectedValue);
            if (hasValue && currentSelectedValue !== "") {
                masterSelect.value = currentSelectedValue;
            } else {
                masterSelect.value = "";
            }

            updateFields();
        };

        hariSelect.addEventListener('change', filterMasterJamByHari);
        masterSelect.addEventListener('change', updateFields);

        filterMasterJamByHari(); // Filter initially
    });
</script>
@endsection
