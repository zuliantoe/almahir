<?php

namespace Modules\PegawaiManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\PegawaiManager\Models\TypePegawai;

class PegawaiManagerController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pegawaiManagers = Pegawai::with(['user', 'typePegawai'])->latest()->get();

        return view('pegawaimanager::index', [
            'title' => 'Daftar Pegawai',
            'pegawaiManagers' => $pegawaiManagers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $types = TypePegawai::all();

        return view('pegawaimanager::create', [
            'title' => 'Tambah Pegawai',
            'types' => $types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'type_pegawai_id' => 'required|uuid',
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
        ]);

        Pegawai::create([
            'nama' => $validated['nama'],
            'user_id' => Auth::id(),
            'type_pegawai_id' => $validated['type_pegawai_id'],
            'email' => $validated['email'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
        ]);

        return redirect()->route('pegawaimanager.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $pegawaiManager = Pegawai::findOrFail($id);

        return view('pegawaimanager::show', [
            'title' => 'Detail Pegawai',
            'pegawaiManager' => $pegawaiManager,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $pegawaiManager = Pegawai::findOrFail($id);
        $types = TypePegawai::all();

        return view('pegawaimanager::edit', [
            'title' => 'Edit Pegawai',
            'pegawaiManager' => $pegawaiManager,
            'types' => $types
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'type_pegawai_id' => 'required|uuid',
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
        ]);

        $pegawai->update($validated);

        return redirect()->route('pegawaimanager.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $pegawai = Pegawai::findOrFail($id);

        $pegawai->delete();

        return redirect()->route('pegawaimanager.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}
