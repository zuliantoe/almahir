@extends('layouts.app')

@section('title', (isset($kurikulum) ? 'Edit' : 'Tambah Massal') . ' Kurikulum')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($kurikulum) ? 'Edit' : 'Tambah Massal' }} Kurikulum</h1>
            <x-btn :href="route('akademik.kurikulum.index')" class="btn-secondary" icon="fas fa-arrow-left">
                Kembali
            </x-btn>
        </div>
    </div>

    @if(!isset($kurikulum))
    <div class="alert alert-success shadow-sm mb-4">
        <i class="fas fa-magic mr-2"></i>
        <strong>Fitur Cerdas:</strong> Anda bisa memilih Kurikulum & Kelas di atas, lalu masukkan daftar Mata Pelajaran di tabel bawah secara massal.
    </div>
    @endif

    <form action="{{ isset($kurikulum) ? route('akademik.kurikulum.update', $kurikulum->id) : route('akademik.kurikulum.bulk-store') }}" 
          method="POST" id="kurikulum-form">
        @csrf
        @if(isset($kurikulum))
            @method('PUT')
        @endif

        {{-- Header Data --}}
        <x-card title="Konfigurasi Kurikulum" type="primary" outline>
            <div class="row">
                <div class="col-md-3">
                    <label class="font-weight-bold">Master Kurikulum <span class="text-danger">*</span></label>
                    <select name="master_kurikulum_id" class="form-control select2" required>
                        <option value="">Pilih Master Kurikulum</option>
                        @foreach($masterKurikulums as $mk)
                            <option value="{{ $mk->id }}" {{ (old('master_kurikulum_id', $kurikulum->master_kurikulum_id ?? '') == $mk->id) ? 'selected' : '' }}>
                                {{ $mk->nama_kurikulum }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select name="tahunajaran_id" class="form-control select2" required>
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ (old('tahunajaran_id', $kurikulum->tahunajaran_id ?? '') == $ta->id) ? 'selected' : '' }}>
                                {{ $ta->tahunajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold">Tingkat <span class="text-danger">*</span></label>
                    <select name="tingkat_id" class="form-control select2" required>
                        <option value="">Pilih Tingkat</option>
                        @foreach($tingkats as $tingkat)
                            <option value="{{ $tingkat->id }}" {{ (old('tingkat_id', $kurikulum->tingkat_id ?? '') == $tingkat->id) ? 'selected' : '' }}>
                                {{ $tingkat->nama_tingkat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold">Kelas (Opsional)</label>
                    <select name="kelas_id" class="form-control select2">
                        <option value="">Semua Kelas di Tingkat Ini</option>
                        @foreach($kelases as $kelas)
                            <option value="{{ $kelas->id }}" {{ (old('kelas_id', $kurikulum->kelas_id ?? '') == $kelas->id) ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>

        {{-- Detail Table --}}
        <x-card title="Daftar Mata Pelajaran & KKM" type="info" outline>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Mata Pelajaran <span class="text-danger">*</span></th>
                            <th width="150">Jam / Minggu <span class="text-danger">*</span></th>
                            <th width="150">KKM <span class="text-danger">*</span></th>
                            @if(!isset($kurikulum))
                            <th width="50" class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="kurikulum-rows">
                        @if(isset($kurikulum))
                            @include('akademik::kurikulum.partials.row', ['index' => 0, 'data' => $kurikulum])
                        @else
                            @for($i=0; $i < 3; $i++)
                                @include('akademik::kurikulum.partials.row', ['index' => $i])
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>

            @if(!isset($kurikulum))
            <div class="mt-3">
                <button type="button" class="btn btn-outline-info btn-sm" onclick="addRow()">
                    <i class="fas fa-plus mr-1"></i> Tambah Mapel
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm ml-2" onclick="addRow(5)">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah 5 Mapel
                </button>
            </div>
            @endif

            <hr>

            <div class="d-flex justify-content-end">
                <x-btn type="reset" class="btn-light mr-2">Reset</x-btn>
                <x-btn type="submit" icon="fas fa-save" class="btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Kurikulum Massal
                </x-btn>
            </div>
        </x-card>
    </form>
</div>

{{-- Template Row --}}
<div id="row-template-container" style="display: none;">
    <table>
        <tbody id="row-template-body">
            @include('akademik::kurikulum.partials.row', ['index' => 'REPLACE_INDEX'])
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.rowIndex = {{ isset($kurikulum) ? 1 : 3 }};

        window.addRow = function(count = 1) {
            const templateHtml = document.getElementById('row-template-body').innerHTML;
            const tbody = document.getElementById('kurikulum-rows');
            
            for (let i = 0; i < count; i++) {
                let html = templateHtml.replace(/REPLACE_INDEX/g, window.rowIndex);
                const tempTr = document.createElement('tbody');
                tempTr.innerHTML = html;
                const newRow = tempTr.querySelector('tr');
                tbody.appendChild(newRow);
                window.rowIndex++;
            }
        };

        window.removeRow = function(btn) {
            const tbody = document.getElementById('kurikulum-rows');
            if (tbody.querySelectorAll('tr').length > 1) {
                btn.closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 baris mata pelajaran.');
            }
        };
    });
</script>
@endsection
