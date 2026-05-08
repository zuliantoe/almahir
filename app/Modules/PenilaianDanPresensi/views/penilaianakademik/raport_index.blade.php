@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 25px; background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);">
                <div class="card-body p-5 text-white">
                    <h3 class="font-weight-bold mb-2"><i class="fas fa-file-alt mr-2"></i> Cetak Raport</h3>
                    <p class="mb-0 opacity-75 font-weight-500">Halaman khusus Wali Kelas dan Admin untuk mengunduh raport santri</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <form method="GET" class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="small font-weight-bold text-muted text-uppercase mb-2">PILIH KELAS</label>
                    <select name="kelas_id" class="form-control" style="border-radius: 12px; height: 50px; border: 1px solid #e0e0e0;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="small font-weight-bold text-muted text-uppercase mb-2">Tahun Ajaran Aktif</label>
                    <div class="form-control d-flex align-items-center font-weight-bold" style="border-radius: 12px; height: 50px; background-color: #f8f9fa; border: 1px solid #e0e0e0; color: #333;">
                        {{ $activeTA->tahunajaran ?? '-' }} ({{ $activeTA->semester ?? '-' }})
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" style="border-radius: 12px; height: 50px; background-color: #4e73df; border: none;">
                        <i class="fas fa-search mr-2"></i> Tampilkan Santri
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-muted small font-weight-bold">NAMA SANTRI</th>
                            <th class="border-0 py-3 text-muted small font-weight-bold">KELAS</th>
                            <th class="border-0 py-3 text-muted small font-weight-bold">NIS</th>
                            <th class="border-0 text-center py-3 text-muted small font-weight-bold">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $s)
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td class="px-4 py-4">
                                <div class="font-weight-bold text-dark" style="font-size: 1.05rem;">{{ $s->nama }}</div>
                            </td>
                            <td class="py-4">
                                <span class="text-muted">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                            </td>
                            <td class="py-4">
                                <span class="text-muted" style="letter-spacing: 1px;">{{ $s->nis ?? '-' }}</span>
                            </td>
                            <td class="text-center px-4 py-4">
                                <button type="button" class="btn btn-outline-primary font-weight-bold px-3 mr-2 btn-input-catatan" 
                                    style="border-radius: 10px; border-width: 2px; font-size: 0.9rem;"
                                    data-id="{{ $s->id }}"
                                    data-nama="{{ $s->nama }}"
                                    data-catatan="{{ $notes[$s->id]->catatan ?? '' }}"
                                    data-tahfidz="{{ $notes[$s->id]->catatan_tahfidz ?? '' }}">
                                    <i class="fas fa-edit mr-2"></i> Input Saran / Nasihat
                                </button>
                                <a href="{{ route('penilaiandanpresensi.penilaianakademik.raport.show', $s->id) }}" class="btn btn-primary font-weight-bold px-4" style="border-radius: 10px; background-color: #4e73df; border: none; font-size: 0.9rem;">
                                    <i class="fas fa-file-invoice mr-2"></i> Cetak Raport
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3 d-block opacity-20"></i>
                                Tidak ada data santri ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $siswas->links() }}
    </div>
</div>

{{-- Catatan Modal --}}
<div class="modal fade" id="catatanModal" tabindex="-1" role="dialog" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title font-weight-bold" id="catatanModalLabel">Input Saran / Nasihat Wali Kelas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="catatanForm">
                    @csrf
                    <input type="hidden" name="siswa_id" id="modal_siswa_id">
                    <div class="mb-4 p-3 bg-light rounded-lg">
                        <span class="text-muted small font-weight-bold d-block mb-1 text-uppercase">Santri</span>
                        <h5 class="mb-0 font-weight-bold" id="modal_nama_siswa">-</h5>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark mb-2">Saran / Nasihat Perkembangan (Umum)</label>
                        <textarea name="catatan" id="modal_catatan" class="form-control" rows="4" style="border-radius: 12px; border: 1px solid #e0e0e0; resize: none;" placeholder="Contoh: Ananda memiliki potensi besar, disarankan untuk lebih giat murojaah..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark mb-2">Catatan Khusus Tahfidz (Opsional)</label>
                        <textarea name="catatan_tahfidz" id="modal_catatan_tahfidz" class="form-control" rows="3" style="border-radius: 12px; border: 1px solid #e0e0e0; resize: none;" placeholder="Contoh: Hafalan juz 30 sudah mutqin..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-outline-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                <button type="button" id="btnSaveCatatan" class="btn btn-primary font-weight-bold px-5" style="border-radius: 10px; background-color: #4e73df; border: none;">Simpan Saran / Nasihat</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-input-catatan', function() {
        const id = $(this).attr('data-id');
        const nama = $(this).attr('data-nama');
        const catatan = $(this).attr('data-catatan');
        const tahfidz = $(this).attr('data-tahfidz');

        $('#modal_siswa_id').val(id);
        $('#modal_nama_siswa').text(nama);
        $('#modal_catatan').val(catatan);
        $('#modal_catatan_tahfidz').val(tahfidz);
        $('#catatanModal').modal('show');
    });

    $('#btnSaveCatatan').click(function() {
        const formData = $('#catatanForm').serialize();
        const btn = $(this);
        btn.attr('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        
        $.ajax({
            url: "{{ route('penilaiandanpresensi.penilaianakademik.raport.save-catatan') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal menyimpan catatan.'
                    });
                    btn.attr('disabled', false).text('Simpan Catatan');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan saat menyimpan catatan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMsg
                });
                btn.attr('disabled', false).text('Simpan Catatan');
            }
        });
    });
});
</script>
@endpush
@endsection
