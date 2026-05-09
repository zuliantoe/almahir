<div class="table-responsive">
    <table class="table table-hover align-middle {{ $striped ?? 'table-striped' }} mb-0" style="width: 100%; min-width: 1000px;">
        <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
            <tr>
                <th style="width: 4%;" class="text-center py-3 border-top-0">No</th>
                <th style="width: 15%;" class="py-3 border-top-0">Kode Aset</th>
                <th style="width: 25%;" class="py-3 border-top-0">Nama Aset</th>
                <th style="width: 15%;" class="py-3 border-top-0 text-right">Harga</th>
                <th style="width: 12%;" class="py-3 border-top-0 text-center">Kondisi</th>
                <th style="width: 12%;" class="py-3 border-top-0 text-center">Tgl Pengadaan</th>
                <th style="width: 15%;" class="py-3 border-top-0 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration + (method_exists($items, 'currentPage') ? ($items->currentPage() - 1) * $items->perPage() : 0) }}</td>
                <td class="text-center" style="white-space: nowrap;"><code>{{ $item->kode_aset }}</code></td>
                <td><strong>{{ $item->nama_aset }}</strong></td>
                <td class="text-right font-weight-bold text-success" style="white-space: nowrap;">{{ $item->harga_formatted }}</td>
                <td class="text-center">{!! $item->status_badge !!}</td>
                <td class="text-center small" style="white-space: nowrap;">
                    <span class="badge badge-light border">
                        <i class="far fa-calendar-alt mr-1"></i> {{ $item->tanggal_pengadaan ? \Carbon\Carbon::parse($item->tanggal_pengadaan)->format('d/m/Y') : '-' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center" style="gap: 4px;">
                        <a href="{{ route('manajemenasetdanasrama.aset.show', $item->id) }}" class="btn btn-action-xs btn-info" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('manajemenasetdanasrama.aset.edit', $item->id) }}" class="btn btn-action-xs btn-warning" title="Edit">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        <a href="{{ route('manajemenasetdanasrama.aset.print-label') }}?id={{ $item->id }}" target="_blank" class="btn btn-action-xs btn-primary" title="Cetak Label">
                            <i class="fas fa-print"></i>
                        </a>
                        <button type="button" class="btn btn-action-xs btn-danger"
                                data-toggle="modal"
                                data-target="#modalHapus"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_aset }}"
                                data-url="{{ route('manajemenasetdanasrama.aset.destroy', $item->id) }}"
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @if($showExtendedActions ?? false)
                    <div class="d-flex justify-content-center mt-2" style="gap: 4px;">
                        <a href="{{ route('manajemenasetdanasrama.kerusakan.create') }}?aset_id={{ $item->id }}" class="btn btn-action-xs btn-outline-danger" title="Lapor Kerusakan">
                            <i class="fas fa-exclamation-triangle"></i>
                        </a>
                        <a href="{{ route('manajemenasetdanasrama.pemeliharaan.create') }}?aset_id={{ $item->id }}" class="btn btn-action-xs btn-outline-primary" title="Catat Pemeliharaan">
                            <i class="fas fa-wrench"></i>
                        </a>
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Belum ada data aset
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
