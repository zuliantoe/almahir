@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Tambah Penilaian Akademik" icon="fas fa-edit">
        <form action="{{ route('penilaiandanpresensi.penilaianakademik.store') }}" method="POST" id="penilaianForm">
            @csrf

            {{-- Master Data Section --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_guru"><strong>Guru <span class="text-danger">*</span></strong></label>
                        <select name="id_guru" id="id_guru" class="form-control" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_mapel"><strong>Mata Pelajaran <span class="text-danger">*</span></strong></label>
                        <select name="id_mapel" id="id_mapel" class="form-control" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="id_tahun_ajaran"><strong>Tahun Ajaran <span class="text-danger">*</span></strong></label>
                        <select name="id_tahun_ajaran" id="id_tahun_ajaran" class="form-control" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                                <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahunajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Detail Penilaian Section --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5><i class="fas fa-plus-circle text-primary mr-2"></i> Detail Penilaian</h5>
                </div>
            </div>

            {{-- Table for adding multiple grades --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="penilaianTable">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 50%">Siswa <span class="text-danger">*</span></th>
                            <th style="width: 30%">Nilai <span class="text-danger">*</span></th>
                            <th style="width: 20%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="penilaianBody">
                        {{-- Rows will be added here --}}
                    </tbody>
                </table>
            </div>

            {{-- Add Row Button --}}
            <div class="mb-4">
                <button type="button" class="btn btn-success" id="addRowBtn" onclick="addRow()">
                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>

            {{-- Submit Section --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fas fa-save mr-1"></i> Simpan Semua Penilaian
                </button>
                <a href="{{ route('penilaiandanpresensi.penilaianakademik.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </form>
    </x-card>
</div>

@endsection

@push('scripts')
<script>
let rowCount = 0;

// Get siswas list from backend
const siswasData = {!! json_encode($siswas) !!};

function addRow() {
    const tbody = document.getElementById('penilaianBody');
    const rowId = `row-${++rowCount}`;
    
    let siswaOptions = '<option value="">-- Pilih Siswa --</option>';
    siswasData.forEach(siswa => {
        siswaOptions += `<option value="${siswa.id}">${siswa.nama}</option>`;
    });
    
    const row = document.createElement('tr');
    row.id = rowId;
    row.innerHTML = `
        <td>
            <select name="penilaian[${rowId}][id_siswa]" class="form-control" required>
                ${siswaOptions}
            </select>
        </td>
        <td>
            <input type="number" name="penilaian[${rowId}][nilai]" class="form-control" min="0" max="100" placeholder="0-100" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow('${rowId}')">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    console.log('Row added:', rowId);
}

function removeRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
    }
    
    // Check if no rows left
    if (document.getElementById('penilaianBody').children.length === 0) {
        alert('Minimal harus ada 1 baris penilaian!');
        addRow();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add first row
    addRow();
    
    // Form validation
    document.getElementById('penilaianForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const guru = document.getElementById('id_guru').value;
        const mapel = document.getElementById('id_mapel').value;
        const tahun = document.getElementById('id_tahun_ajaran').value;
        const rows = document.querySelectorAll('#penilaianBody tr');
        
        if (!guru) {
            alert('Pilih guru terlebih dahulu!');
            return false;
        }
        if (!mapel) {
            alert('Pilih mata pelajaran terlebih dahulu!');
            return false;
        }
        if (!tahun) {
            alert('Pilih tahun ajaran terlebih dahulu!');
            return false;
        }
        if (rows.length === 0) {
            alert('Minimal harus ada 1 penilaian!');
            return false;
        }
        
        // Validate all rows have siswa and nilai
        let isValid = true;
        rows.forEach((row, index) => {
            const siswaSelect = row.querySelector('select');
            const nilaiInput = row.querySelector('input[type="number"]');
            
            if (!siswaSelect || !siswaSelect.value) {
                alert(`Baris ${index + 1}: Pilih siswa!`);
                isValid = false;
                return;
            }
            if (!nilaiInput || !nilaiInput.value) {
                alert(`Baris ${index + 1}: Masukkan nilai!`);
                isValid = false;
                return;
            }
            if (nilaiInput.value < 0 || nilaiInput.value > 100) {
                alert(`Baris ${index + 1}: Nilai harus antara 0-100!`);
                isValid = false;
                return;
            }
        });
        
        if (!isValid) return false;
        
        // Change button text during submission
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
        
        this.submit();
    });
});
</script>
@endpush

@push('styles')
<style>
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }
</style>
@endpush
