<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\Aset;

class AsetController extends BaseController
{
    /**
     * Display a listing of master aset.
     */
    public function index(Request $request): View
    {
        $aset = Aset::with('pengadaan.pengajuan')
                    ->whereNull('deleted_at')
                    ->latest()
                    ->paginate(15);
        
        return view('manajemenasetdanasrama::aset.index', [
            'title' => 'Master Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Display the specified aset.
     */
    public function show(string $id): View
    {
        $aset = Aset::with(['pengadaan.pengajuan', 'kerusakan', 'pemeliharaan'])
                    ->findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.show', [
            'title' => 'Detail Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Show the form for editing the specified aset.
     */
    public function edit(string $id): View
    {
        $aset = Aset::findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.edit', [
            'title' => 'Edit Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Update the specified aset in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $aset = Aset::findOrFail($id);
        
        $validated = $request->validate([
            'nama_aset'       => 'required|string|max:255',
            'status_kondisi'  => 'required|in:baik,rusak,dalam_perbaikan,sudah_diperbaiki',
            'kondisi'         => 'nullable|string',
            'deskripsi_aset'  => 'nullable|string',
        ]);

        $aset->update($validated);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Remove the specified aset from storage (soft delete).
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'alasan_hapus' => 'required|string'
        ]);

        $aset = Aset::findOrFail($id);
        $aset->deleted_by = auth()->id;
        $aset->alasan_hapus = $request->alasan_hapus;
        $aset->save();
        $aset->delete();

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Aset berhasil dipindahkan ke trash.');
    }
}