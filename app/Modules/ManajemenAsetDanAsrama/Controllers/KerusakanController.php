<?php

namespace Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use Modules\ManajemenAsetDanAsrama\Models\Aset;

class KerusakanController extends BaseController
{
    /**
     * Display a listing of kerusakan.
     */
    public function index(Request $request): View
    {
        $kerusakan = Kerusakan::with('aset')
                        ->latest()
                        ->paginate(15);
        
        return view('manajemenasetdanasrama::kerusakan.index', [
            'title'     => 'Data Kerusakan Aset',
            'kerusakan' => $kerusakan,
        ]);
    }

    /**
     * Show the form for creating a new kerusakan.
     */
    public function create(): View
    {
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::kerusakan.create', [
            'title' => 'Lapor Kerusakan Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Store a newly created kerusakan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'aset_id'          => 'required|exists:aset,id',
            'tanggal_kerusakan'=> 'required|date',
            'deskripsi_kerusakan' => 'required|string',
            'tingkat_kerusakan' => 'required|in:ringan,sedang,berat',
            'status_penanganan' => 'required|in:belum_ditangani,sedang_ditangani,selesai',
            'catatan'          => 'nullable|string',
        ]);

        Kerusakan::create($validated);

        // Update status aset jika diperlukan (misal jika rusak berat)
        if ($validated['tingkat_kerusakan'] == 'berat') {
            Aset::where('id', $validated['aset_id'])->update(['status_kondisi' => 'rusak']);
        }

        return redirect()->route('manajemenasetdanasrama.kerusakan.index')
            ->with('success', 'Laporan kerusakan berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified kerusakan.
     */
    public function edit(string $id): View
    {
        $kerusakan = Kerusakan::findOrFail($id);
        $aset = Aset::all();
        
        return view('manajemenasetdanasrama::kerusakan.edit', [
            'title'     => 'Edit Kerusakan Aset',
            'kerusakan' => $kerusakan,
            'aset'      => $aset,
        ]);
    }

    /**
     * Update the specified kerusakan in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kerusakan = Kerusakan::findOrFail($id);
        
        $validated = $request->validate([
            'aset_id'          => 'required|exists:aset,id',
            'tanggal_kerusakan'=> 'required|date',
            'deskripsi_kerusakan' => 'required|string',
            'tingkat_kerusakan' => 'required|in:ringan,sedang,berat',
            'status_penanganan' => 'required|in:belum_ditangani,sedang_ditangani,selesai',
            'catatan'          => 'nullable|string',
        ]);

        $kerusakan->update($validated);

        return redirect()->route('manajemenasetdanasrama.kerusakan.index')
            ->with('success', 'Data kerusakan berhasil diperbarui.');
    }

    /**
     * Remove the specified kerusakan from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $kerusakan = Kerusakan::findOrFail($id);
        $kerusakan->delete();

        return redirect()->route('manajemenasetdanasrama.kerusakan.index')
            ->with('success', 'Kerusakan berhasil dihapus.');
    }
}