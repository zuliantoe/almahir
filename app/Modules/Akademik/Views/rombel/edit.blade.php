@extends('layouts.app')

@section('title', 'Edit Rombel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Rombel</h1>
                <p class="text-muted">Perbarui informasi rombongan belajar dan anggotanya</p>
            </div>
            <x-btn :href="route('akademik.rombel.index')" icon="fas fa-arrow-left" class="btn-secondary shadow-sm">
                Kembali
            </x-btn>
        </div>
    </div>

    @if ($errors->any())
        <x-alert type="danger" dismissible>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form action="{{ route('akademik.rombel.update', $rombel->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-5">
                <x-card title="Informasi Rombel" icon="fas fa-info-circle" type="warning" outline>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama Rombel <span class="text-danger">*</span></label>
                        <input type="text" name="nama_rombel" class="form-control @error('nama_rombel') is-invalid @enderror" 
                               value="{{ old('nama_rombel', $rombel->nama_rombel) }}" placeholder="Contoh: X IPA 1 - A" required>
                        @error('nama_rombel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id', $rombel->kelas_id) == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="tahunajaran_id" class="form-control @error('tahunajaran_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahun_ajaran as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahunajaran_id', $rombel->tahunajaran_id) == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahunajaran }} - {{ $ta->semester }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahunajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Wali Kelas <span class="text-danger">*</span></label>
                        <select name="guru_id" class="form-control @error('guru_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}" {{ old('guru_id', $rombel->guru_id) == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional...">{{ old('keterangan', $rombel->keterangan) }}</textarea>
                    </div>
                </x-card>
            </div>

            <div class="col-lg-7">
                <x-card title="Pilih Siswa" icon="fas fa-user-graduate" type="info" outline>
                    <div class="mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="siswaSearch" class="form-control border-left-0" placeholder="Cari nama atau NIS siswa...">
                        </div>
                    </div>

                    <div class="siswa-list-container" style="max-height: 400px; overflow-y: auto; border: 1px solid #e3e6f0; border-radius: 8px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th width="40" class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="selectAllSiswa">
                                            <label class="custom-control-label" for="selectAllSiswa"></label>
                                        </div>
                                    </th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Gender</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody">
                                @forelse($siswas as $s)
                                    <tr class="siswa-row">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" 
                                                       class="custom-control-input siswa-check" id="siswa_{{ $s->id }}"
                                                       {{ in_array($s->id, old('siswa_ids', $selected_siswas)) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="siswa_{{ $s->id }}"></label>
                                            </div>
                                        </td>
                                        <td><code>{{ $s->nis }}</code></td>
                                        <td class="siswa-nama font-weight-bold">{{ $s->nama }}</td>
                                        <td class="text-center text-muted">{{ $s->jenis_kelamin }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small italic">Tidak ada siswa aktif ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-right">
                        <small class="text-muted"><span id="selectedCount">0</span> siswa terpilih</small>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning px-4 shadow-sm text-white">
                            <i class="fas fa-save mr-2"></i>Update Rombel
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('siswaSearch');
        const rows = document.querySelectorAll('.siswa-row');
        const selectAll = document.getElementById('selectAllSiswa');
        const checks = document.querySelectorAll('.siswa-check');
        const countSpan = document.getElementById('selectedCount');

        // Search logic
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            rows.forEach(row => {
                const name = row.querySelector('.siswa-nama').textContent.toLowerCase();
                const nis = row.querySelector('code').textContent.toLowerCase();
                if (name.includes(term) || nis.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Select All logic
        selectAll.addEventListener('change', function() {
            checks.forEach(check => {
                if (check.closest('.siswa-row').style.display !== 'none') {
                    check.checked = this.checked;
                }
            });
            updateCount();
        });

        // Count logic
        checks.forEach(check => {
            check.addEventListener('change', updateCount);
        });

        function updateCount() {
            const selected = document.querySelectorAll('.siswa-check:checked').length;
            countSpan.textContent = selected;
        }

        updateCount();
    });
</script>
@endpush
@endsection
