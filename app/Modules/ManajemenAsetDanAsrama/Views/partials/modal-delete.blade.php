{{-- 
    Shared Delete Modal Partial
    Usage: @include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Data'])
--}}
{{-- 
    Shared Delete Modal Partial
    Usage: @include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Data'])
--}}
<div class="modal fade" id="{{ $id ?? 'modalHapus' }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-white border-0 pt-4 pb-0 justify-content-center">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="fas fa-trash-alt text-danger fa-2x"></i>
                </div>
            </div>
            <div class="modal-body text-center p-4">
                <h5 class="font-weight-bold">{{ $title ?? 'Konfirmasi Hapus' }}</h5>
                <p class="text-muted small">Anda yakin ingin menghapus <strong id="hapus_nama_generic" class="text-dark"></strong>? Tindakan ini tidak bisa dibatalkan.</p>
                <form id="formHapusGeneric" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="form-group text-left">
                        <label class="small font-weight-bold text-muted text-uppercase">Alasan Penghapusan <span class="text-danger">*</span></label>
                        <textarea class="form-control bg-light border-0" id="alasan_hapus_generic" name="alasan_hapus" rows="2" placeholder="Masukkan alasan..." required></textarea>
                    </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3 justify-content-center">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 8px;">Ya, Hapus Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#{{ $id ?? "modalHapus" }}').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            var url = button.data('url');

            var modal = $(this);
            modal.find('#hapus_nama_generic').text(nama);
            modal.find('#alasan_hapus_generic').val('');
            modal.find('#formHapusGeneric').attr('action', url);
        });
    });
</script>
@endpush
