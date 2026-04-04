<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use Modules\ManajemenAsetDanAsrama\Models\Aset;

class TrashController extends BaseController
{
    /**
     * Display a listing of trashed items.
     */
    public function index(Request $request): View
    {
        $pengajuanTrash = PengajuanAset::onlyTrashed()
                            ->with(['pengaju', 'deletedBy'])
                            ->latest('deleted_at')
                            ->get();
        
        $asetTrash = Aset::onlyTrashed()
                        ->with('deletedBy')
                        ->latest('deleted_at')
                        ->get();
        
        return view('manajemenasetdanasrama::trash.index', [
            'title'          => 'Data Terhapus (Trash)',
            'pengajuanTrash' => $pengajuanTrash,
            'asetTrash'      => $asetTrash,
        ]);
    }

    /**
     * Restore the specified trashed item.
     */
    public function restore(string $type, string $id): RedirectResponse
    {
        if ($type === 'pengajuan') {
            $item = PengajuanAset::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Pengajuan aset berhasil dipulihkan.';
        } elseif ($type === 'aset') {
            $item = Aset::onlyTrashed()->findOrFail($id);
            $item->restore();
            $message = 'Aset berhasil dipulihkan.';
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
        if ($type === 'pengajuan') {
            $item = PengajuanAset::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Pengajuan aset berhasil dihapus permanen.';
        } elseif ($type === 'aset') {
            $item = Aset::onlyTrashed()->findOrFail($id);
            $item->forceDelete();
            $message = 'Aset berhasil dihapus permanen.';
        } else {
            abort(404);
        }

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', $message);
    }
}