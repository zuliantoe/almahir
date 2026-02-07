<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * AkademikController
 * 
 * CRUD operations for Akademik module.
 */
class AkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // TODO: Implement listing logic
        $akademiks = collect();
        
        return view('akademik::index', [
            'title' => 'Daftar Akademik',
            'akademiks' => $akademiks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('akademik::create', [
            'title' => 'Tambah Akademik',
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

        return redirect()->route('akademik.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // TODO: Find record
        $akademik = null;
        
        return view('akademik::show', [
            'title' => 'Detail Akademik',
            'akademik' => $akademik,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // TODO: Find record
        $akademik = null;
        
        return view('akademik::edit', [
            'title' => 'Edit Akademik',
            'akademik' => $akademik,
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

        return redirect()->route('akademik.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // TODO: Delete record

        return redirect()->route('akademik.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
