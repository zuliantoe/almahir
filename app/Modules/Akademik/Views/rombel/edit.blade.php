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
                        <label class="font-weight-bold">Tingkat <span class="text-danger">*</span></label>
                        <select id="tingkat_id" class="form-control" required>
                            <option value="">-- Pilih Tingkat --</option>
                            @foreach($tingkat as $t)
                                <option value="{{ $t->id }}" {{ $rombel->tingkat_id == $t->id ? 'selected' : '' }}>{{ $t->nama_tingkat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" id="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" data-tingkat="{{ $k->tingkat_id }}" {{ old('kelas_id', $rombel->kelas_id) == $k->id ? 'selected' : '' }}>
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
                        <div class="alert alert-warning py-2 shadow-sm border-0" style="background: linear-gradient(to right, #fff9e6, #ffffff);">
                            <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                            <small class="font-weight-bold text-dark">Mengubah Kelas atau Tahun Ajaran akan mengarsipkan data saat ini sebagai riwayat (Naik Kelas) dan membuat snapshot baru.</small>
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
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody">
                                @forelse($siswas as $s)
                                    @php $isSelected = in_array($s->id, old('siswa_ids', $selected_siswas)); @endphp
                                    <tr class="siswa-row {{ $isSelected ? 'selected' : '' }}" style="cursor: pointer;" onclick="toggleRow(this)">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" 
                                                       class="custom-control-input siswa-check" id="siswa_{{ $s->id }}"
                                                       {{ $isSelected ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="siswa_{{ $s->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="siswa-nama font-weight-bold text-dark">{{ $s->nama }}</span>
                                                <small class="text-muted"><code class="text-primary">{{ $s->nis }}</code></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($isSelected)
                                                <span class="badge badge-success px-2 shadow-sm"><i class="fas fa-check mr-1"></i> Anggota</span>
                                            @else
                                                <span class="badge badge-light border px-2">Calon</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-slash fa-3x mb-3 text-light"></i>
                                            <p class="mb-0 italic">Tidak ada siswa tambahan yang tersedia untuk Tahun Ajaran ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span class="badge badge-light border px-3 py-2 text-muted">
                            Total: {{ $siswas->count() }} Siswa Ditampilkan
                        </span>
                        <div class="text-warning font-weight-bold" style="font-size: 1.1rem;">
                            <span id="selectedCount">0</span> <small>Siswa Terpilih</small>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <x-btn :href="route('akademik.rombel.index')" class="btn-light text-muted border">Batal</x-btn>
                        <button type="submit" class="btn btn-warning px-5 shadow-lg font-weight-bold text-white" style="border-radius: 30px;">
                            <i class="fas fa-save mr-2"></i>Perbarui Rombel
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

<style>
    .custom-control-lg .custom-control-label::before, 
    .custom-control-lg .custom-control-label::after { width: 1.5rem; height: 1.5rem; top: -0.25rem; left: -1.75rem; }
    .siswa-row:hover { background-color: #fff9e6 !important; }
    .siswa-row.selected { background-color: #fffcf0 !important; border-left: 4px solid #ffc107; }
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

        function filterKelas() {
            const tingkatId = tingkatSelect.value;
            const currentKelasId = kelasSelect.value;
            
            // Clear current options
            kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
            
            if (tingkatId) {
                const filtered = kelasOptions.filter(opt => opt.getAttribute('data-tingkat') == tingkatId);
                filtered.forEach(opt => {
                    const newOpt = opt.cloneNode(true);
                    if (newOpt.value == currentKelasId) newOpt.selected = true;
                    kelasSelect.appendChild(newOpt);
                });
                kelasSelect.disabled = false;
            } else {
                kelasSelect.disabled = true;
            }
        }

        tingkatSelect.addEventListener('change', filterKelas);

        const searchInput = document.getElementById('siswaSearch');
        const rows = document.querySelectorAll('.siswa-row');
        const selectAll = document.getElementById('selectAllSiswa');
        const checks = document.querySelectorAll('.siswa-check');
        const countSpan = document.getElementById('selectedCount');

        // Initial filter call to make sure correct classes are shown for existing Tingkat
        if (tingkatSelect.value) {
            // But we don't want to clear it if it's already correct on load (standard edit)
            // Actually, we should call it to ensure ONLY current tingkat classes are in the list
            filterKelas();
        }

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
