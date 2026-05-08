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

    <x-card :title="(isset($jadwalPelajaran) ? 'Form Edit Data' : 'Form Input Massal Jadwal')" type="primary" outline>
        <form action="{{ isset($jadwalPelajaran) ? route('akademik.jadwal-pelajaran.update', $jadwalPelajaran->id) : route('akademik.jadwal-pelajaran.bulk-store') }}" 
              method="POST" id="schedule-form">
            @csrf
            @if(isset($jadwalPelajaran))
                @method('PUT')
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="schedule-table">
                    <thead class="bg-light">
                        <tr>
                            <th width="150">Rombel <span class="text-danger">*</span></th>
                            <th width="200">Mata Pelajaran <span class="text-danger">*</span></th>
                            <th width="200">Guru Pengajar <span class="text-danger">*</span></th>
                            <th width="120">Hari <span class="text-danger">*</span></th>
                            <th width="80">Jam Ke- <span class="text-danger">*</span></th>
                            <th width="120">Jam Mulai <span class="text-danger">*</span></th>
                            <th width="120">Jam Selesai <span class="text-danger">*</span></th>
                            @if(!isset($jadwalPelajaran))
                            <th width="50" class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="schedule-rows">
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
            <div class="mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow()">
                    <i class="fas fa-plus mr-1"></i> Tambah 1 Baris
                </button>
                <button type="button" class="btn btn-outline-info btn-sm ml-2" onclick="addRow(5)">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah 5 Baris
                </button>
            </div>
            @endif

    <hr>

    <div class="d-flex justify-content-end align-items-center">
        <div class="mr-auto text-muted small">
            <span class="text-danger">*</span> Wajib diisi. Pastikan jam mulai dan selesai sesuai format (HH:MM).
        </div>
        <x-btn type="reset" class="btn-light mr-2">Reset</x-btn>
        <x-btn type="submit" icon="fas fa-save" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Semua Jadwal
        </x-btn>
    </div>
</form>
</x-card>

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
