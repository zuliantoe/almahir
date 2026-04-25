<?php

namespace Modules\PegawaiManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PegawaiManager\Models\TypePegawai;

class TypePegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $types = TypePegawai::withCount('pegawai')->latest()->paginate(10);

        return view('pegawaimanager::type-pegawai.index', [
            'title' => 'Tipe Pegawai',
            'types' => $types,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pegawaimanager::type-pegawai.create', [
            'title' => 'Tambah Tipe Pegawai',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_type' => 'required|string|max:255',
        ]);

        TypePegawai::create($validated);

        return redirect()->route('pegawaimanager.types.index')
            ->with('success', 'Tipe pegawai berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $type = TypePegawai::findOrFail($id);

        return view('pegawaimanager::type-pegawai.edit', [
            'title' => 'Edit Tipe Pegawai',
            'type' => $type,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $type = TypePegawai::findOrFail($id);

        $validated = $request->validate([
            'nama_type' => 'required|string|max:255',
        ]);

        $type->update($validated);

        return redirect()->route('pegawaimanager.types.index')
            ->with('success', 'Tipe pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $type = TypePegawai::findOrFail($id);
        
        // Optional: Check if type is used by any pegawai
        if ($type->pegawai()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tipe pegawai tidak bisa dihapus karena masih digunakan oleh pegawai.');
        }

        $type->delete();

        return redirect()->route('pegawaimanager.types.index')
            ->with('success', 'Tipe pegawai berhasil dihapus.');
    }
}
