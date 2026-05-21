<div class="table-responsive">
    <table class="table table-hover align-middle {{ $striped ?? 'table-striped' }} mb-0" style="width: 100%; min-width: 900px;">
        <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
            <tr>
                <th style="width: 3%;" class="text-center py-3 border-top-0">No</th>
                <th style="width: 17%;" class="py-3 border-top-0">No. Pengajuan</th>
                <th style="width: 30%;" class="py-3 border-top-0">Nama Aset</th>
                <th style="width: 15%;" class="py-3 border-top-0 text-right">Est. Harga</th>
                <th style="width: 10%;" class="py-3 border-top-0 text-center">Tgl Ajuan</th>
                @if($showStatus ?? false)
                <th style="width: 10%;" class="py-3 border-top-0 text-center">Status</th>
                @endif
                <th style="width: 10%;" class="py-3 border-top-0 text-truncate">Pengaju</th>
                <th style="width: 10%;" class="py-3 border-top-0 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration + (method_exists($items, 'currentPage') ? ($items->currentPage() - 1) * $items->perPage() : 0) }}</td>
                <td style="white-space: nowrap;"><code class="text-primary">{{ $item->nomor_pengajuan ?? '-' }}</code></td>
                <td>
                    <div class="font-weight-bold text-dark">{{ $item->nama_aset }}</div>
                    @if($item->alasan_pengajuan_ulang)
                        <small class="text-info d-block"><i class="fas fa-redo-alt mr-1"></i> Diajukan ulang</small>
                    @endif
                </td>
                <td class="text-right font-weight-bold text-success" style="white-space: nowrap;">
                    {{ $item->estimasi_harga_formatted ?? 'Rp ' . number_format($item->estimasi_harga, 0, ',', '.') }}
                </td>
                <td class="text-center small" style="white-space: nowrap;">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                @if($showStatus ?? false)
                <td class="text-center">{!! $item->status_badge !!}</td>
                @endif
                <td class="small" style="white-space: nowrap;">{{ $item->pengaju->name ?? '-' }}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center" style="gap: 4px;">
                        {{-- Tombol Lihat --}}
                        <button type="button" class="btn btn-action-xs btn-info btn-lihat" data-id="{{ $item->id }}" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>

                        @if($mode == 'user')
                            {{-- Edit --}}
                            <button type="button" class="btn btn-action-xs btn-warning" 
                                    data-toggle="modal" 
                                    data-target="#modalEditPengajuan"
                                    data-id="{{ $item->id }}"
                                    data-nama_aset="{{ $item->nama_aset }}"
                                    data-deskripsi="{{ $item->deskripsi_pengajuan }}"
                                    data-estimasi_harga="{{ $item->estimasi_harga }}"
                                    title="Edit">
                                <i class="fas fa-edit text-white"></i>
                            </button>
                            
                            {{-- Duplikat --}}
                            <button type="button" class="btn btn-action-xs btn-secondary btn-duplicate-pengajuan" 
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_aset }}"
                                    title="Duplikat">
                                <i class="fas fa-copy"></i>
                            </button>

                            {{-- Hapus --}}
                            <button type="button" class="btn btn-action-xs btn-danger" 
                                    data-toggle="modal" 
                                    data-target="#modalHapus"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_aset }}"
                                    data-url="{{ route('manajemenasetdanasrama.pengajuan.destroy', $item->id) }}"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>

                            @if($item->status == 'ditolak')
                            <button type="button" class="btn btn-action-xs btn-dark btn-ajukan-ulang"
                                    data-toggle="modal" 
                                    data-target="#modalAjukanUlang"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_aset }}"
                                    data-deskripsi="{{ $item->deskripsi_pengajuan }}"
                                    title="Ajukan Kembali">
                                <i class="fas fa-redo text-white"></i>
                            </button>
                            @endif

                        @elseif($mode == 'approver')
                            <button type="button" class="btn btn-action-xs btn-success btn-approve" data-id="{{ $item->id }}" data-nama="{{ $item->nama_aset }}" title="Setujui">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-action-xs btn-danger btn-reject" data-id="{{ $item->id }}" data-nama="{{ $item->nama_aset }}" title="Tolak">
                                <i class="fas fa-times"></i>
                            </button>

                        @elseif($mode == 'procurement')
                            <a href="{{ route('manajemenasetdanasrama.pengadaan.proses', $item->id) }}" class="btn btn-action-xs btn-success" title="Proses PO">
                                <i class="fas fa-truck mr-1"></i> Proses
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ ($showStatus ?? false) ? '8' : '7' }}" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center justify-content-center text-muted" style="opacity: 0.6;">
                        <i class="fas fa-box-open fa-4x mb-3"></i>
                        <h6 class="font-weight-bold">Belum Ada Data Pengajuan</h6>
                        <p class="small mb-0">Klik tombol "Tambah Pengajuan" untuk membuat usulan baru.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
