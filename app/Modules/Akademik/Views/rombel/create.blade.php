@extends('layouts.app')

@section('title', 'Tambah Rombel')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Tambah Rombel</h1>
                <p class="text-muted">Buat rombongan belajar baru dan tentukan anggotanya</p>
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

    <form action="{{ route('akademik.rombel.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-5">
                <x-card title="Informasi Rombel" icon="fas fa-info-circle" type="primary" outline>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama Rombel <span class="text-danger">*</span></label>
                        <input type="text" name="nama_rombel" class="form-control @error('nama_rombel') is-invalid @enderror" 
                               value="{{ old('nama_rombel') }}" placeholder="Contoh: X IPA 1 - A" required>
                        @error('nama_rombel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Tingkat <span class="text-danger">*</span></label>
                        <select id="tingkat_id" class="form-control" required>
                            <option value="">-- Pilih Tingkat --</option>
                            @foreach($tingkat as $t)
                                <option value="{{ $t->id }}">{{ $t->nama_tingkat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" id="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror" required disabled>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" data-tingkat="{{ $k->tingkat_id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
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
                                <option value="{{ $ta->id }}" {{ old('tahunajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahunajaran }}
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
                                <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional...">{{ old('keterangan') }}</textarea>
                    </div>
                </x-card>
            </div>

            <div class="col-lg-7">
                <x-card title="Pilih Siswa" icon="fas fa-user-graduate" type="info" outline>
                    <div class="mb-3">
                        <div class="alert alert-info py-2 shadow-sm border-0" style="background: linear-gradient(to right, #e0f3ff, #ffffff);">
                            <i class="fas fa-info-circle mr-2 text-info"></i>
                            <small class="font-weight-bold text-info">Hanya menampilkan siswa yang BELUM terdaftar di Rombel manapun pada Tahun Ajaran ini.</small>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="siswaSearch" class="form-control border-left-0" placeholder="Cari nama atau NIS siswa...">
                        </div>
                    </div>

                    <div class="siswa-list-container shadow-sm" style="max-height: 450px; overflow-y: auto; border: 1px solid #e3e6f0; border-radius: 12px; background: #fdfdfd;">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light sticky-top shadow-sm">
                                <tr>
                                    <th width="40" class="text-center">
                                        <div class="custom-control custom-checkbox custom-control-lg">
                                            <input type="checkbox" class="custom-control-input" id="selectAllSiswa">
                                            <label class="custom-control-label" for="selectAllSiswa"></label>
                                        </div>
                                    </th>
                                    <th>Identitas Siswa</th>
                                    <th class="text-center">Gender</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody">
                                @forelse($siswas as $s)
                                    <tr class="siswa-row" style="cursor: pointer;" onclick="toggleRow(this)">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" 
                                                       class="custom-control-input siswa-check" id="siswa_{{ $s->id }}"
                                                       {{ is_array(old('siswa_ids')) && in_array($s->id, old('siswa_ids')) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="siswa_{{ $s->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="siswa-nama font-weight-bold text-dark">{{ $s->nama }}</span>
                                                <small class="text-muted"><code class="text-primary">{{ $s->nis }}</code></small>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted">
                                            @if($s->jenis_kelamin == 'L')
                                                <span class="badge badge-soft-info px-2">Laki-laki</span>
                                            @else
                                                <span class="badge badge-soft-danger px-2">Perempuan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-slash fa-3x mb-3 text-light"></i>
                                            <p class="mb-0 italic">Semua siswa aktif sudah terdaftar di Rombel lain.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span class="badge badge-light border px-3 py-2 text-muted">
                            Total: {{ $siswas->count() }} Siswa Tersedia
                        </span>
                        <div class="text-primary font-weight-bold" style="font-size: 1.1rem;">
                            <span id="selectedCount">0</span> <small>Siswa Terpilih</small>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between">
                        <x-btn :href="route('akademik.rombel.index')" class="btn-light text-muted border">Batal</x-btn>
                        <button type="submit" class="btn btn-primary px-5 shadow-lg font-weight-bold" style="border-radius: 30px;">
                            <i class="fas fa-check-circle mr-2"></i>Finalisasi Rombel
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

<style>
    .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .custom-control-lg .custom-control-label::before, 
    .custom-control-lg .custom-control-label::after { width: 1.5rem; height: 1.5rem; top: -0.25rem; left: -1.75rem; }
    .siswa-row:hover { background-color: #f0f7ff !important; }
    .siswa-row.selected { background-color: #e3f2fd !important; }
    .sticky-top { z-index: 100; }
</style>

@push('scripts')
<script>
    function toggleRow(row) {
        const check = row.querySelector('.siswa-check');
        if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'LABEL') {
            check.checked = !check.checked;
            updateCount();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tingkatSelect = document.getElementById('tingkat_id');
        const kelasSelect = document.getElementById('kelas_id');
        const kelasOptions = Array.from(kelasSelect.options);

        tingkatSelect.addEventListener('change', function() {
            const tingkatId = this.value;
            
            // Clear current options
            kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
            
            if (tingkatId) {
                const filtered = kelasOptions.filter(opt => opt.getAttribute('data-tingkat') == tingkatId);
                filtered.forEach(opt => kelasSelect.appendChild(opt.cloneNode(true)));
                kelasSelect.disabled = false;
            } else {
                kelasSelect.disabled = true;
            }
        });

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
                const row = check.closest('.siswa-row');
                if (row.style.display !== 'none') {
                    check.checked = this.checked;
                    if (this.checked) row.classList.add('selected');
                    else row.classList.remove('selected');
                }
            });
            updateCount();
        });

        // Check logic
        checks.forEach(check => {
            check.addEventListener('change', function() {
                const row = this.closest('.siswa-row');
                if (this.checked) row.classList.add('selected');
                else row.classList.remove('selected');
                updateCount();
            });
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
