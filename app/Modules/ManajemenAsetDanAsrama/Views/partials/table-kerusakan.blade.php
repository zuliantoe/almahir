<div class="table-responsive">
    <table class="table table-bordered table-hover {{ $striped ?? 'table-striped' }} {{ $tableClass ?? '' }}">
        <thead class="{{ $theadClass ?? 'thead-light' }}">
            <tr>
                <th width="50">No</th>
                @if($showAset ?? true)
                <th>Nama Aset</th>
                @endif
                <th width="130">Tanggal</th>
                <th width="120">Tingkat</th>
                <th width="150">Status</th>
                <th>Deskripsi</th>
                <th width="{{ $actionWidth ?? '150' }}">Aksi</th>
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
                <td>{{ \Carbon\Carbon::parse($item->tanggal_kerusakan)->format('d/m/Y') }}</td>
                <td>
                    @if($item->tingkat_kerusakan == 'ringan')
                        <span class="badge badge-info">Ringan</span>
                    @elseif($item->tingkat_kerusakan == 'sedang')
                        <span class="badge badge-warning">Sedang</span>
                    @elseif($item->tingkat_kerusakan == 'berat')
                        <span class="badge badge-danger">Berat</span>
                    @endif
                </td>
                <td>
                    @if($item->status_penanganan == 'belum_ditangani')
                        <span class="badge badge-danger">Belum Ditangani</span>
                    @elseif($item->status_penanganan == 'sedang_ditangani')
                        <span class="badge badge-warning">Sedang Ditangani</span>
                    @elseif($item->status_penanganan == 'selesai')
                        <span class="badge badge-success">Selesai</span>
                    @endif
                </td>
                <td>{{ Str::limit($item->deskripsi_kerusakan ?? '-', 50) }}</td>
                <td>
                    <div class="btn-group">
                        @if($item->status_penanganan == 'belum_ditangani')
                        <form action="{{ route('manajemenasetdanasrama.kerusakan.proses-pemeliharaan', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-success" title="Proses Pemeliharaan">
                                <i class="fas fa-wrench"></i> Proses
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('manajemenasetdanasrama.kerusakan.edit', $item->id) }}" class="btn btn-xs btn-warning" title="Edit">
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
                <td colspan="{{ ($showAset ?? true) ? '7' : '6' }}" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i> Belum ada data kerusakan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
