<?php

namespace Modules\PegawaiManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\PegawaiManager\Models\Pegawai;

/**
 * PegawaiManagerController
 *
 * CRUD operations for PegawaiManager module.
 */
class PegawaiManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // TODO: Implement listing logic
        $pegawaiManagers = Pegawai::with(['user', 'typePegawai'])->get();

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
        return view('pegawaimanager::create', [
            'title' => 'Tambah PegawaiManager',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Create record

        return redirect()->route('pegawaimanager.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // TODO: Find record
        $pegawaiManager = null;

        return view('pegawaimanager::show', [
            'title' => 'Detail PegawaiManager',
            'pegawaiManager' => $pegawaiManager,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // TODO: Find record
        $pegawaiManager = null;

        return view('pegawaimanager::edit', [
            'title' => 'Edit PegawaiManager',
            'pegawaiManager' => $pegawaiManager,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Update record

        return redirect()->route('pegawaimanager.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // TODO: Delete record

        return redirect()->route('pegawaimanager.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
