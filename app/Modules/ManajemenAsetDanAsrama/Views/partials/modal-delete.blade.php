{{-- 
    Shared Delete Modal Partial
    Usage: @include('manajemenasetdanasrama::partials.modal-delete', ['id' => 'modalHapus', 'title' => 'Hapus Data'])
--}}
<div class="modal fade" id="{{ $id ?? 'modalHapus' }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formHapusGeneric" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">{{ $title ?? 'Konfirmasi Hapus' }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus <strong id="hapus_nama_generic"></strong>?</p>
                    <div class="form-group">
                        <label for="alasan_hapus_generic">Alasan Penghapusan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_hapus_generic" name="alasan_hapus" rows="3" placeholder="Masukkan alasan penghapusan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle trigger untuk modal hapus generic
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
