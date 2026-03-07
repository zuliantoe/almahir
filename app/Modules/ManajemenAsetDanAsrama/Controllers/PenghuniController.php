<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use Modules\ManajemenAsetDanAsrama\Models\Kamar;
use Modules\Siswa\Models\Siswa;

class PenghuniController extends BaseController
{
    /**
     * Display a listing of penghuni.
     */
    public function index(Request $request): View
    {
        $penghuni = KamarPenghuni::with(['kamar', 'siswa'])
                        ->latest()
                        ->paginate(15);
        
        return view('manajemenasetdanasrama::penghuni.index', [
            'title'    => 'Data Penghuni Kamar',
            'penghuni' => $penghuni,
        ]);
    }

    /**
     * Show the form for creating a new penghuni.
     */
    public function create(): View
    {
        $kamar = Kamar::all();
        $siswa = Siswa::all();
        
        return view('manajemenasetdanasrama::penghuni.create', [
            'title' => 'Tambah Penghuni Kamar',
            'kamar' => $kamar,
            'siswa' => $siswa,
        ]);
    }

    /**
     * Store a newly created penghuni in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kamar_id'      => 'required|exists:kamar,id',
            'siswa_id'      => 'required|exists:siswa,id|unique:kamar_penghuni,siswa_id',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar'=> 'nullable|date|after:tanggal_masuk',
            'keterangan'    => 'nullable|string',
        ]);

        KamarPenghuni::create($validated);

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified penghuni.
     */
    public function edit(string $id): View
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        $kamar = Kamar::all();
        $siswa = Siswa::all();
        
        return view('manajemenasetdanasrama::penghuni.edit', [
            'title'    => 'Edit Penghuni Kamar',
            'penghuni' => $penghuni,
            'kamar'    => $kamar,
            'siswa'    => $siswa,
        ]);
    }

    /**
     * Update the specified penghuni in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        
        $validated = $request->validate([
            'kamar_id'      => 'required|exists:kamar,id',
            'siswa_id'      => 'required|exists:siswa,id|unique:kamar_penghuni,siswa_id,' . $id,
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar'=> 'nullable|date|after:tanggal_masuk',
            'keterangan'    => 'nullable|string',
        ]);

        $penghuni->update($validated);

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni berhasil diperbarui.');
    }

    /**
     * Remove the specified penghuni from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $penghuni = KamarPenghuni::findOrFail($id);
        $penghuni->delete();

        return redirect()->route('manajemenasetdanasrama.penghuni.index')
            ->with('success', 'Penghuni berhasil dihapus.');
    }
}