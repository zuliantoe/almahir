<div class="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th width="40" class="text-center">NO</th>
                @if($showAset ?? true)
                <th>ASET</th>
                @endif
                <th>DESKRIPSI KERUSAKAN</th>
                <th width="110">TINGKAT</th>
                <th width="130">STATUS</th>
                <th width="100" class="text-center">TANGGAL</th>
                <th width="100" class="text-center">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                @if($showAset ?? true)
                <td>
                    <strong>{{ $item->aset->nama_aset ?? '-' }}</strong><br>
                    <small class="text-muted">{{ $item->aset->kode_aset ?? '' }}</small>
                </td>
                @endif
                <td>{{ Str::limit($item->deskripsi_kerusakan, 80) }}</td>
                <td>
                    @php
                        $tingkatClass = match($item->tingkat_kerusakan) {
                            'ringan' => 'badge-info',
                            'sedang' => 'badge-warning',
                            'berat'  => 'badge-danger',
                            default  => 'badge-secondary',
                        };
                    @endphp
                    <span class="badge {{ $tingkatClass }}">{{ ucfirst($item->tingkat_kerusakan) }}</span>
                </td>
                <td>
                    @php
                        $statusClass = match($item->status_penanganan) {
                            'belum_ditangani'   => 'badge-danger',
                            'sedang_ditangani'  => 'badge-warning',
                            'sudah_ditangani'   => 'badge-success',
                            default             => 'badge-secondary',
                        };
                        $statusLabel = match($item->status_penanganan) {
                            'belum_ditangani'   => 'Belum Ditangani',
                            'sedang_ditangani'  => 'Sedang Ditangani',
                            'sudah_ditangani'   => 'Sudah Ditangani',
                            default             => ucfirst($item->status_penanganan),
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td class="text-center">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center" style="gap: 4px;">
                        @if($item->status_penanganan == 'belum_ditangani')
                        <form action="{{ route('manajemenasetdanasrama.kerusakan.proses-pemeliharaan', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-action-xs btn-success" title="Proses Pemeliharaan">
                                <i class="fas fa-wrench"></i>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('manajemenasetdanasrama.kerusakan.edit', $item->id) }}" class="btn btn-action-xs btn-warning" title="Edit">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        @if(!($hideDelete ?? false))
                        <button type="button" class="btn btn-action-xs btn-danger"
                                data-toggle="modal"
                                data-target="#modalHapus"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->aset->nama_aset ?? 'Laporan ini' }}"
                                data-url="{{ route('manajemenasetdanasrama.kerusakan.destroy', $item->id) }}"
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
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
