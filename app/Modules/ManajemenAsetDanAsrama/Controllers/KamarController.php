<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;

class KamarController extends BaseController
{
    /**
     * Display a listing of kamar.
     */
    public function index(Request $request): View
    {
        $kamar = Kamar::withCount('penghuni')->paginate(15);
        
        return view('manajemenasetdanasrama::kamar.index', [
            'title' => 'Data Kamar',
            'kamar' => $kamar,
        ]);
    }

    /**
     * Show the form for creating a new kamar.
     */
    public function create()
    {
        return redirect()->route('manajemenasetdanasrama.kamar.index');
    }

    /**
     * Store a newly created kamar in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:255|unique:kamar,nama_kamar',
            'kapasitas'  => 'required|integer|min:1',
            'deskripsi'  => 'nullable|string',
        ]);

        Kamar::create($validated);

        return redirect()->route('manajemenasetdanasrama.kamar.index')
            ->with('success', 'Kamar berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified kamar.
     */
    public function edit(string $id)
    {
        return redirect()->route('manajemenasetdanasrama.kamar.index');
    }

    /**
     * Update the specified kamar in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kamar = Kamar::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:255|unique:kamar,nama_kamar,' . $id,
            'kapasitas'  => 'required|integer|min:1',
            'deskripsi'  => 'nullable|string',
        ]);

        $kamar->update($validated);

        return redirect()->route('manajemenasetdanasrama.kamar.index')
            ->with('success', 'Kamar berhasil diperbarui.');
    }

    /**
     * Remove the specified kamar from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $kamar = Kamar::findOrFail($id);
        
        // Cek apakah masih ada penghuni
        if ($kamar->penghuni()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kamar tidak bisa dihapus karena masih memiliki penghuni.');
        }

        $kamar->delete();

        return redirect()->route('manajemenasetdanasrama.kamar.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }
}