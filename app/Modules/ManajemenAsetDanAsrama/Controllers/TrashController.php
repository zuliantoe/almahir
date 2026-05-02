<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;

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
        
        return view('manajemenasetdanasrama::trash.index', [
            'title'          => 'Data Terhapus (Trash)',
            'asetTrash'      => $asetTrash,
            'pengajuanTrash' => $pengajuanTrash,
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
        } else {
            abort(404);
        }

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', $message);
    }
}