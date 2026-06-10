@php
    $isEdit = isset($masterJamPelajaran) && !isset($isDuplicate);
@endphp

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-clock mr-2"></i>
            {{ $isEdit ? 'Edit Master Jam Pelajaran' : (isset($isDuplicate) ? 'Duplikat Master Jam Pelajaran' : 'Tambah Master Jam Pelajaran') }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('akademik.master-jam-pelajaran.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ $isEdit ? route('akademik.master-jam-pelajaran.update', $masterJamPelajaran->id) : route('akademik.master-jam-pelajaran.store') }}" method="POST">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Hari <span class="text-danger">*</span></label>
                    <select name="hari" class="form-control @error('hari') is-invalid @enderror" required>
                        <option value="">Pilih Hari</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                            <option value="{{ $h }}" {{ old('hari', $masterJamPelajaran->hari ?? '') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                    @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <x-input label="Jam Ke" name="jamke" type="number" min="1" required
                             :value="old('jamke', $masterJamPelajaran->jamke ?? '')" placeholder="Contoh: 1" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-input label="Jam Mulai" name="jamawal" type="time" required
                             :value="old('jamawal', isset($masterJamPelajaran) ? substr($masterJamPelajaran->jamawal,0,5) : '')" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-input label="Jam Selesai" name="jamakhir" type="time" required
                             :value="old('jamakhir', isset($masterJamPelajaran) ? substr($masterJamPelajaran->jamakhir,0,5) : '')" />
                </div>

                <div class="col-md-12 mb-3">
                    <div class="custom-control custom-checkbox mt-2">
                        <input type="hidden" name="is_istirahat" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_istirahat" name="is_istirahat" value="1"
                            {{ old('is_istirahat', $masterJamPelajaran->is_istirahat ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="is_istirahat">
                            Tandai Sebagai Jam Istirahat
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">
                        Jika dicentang, jam ini akan dianggap sebagai waktu istirahat dan tidak dapat dialokasikan untuk mata pelajaran.
                    </small>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light text-right">
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

