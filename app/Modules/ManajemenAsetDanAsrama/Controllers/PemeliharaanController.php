<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\Pemeliharaan;
use Modules\ManajemenAsetDanAsrama\Models\Aset;

class PemeliharaanController extends BaseController
{
    /**
     * Display a listing of pemeliharaan.
     */
    public function index(Request $request): View
    {
        $pemeliharaan = Pemeliharaan::with('aset')
                            ->latest()
                            ->paginate(15);
        
        return view('manajemenasetdanasrama::pemeliharaan.index', [
            'title'        => 'Data Pemeliharaan Aset',
            'pemeliharaan' => $pemeliharaan,
        ]);
    }

    /**
     * Show the form for creating a new pemeliharaan.
     */
    public function create(): View
    {
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::pemeliharaan.create', [
            'title' => 'Tambah Pemeliharaan Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Store a newly created pemeliharaan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'aset_id'           => 'required|exists:aset,id',
            'tanggal_pemeliharaan' => 'required|date',
            'deskripsi_pemeliharaan' => 'required|string',
            'biaya'             => 'required|numeric|min:0',
            'catatan'           => 'nullable|string',
        ]);

        Pemeliharaan::create($validated);

        return redirect()->route('manajemenasetdanasrama.pemeliharaan.index')
            ->with('success', 'Data pemeliharaan berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified pemeliharaan.
     */
    public function edit(string $id): View
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::pemeliharaan.edit', [
            'title'        => 'Edit Pemeliharaan Aset',
            'pemeliharaan' => $pemeliharaan,
            'aset'         => $aset,
        ]);
    }

    /**
     * Update the specified pemeliharaan in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        
        $validated = $request->validate([
            'aset_id'           => 'required|exists:aset,id',
            'tanggal_pemeliharaan' => 'required|date',
            'deskripsi_pemeliharaan' => 'required|string',
            'biaya'             => 'required|numeric|min:0',
            'catatan'           => 'nullable|string',
        ]);

        $pemeliharaan->update($validated);

        return redirect()->route('manajemenasetdanasrama.pemeliharaan.index')
            ->with('success', 'Data pemeliharaan berhasil diperbarui.');
    }

    /**
     * Remove the specified pemeliharaan from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $pemeliharaan->delete();

        return redirect()->route('manajemenasetdanasrama.pemeliharaan.index')
            ->with('success', 'Pemeliharaan berhasil dihapus.');
    }
}