<div class="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th width="40" class="text-center">NO</th>
                @if($showAset ?? true)
                <th>ASET</th>
                @endif
                <th>DESKRIPSI PEKERJAAN</th>
                <th width="110">JENIS</th>
                <th width="110">STATUS</th>
                <th width="110" class="text-center">TGL MULAI</th>
                <th width="110" class="text-center">TGL SELESAI</th>
                <th width="{{ $actionWidth ?? '100' }}" class="text-center">AKSI</th>
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
                <td>{{ Str::limit($item->deskripsi_pekerjaan ?? $item->catatan ?? '-', 80) }}</td>
                <td>
                    @php
                        $jenisClass = match($item->jenis_pemeliharaan ?? '') {
                            'preventif' => 'badge-info',
                            'korektif'  => 'badge-warning',
                            default     => 'badge-secondary',
                        };
                    @endphp
                    <span class="badge {{ $jenisClass }}">{{ ucfirst($item->jenis_pemeliharaan ?? 'Lainnya') }}</span>
                </td>
                <td>
                    @php
                        $statusClass = match($item->status ?? '') {
                            'proses'  => 'badge-warning',
                            'selesai' => 'badge-success',
                            'batal'   => 'badge-danger',
                            default   => 'badge-secondary',
                        };
                        $statusLabel = match($item->status ?? '') {
                            'proses'  => 'Sedang Proses',
                            'selesai' => 'Selesai',
                            'batal'   => 'Batal',
                            default   => ucfirst($item->status ?? '-'),
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td class="text-center">{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center" style="gap: 4px;">
                        @if(($item->status ?? '') == 'proses')
                        <button type="button" class="btn btn-action-xs btn-success"
                                data-toggle="modal"
                                data-target="#modalSelesai"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->aset->nama_aset ?? 'Aset' }}"
                                title="Selesaikan Perbaikan">
                            <i class="fas fa-check-double"></i>
                        </button>
                        @endif
                        <a href="{{ route('manajemenasetdanasrama.pemeliharaan.edit', $item->id) }}" class="btn btn-action-xs btn-warning" title="Edit">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        @if(!($hideDelete ?? false))
                        <button type="button" class="btn btn-action-xs btn-danger"
                                data-toggle="modal"
                                data-target="#modalHapus"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->aset->nama_aset ?? 'Laporan ini' }}"
                                data-url="{{ route('manajemenasetdanasrama.pemeliharaan.destroy', $item->id) }}"
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
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
