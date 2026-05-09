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
