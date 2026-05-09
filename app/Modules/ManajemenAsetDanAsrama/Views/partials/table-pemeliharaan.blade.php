<div class="table-responsive">
    <table class="table table-bordered table-hover {{ $striped ?? 'table-striped' }} {{ $tableClass ?? '' }}">
        <thead class="{{ $theadClass ?? 'thead-light' }}">
            <tr>
                <th width="50">No</th>
                @if($showAset ?? true)
                <th>Nama Aset</th>
                @endif
                <th width="120">Tgl Mulai</th>
                <th width="120">Tgl Selesai</th>
                <th>Deskripsi & Catatan</th>
                <th width="140">Biaya</th>
                <th width="100">Status</th>
                <th width="{{ $actionWidth ?? '120' }}">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ $loop->iteration + (method_exists($items, 'currentPage') ? ($items->currentPage() - 1) * $items->perPage() : 0) }}</td>
                @if($showAset ?? true)
                <td>
                    <strong>{{ $item->aset->nama_aset ?? '-' }}</strong>
                    <br><small class="text-muted">{{ $item->aset->kode_aset ?? '' }}</small>
                </td>
                @endif
                <td>{{ \Carbon\Carbon::parse($item->tanggal_pemeliharaan)->format('d/m/Y') }}</td>
                <td>{{ $item->tanggal_selesai_pemeliharaan ? \Carbon\Carbon::parse($item->tanggal_selesai_pemeliharaan)->format('d/m/Y') : '-' }}</td>
                <td>
                    <div style="max-height: 80px; overflow-y: auto;">
                        <strong>Desc:</strong> {{ $item->deskripsi_pemeliharaan ?? '-' }}
                        @if($item->catatan_selesai)
                        <br><small class="text-success"><strong>Selesai:</strong> {{ $item->catatan_selesai }}</small>
                        @endif
                    </div>
                </td>
                <td>Rp {{ number_format($item->biaya_pemeliharaan ?? 0, 0, ',', '.') }}</td>
                <td>
                    @if($item->status == 'proses')
                        <span class="badge badge-warning">Proses</span>
                    @elseif($item->status == 'selesai')
                        <span class="badge badge-success">Selesai</span>
                    @else
                        <span class="badge badge-secondary">-</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        @if($item->status == 'proses')
                        <button type="button" class="btn btn-xs btn-success"
                                data-toggle="modal"
                                data-target="#modalSelesai"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->aset->nama_aset ?? 'Aset' }}"
                                title="Selesaikan Perbaikan">
                            <i class="fas fa-check-double"></i>
                        </button>
                        @endif
                        <a href="{{ route('manajemenasetdanasrama.pemeliharaan.edit', $item->id) }}" class="btn btn-xs btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-xs btn-danger"
                                data-toggle="modal"
                                data-target="#modalHapus"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->aset->nama_aset ?? 'Laporan ini' }}"
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ ($showAset ?? true) ? '8' : '7' }}" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Belum ada data pemeliharaan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
