{{-- MODAL AUTO GENERATE WIZARD --}}
<div class="modal fade" id="modalAutoGenerate" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formSmartGenerate" action="{{ route('manajemenasetdanasrama.jadwal-piket.auto-generate') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-robot mr-2"></i> Smart Picket Generator
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                {{-- STEP 1: Dasar & Jumlah Lokasi --}}
                <div id="step-1" class="picket-step">
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 10px; background: #e7f3ff; color: #004085;">
                            <i class="fas fa-info-circle mr-2"></i> <strong>Langkah 1:</strong> Tentukan rentang waktu dan jumlah lokasi piket.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Dari Tanggal <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-calendar-alt text-primary"></i></span>
                                        </div>
                                        <input type="date" class="form-control border-left-0" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Sampai Tanggal <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-calendar-check text-primary"></i></span>
                                        </div>
                                        <input type="date" class="form-control border-left-0" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-uppercase text-muted mb-1">Pilih Waktu (Shift) <span class="text-danger">*</span></label>
                                    <select class="form-control shadow-sm" name="shift" required>
                                        <option value="pagi">Pagi</option>
                                        <option value="sore">Sore</option>
                                        <option value="malam">Malam</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label class="small font-weight-bold text-uppercase text-primary mb-1">Berapa Banyak Tempat Piket? <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                </div>
                                <input type="number" id="input_jumlah_lokasi" class="form-control font-weight-bold" placeholder="Contoh: 3" min="1" max="10" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3 px-4">
                        <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">Batal</button>
                        <button type="button" id="btnNextStep" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">
                            Lanjut Atur Lokasi <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 2: Detail Lokasi & Kuota --}}
                <div id="step-2" class="picket-step d-none">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-list-ul mr-2"></i> Pengaturan Lokasi & Kuota</h6>
                            <div class="bg-dark text-white px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                                <i class="fas fa-users mr-1"></i> Sisa Santri: <span id="label_sisa_santri" class="font-weight-bold text-warning">{{ $totalSantri }}</span>
                            </div>
                        </div>
                        
                        <div id="location_inputs_container" style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                            {{-- Input Dinamis Muncul Disini --}}
                        </div>

                        <div class="alert alert-warning border-0 shadow-sm mt-3 mb-0 d-none" id="alert_over_limit" style="border-radius: 10px;">
                            <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Peringatan!</strong> Jumlah santri melebihi kapasitas total.
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3 px-4">
                        <button type="button" id="btnPrevStep" class="btn btn-outline-secondary px-4 mr-auto" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                        <button type="submit" id="btnGenerateNow" class="btn btn-success px-4 font-weight-bold shadow-sm" style="border-radius: 8px;" disabled><i class="fas fa-rocket mr-2"></i> Generate Sekarang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const totalSantri = {{ $totalSantri }};
        
        // Wizard Logic: Next Step
        $('#btnNextStep').on('click', function() {
            const jml = $('#input_jumlah_lokasi').val();
            if(!jml || jml < 1) {
                alert('Silakan isi jumlah lokasi piket terlebih dahulu.');
                return;
            }

            // Generate Inputs di Step 2
            let html = '';
            for(let i=1; i<=jml; i++) {
                html += `
                    <div class="location-row shadow-sm">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="form-group mb-md-0">
                                    <label class="small font-weight-bold text-muted uppercase">NAMA LOKASI ${i}</label>
                                    <input type="text" name="lokasi[]" class="form-control" placeholder="Misal: Masjid / Halaman" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold text-muted uppercase">JUMLAH SANTRI</label>
                                    <input type="number" name="jumlah_santri[]" class="form-control input-piket-quota" value="0" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            $('#location_inputs_container').html(html);
            
            // Switch Step
            $('#step-1').addClass('d-none');
            $('#step-2').removeClass('d-none');
            updateRemainingCount();
        });

        // Wizard Logic: Prev Step
        $('#btnPrevStep').on('click', function() {
            $('#step-2').addClass('d-none');
            $('#step-1').removeClass('d-none');
        });

        // Live Counter Logic
        $(document).on('input', '.input-piket-quota', function() {
            updateRemainingCount();
        });

        function updateRemainingCount() {
            let totalInput = 0;
            $('.input-piket-quota').each(function() {
                totalInput += parseInt($(this).val()) || 0;
            });

            const sisa = totalSantri - totalInput;
            const label = $('#label_sisa_santri');
            
            label.text(sisa);

            if (sisa < 0) {
                label.removeClass('text-warning text-success').addClass('text-danger');
                $('#alert_over_limit').removeClass('d-none');
                $('#btnGenerateNow').attr('disabled', true);
            } else if (sisa === 0) {
                label.removeClass('text-warning text-danger').addClass('text-success');
                $('#alert_over_limit').addClass('d-none');
                $('#btnGenerateNow').attr('disabled', false);
            } else {
                label.removeClass('text-success text-danger').addClass('text-warning');
                $('#alert_over_limit').addClass('d-none');
                $('#btnGenerateNow').attr('disabled', true);
            }
        }
    });
</script>

<style>
    .location-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        border-left: 5px solid #4361ee;
    }
</style>
