@extends('layouts.app')

@section('title', 'Edit Kegiatan Akademik')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <x-card title="Edit Kegiatan Akademik" icon="fas fa-edit" type="warning" outline>
                <form action="{{ route('akademik.kalender-akademik.update', $kalenderAkademik->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Tahun Ajaran</label>
                            <select name="tahunajaran_id" class="form-control @error('tahunajaran_id') is-invalid @enderror">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ (old('tahunajaran_id', $kalenderAkademik->tahunajaran_id) == $ta->id) ? 'selected' : '' }}>
                                        {{ $ta->tahunajaran }} ({{ $ta->semester }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tahunajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Jenis Kegiatan</label>
                            <select name="kegiatan_id" class="form-control @error('kegiatan_id') is-invalid @enderror">
                                <option value="">Pilih Jenis Kegiatan</option>
                                @foreach($jenisKegiatans as $jk)
                                    <option value="{{ $jk->id }}" {{ (old('kegiatan_id', $kalenderAkademik->kegiatan_id) == $jk->id) ? 'selected' : '' }}>
                                        {{ $jk->jeniskegiatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kegiatan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <x-input label="Nama Kegiatan" name="nama_kegiatan" 
                                     :value="old('nama_kegiatan', $kalenderAkademik->nama_kegiatan)" 
                                     placeholder="Contoh: UTS Semester Ganjil" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-input label="Tanggal Mulai" name="tanggal_awal" type="date" 
                                     :value="old('tanggal_awal', $kalenderAkademik->tanggal_awal)" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-input label="Tanggal Selesai" name="tanggal_akhir" type="date" 
                                     :value="old('tanggal_akhir', $kalenderAkademik->tanggal_akhir)" />
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-group text-dark">
                                <label class="font-weight-bold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                          rows="3" placeholder="Keterangan tambahan kegiatan...">{{ old('deskripsi', $kalenderAkademik->deskripsi) }}</textarea>
                                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <x-btn :href="route('akademik.kalender-akademik.index')" class="btn-secondary mr-2" icon="fas fa-arrow-left">
                            Batal
                        </x-btn>
                        <x-btn type="submit" class="btn-warning text-white" icon="fas fa-save">
                            Perbarui Kegiatan
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
