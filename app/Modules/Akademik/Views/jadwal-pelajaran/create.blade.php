@extends('layouts.app')

@section('title', (isset($jadwalPelajaran) ? 'Edit' : 'Tambah Massal') . ' Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($jadwalPelajaran) ? 'Edit' : 'Tambah Massal' }} Jadwal Pelajaran</h1>
            <x-btn :href="route('akademik.jadwal-pelajaran.index')" class="btn-secondary" icon="fas fa-arrow-left">
                Kembali
            </x-btn>
        </div>
    </div>

    @if(!isset($jadwalPelajaran))
    <div class="alert alert-info shadow-sm mb-4">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Tips UX:</strong> Gunakan tombol <strong>+ Tambah Baris</strong> untuk menginput banyak jadwal sekaligus (misal 8 jam pelajaran) sebelum menekan tombol Simpan.
    </div>
    @endif

    <x-card :title="(isset($jadwalPelajaran) ? 'Form Edit Data' : 'Form Input Massal Jadwal')" type="primary" outline class="shadow-lg border-0 rounded-xl overflow-hidden">
        <form action="{{ isset($jadwalPelajaran) ? route('akademik.jadwal-pelajaran.update', $jadwalPelajaran->id) : route('akademik.jadwal-pelajaran.bulk-store') }}" 
              method="POST" id="schedule-form" class="p-2">
            @csrf
            @if(isset($jadwalPelajaran))
                @method('PUT')
            @endif

            <div class="table-responsive rounded-lg border shadow-sm mb-4">
                <table class="table table-hover align-middle mb-0" id="schedule-table">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 px-4" width="180">ROMBEL <span class="text-white-50">*</span></th>
                            <th class="py-3 px-4" width="220">MATA PELAJARAN <span class="text-white-50">*</span></th>
                            <th class="py-3 px-4" width="220">GURU PENGAJAR <span class="text-white-50">*</span></th>
                            <th class="py-3 px-4" width="180">HARI <span class="text-white-50">*</span></th>
                            <th class="py-3 px-4" width="100">JAM KE- <span class="text-white-50">*</span></th>
                            <th class="py-3 px-4" width="180">JAM MULAI <span class="text-white-50">*</span></th>
                            <th class="py-3 px-4" width="180">JAM SELESAI <span class="text-white-50">*</span></th>
                            @if(!isset($jadwalPelajaran))
                            <th class="py-3 px-4 text-center" width="60">AKSI</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="schedule-rows" class="bg-white">
                        {{-- Row template --}}
                        @if(isset($jadwalPelajaran))
                            @include('akademik::jadwal-pelajaran.partials.row', ['index' => 0, 'data' => $jadwalPelajaran])
                        @else
                            @for($i=0; $i < 1; $i++)
                                @include('akademik::jadwal-pelajaran.partials.row', ['index' => $i])
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>

            @if(!isset($jadwalPelajaran))
            <div class="mb-4 d-flex">
                <button type="button" class="btn btn-outline-primary btn-sm px-4 rounded-pill transition-all hover-scale" onclick="addRow()">
                    <i class="fas fa-plus mr-2"></i> Tambah 1 Baris
                </button>
                <button type="button" class="btn btn-outline-info btn-sm px-4 ml-2 rounded-pill transition-all hover-scale" onclick="addRow(5)">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah 5 Baris
                </button>
            </div>
            @endif

    <div class="bg-light p-4 rounded-lg border-top">
        <div class="row align-items-center">
            <div class="col-md-6 text-muted small">
                <i class="fas fa-exclamation-triangle mr-1 text-warning"></i> 
                Wajib diisi. Pastikan rentang waktu tidak bentrok dengan jadwal lain di rombel yang sama.
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <x-btn type="reset" class="btn-light px-4 mr-3 rounded-pill border">Reset</x-btn>
                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm transition-all hover-elevate">
                    <i class="fas fa-save mr-2"></i> Simpan Semua Jadwal
                </button>
            </div>
        </div>
    </div>
</form>
</x-card>

<style>
    .rounded-xl { border-radius: 1rem !important; }
    .rounded-lg { border-radius: 0.75rem !important; }
    .rounded-left-pill { border-top-left-radius: 50rem !important; border-bottom-left-radius: 50rem !important; }
    .rounded-right-pill { border-top-right-radius: 50rem !important; border-bottom-right-radius: 50rem !important; }
    .bg-primary-soft { background-color: rgba(78, 115, 223, 0.1) !important; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .hover-scale:hover { transform: scale(1.02); }
    .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important; }
    .table th { font-weight: 700; letter-spacing: 0.05rem; font-size: 0.75rem; border-bottom: 0; white-space: nowrap; }
    .table td { border-color: #f8f9fc; vertical-align: middle; }
    
    /* Modern Scrollbar */
    .table-responsive::-webkit-scrollbar { height: 8px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #d1d3e2; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #b7b9cc; }

    .form-control-premium {
        border: 1px solid #d1d3e2;
        border-radius: 0.5rem;
        padding: 0.5rem 0.5rem;
        transition: all 0.2s;
        font-size: 0.85rem;
        height: auto;
        min-width: 100px;
    }
    select.form-control-premium {
        padding-right: 1.5rem !important;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234e73df' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 10px 10px;
    }
    .form-control-premium:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
        background-color: #fff;
    }
    input[type="time"].form-control-premium {
        min-width: 140px;
    }
    .input-group-text { border-color: #d1d3e2; }
</style>

{{-- Template untuk JavaScript --}}
<div id="row-template-container" style="display: none;">
    <table>
        <tbody id="row-template-body">
            @include('akademik::jadwal-pelajaran.partials.row', ['index' => 'REPLACE_INDEX'])
        </tbody>
    </table>
</div>

<script>
    // Pastikan script ini jalan setelah DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        window.rowIndex = 1;

        window.addRow = function(count = 1) {
            const templateHtml = document.getElementById('row-template-body').innerHTML;
            const tbody = document.getElementById('schedule-rows');
            
            for (let i = 0; i < count; i++) {
                let html = templateHtml.replace(/REPLACE_INDEX/g, window.rowIndex);
                
                // Gunakan cara yang lebih aman untuk menyisipkan HTML ke tabel
                const tempTr = document.createElement('tbody');
                tempTr.innerHTML = html;
                const newRow = tempTr.querySelector('tr');
                
                tbody.appendChild(newRow);
                window.rowIndex++;
            }
        };

        window.removeRow = function(btn) {
            const tbody = document.getElementById('schedule-rows');
            if (tbody.querySelectorAll('tr').length > 1) {
                btn.closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 baris jadwal.');
            }
        };
    });
</script>

</div>
@endsection
