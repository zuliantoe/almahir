<script>
    function showDetailPengajuan(id, showUrl) {
        $.ajax({
            url: showUrl.replace(':id', id),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Isi field modal
                $('#detail_nomor_pengajuan').text(data.nomor_pengajuan || '-');
                $('#detail_nama_aset').text(data.nama_aset || '-');
                $('#detail_deskripsi').text(data.deskripsi_pengajuan || '-');
                $('#detail_estimasi_harga').text(data.estimasi_harga ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data.estimasi_harga) : '-');
                $('#detail_tanggal').text(data.tanggal_pengajuan ? new Date(data.tanggal_pengajuan).toLocaleDateString('id-ID') : '-');

                // Status dengan badge
                var statusText = '';
                var statusClass = '';
                switch (data.status) {
                    case 'diajukan':
                        statusText = 'Diajukan';
                        statusClass = 'badge-warning';
                        break;
                    case 'disetujui':
                        statusText = 'Disetujui';
                        statusClass = 'badge-success';
                        break;
                    case 'ditolak':
                        statusText = 'Ditolak';
                        statusClass = 'badge-danger';
                        break;
                    case 'proses_pengadaan':
                        statusText = 'Proses Pengadaan';
                        statusClass = 'badge-info';
                        break;
                }
                $('#detail_status').html('<span class="badge ' + statusClass + '">' + statusText + '</span>');

                $('#detail_pengaju').text(data.pengaju ? data.pengaju.name : '-');
                $('#detail_approved_by').text(data.approver ? data.approver.name : '-');
                $('#detail_approved_at').text(data.approved_at ? new Date(data.approved_at).toLocaleString('id-ID') : '-');

                // Data pengadaan
                if (data.pengadaan && data.pengadaan.length > 0) {
                    var html = '<table class="table table-sm table-bordered"><thead><tr><th>No. PO</th><th>Vendor</th><th>Tgl Pesan</th><th>Status</th></tr></thead><tbody>';
                    $.each(data.pengadaan, function(i, item) {
                        html += '<tr>' +
                            '<td>' + (item.nomor_po || '-') + '</td>' +
                            '<td>' + (item.vendor || '-') + '</td>' +
                            '<td>' + (item.tanggal_pesan ? new Date(item.tanggal_pesan).toLocaleDateString('id-ID') : '-') + '</td>' +
                            '<td><span class="badge badge-light border">' + (item.status || '-') + '</span></td>' +
                            '</tr>';
                    });
                    html += '</tbody></table>';
                    $('#detail_pengadaan').html(html);
                } else {
                    $('#detail_pengadaan').html('<p class="text-muted small italic text-center py-2">Tidak ada riwayat pengadaan</p>');
                }

                // Tampilkan catatan tolak jika statusnya ditolak
                if (data.status === 'ditolak' && data.catatan_tolak) {
                    $('#section_catatan_tolak').fadeIn();
                    $('#detail_catatan_tolak').text(data.catatan_tolak);
                    $('#label_approved_by').text('Ditolak Oleh');
                    $('#label_approved_at').text('Tanggal Penolakan');
                } else {
                    $('#section_catatan_tolak').hide();
                    $('#label_approved_by').text('Verifikator');
                    $('#label_approved_at').text('Tgl Verifikasi');
                }

                // Tampilkan alasan pengajuan ulang jika ada
                if (data.alasan_pengajuan_ulang) {
                    $('#section_alasan_pengajuan_ulang').show();
                    $('#detail_alasan_pengajuan_ulang').text(data.alasan_pengajuan_ulang);
                } else {
                    $('#section_alasan_pengajuan_ulang').hide();
                }

                // Tampilkan modal
                $('#modalDetailPengajuan').modal('show');
            },
            error: function(xhr) {
                alert('Gagal mengambil data. Silakan coba lagi.');
            }
        });
    }
</script>
