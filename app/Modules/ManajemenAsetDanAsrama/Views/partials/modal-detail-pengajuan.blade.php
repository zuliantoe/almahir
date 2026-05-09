<div class="modal fade" id="modalDetailPengajuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-file-invoice mr-2"></i> Detail Pengajuan Aset
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h6 class="font-weight-bold text-info mb-3 text-uppercase small" style="letter-spacing: 1px;">Informasi Aset</h6>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Nomor Pengajuan</label>
                            <code id="detail_nomor_pengajuan" class="font-weight-bold" style="font-size: 1rem;">-</code>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Nama Aset</label>
                            <span id="detail_nama_aset" class="font-weight-bold text-dark h6">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Estimasi Harga</label>
                            <span id="detail_estimasi_harga" class="font-weight-bold text-success h6">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Deskripsi Alasan</label>
                            <p id="detail_deskripsi" class="text-muted small mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-6 pl-md-4">
                        <h6 class="font-weight-bold text-info mb-3 text-uppercase small" style="letter-spacing: 1px;">Status & Verifikasi</h6>
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block text-uppercase">Status Saat Ini</label>
                            <div id="detail_status" class="mt-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small text-muted mb-0 d-block text-uppercase">Diajukan Oleh</label>
                                <span id="detail_pengaju" class="font-weight-bold small text-dark">-</span>
                            </div>
                            <div class="col-6 mb-3 text-right">
                                <label class="small text-muted mb-0 d-block text-uppercase">Tgl Pengajuan</label>
                                <span id="detail_tanggal" class="font-weight-bold small text-dark">-</span>
                            </div>
                        </div>
                        <div class="bg-light p-3 rounded shadow-sm border">
                            <div class="mb-2">
                                <label id="label_approved_by" class="small text-muted mb-0 d-block text-uppercase">Verifikator</label>
                                <span id="detail_approved_by" class="font-weight-bold small text-dark">-</span>
                            </div>
                            <div>
                                <label id="label_approved_at" class="small text-muted mb-0 d-block text-uppercase">Tgl Verifikasi</label>
                                <span id="detail_approved_at" class="font-weight-bold small text-dark">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="section_catatan_tolak" class="mt-4 p-3 rounded border border-danger" style="display:none; background: #fff5f5;">
                    <label class="small text-danger font-weight-bold mb-1 text-uppercase"><i class="fas fa-exclamation-circle mr-1"></i> Catatan Penolakan</label>
                    <p id="detail_catatan_tolak" class="mb-0 text-dark small italic"></p>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <h6 class="font-weight-bold text-muted mb-3 text-uppercase small"><i class="fas fa-link mr-1"></i> Riwayat Pengadaan Terkait</h6>
                    <div id="detail_pengadaan" class="table-responsive">
                        {{-- Data via AJAX --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-3 px-4">
                <button type="button" class="btn btn-secondary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
