<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;
use App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use App\Modules\ManajemenAsetDanAsrama\Models\Pemeliharaan;

class TrashController extends BaseController
{
    /**
     * Display a listing of trashed items.
     */
    public function index(Request $request): View
    {
        $asetTrash = Aset::onlyTrashed()
                        ->with('deletedBy')
                        ->latest('deleted_at')
                        ->get();

        $pengajuanTrash = PengajuanAset::onlyTrashed()
                            ->with('deletedBy')
                            ->latest('deleted_at')
                            ->get();

        $kerusakanTrash = Kerusakan::onlyTrashed()
                            ->with(['aset:id,nama_aset,kode_aset', 'deletedBy'])
                            ->latest('deleted_at')
                            ->get();

        $pemeliharaanTrash = Pemeliharaan::onlyTrashed()
                            ->with(['aset:id,nama_aset,kode_aset', 'deletedBy'])
                            ->latest('deleted_at')
                            ->get();
        
        return view('manajemenasetdanasrama::trash.index', [
            'title'             => 'Data Terhapus (Trash)',
            'asetTrash'         => $asetTrash,
            'pengajuanTrash'    => $pengajuanTrash,
            'kerusakanTrash'    => $kerusakanTrash,
            'pemeliharaanTrash' => $pemeliharaanTrash,
        ]);
    }

    /**
     * Restore the specified trashed item.
     */
    public function restore(string $type, string $id): RedirectResponse
    {
        if ($type === 'aset') {
            $item = Aset::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Aset berhasil dipulihkan.';
        } elseif ($type === 'pengajuan') {
            $item = PengajuanAset::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Pengajuan aset berhasil dipulihkan.';
        } elseif ($type === 'kerusakan') {
            $item = Kerusakan::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Laporan kerusakan berhasil dipulihkan.';
        } elseif ($type === 'pemeliharaan') {
            $item = Pemeliharaan::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Data pemeliharaan berhasil dipulihkan.';
        } else {
            abort(404);
        }

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', $message);
    }

    /**
     * Permanently delete the specified trashed item.
     */
    public function forceDelete(string $type, string $id): RedirectResponse
    {
        if ($type === 'aset') {
            $item = Aset::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Aset berhasil dihapus permanen.';
        } elseif ($type === 'pengajuan') {
            $item = PengajuanAset::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Pengajuan aset berhasil dihapus permanen.';
        } elseif ($type === 'kerusakan') {
            $item = Kerusakan::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Laporan kerusakan berhasil dihapus permanen.';
        } elseif ($type === 'pemeliharaan') {
            $item = Pemeliharaan::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Data pemeliharaan berhasil dihapus permanen.';
        } else {
            abort(404);
        }

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', $message);
    }

    /**
     * Bulk permanently delete trashed items based on pattern.
     */
    public function bulkForceDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => 'required|in:aset,pengajuan',
            'pattern' => 'required|string|min:2',
        ]);

        $type = $request->type;
        $pattern = strtoupper($request->pattern);

        if ($type === 'aset') {
            $query = Aset::onlyTrashed()->where('kode_aset', 'LIKE', "{$pattern}%");
        } else {
            $query = PengajuanAset::onlyTrashed()->where('nomor_pengajuan', 'LIKE', "{$pattern}%");
        }

        $count = $query->count();
        if ($count === 0) {
            return redirect()->back()->with('error', "Tidak ditemukan data ".ucfirst($type)." di sampah dengan pola '{$pattern}'.");
        }

        $query->forceDelete();

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', "Berhasil menghapus permanen {$count} data {$type} dengan pola '{$pattern}'.");
    }

    /**
     * Empty all trash.
     */
    public function emptyTrash(): RedirectResponse
    {
        Aset::onlyTrashed()->forceDelete();
        PengajuanAset::onlyTrashed()->forceDelete();
        Kerusakan::onlyTrashed()->forceDelete();
        Pemeliharaan::onlyTrashed()->forceDelete();

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', 'Semua data di sampah berhasil dihapus permanen.');
    }
}