                <td class="text-center">
                    <div class="d-flex justify-content-center" style="gap: 4px;">
                        @if($item->status == 'proses')
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
