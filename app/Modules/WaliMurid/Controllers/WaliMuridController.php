<?php

namespace Modules\WaliMurid\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\WaliMurid\Models\WaliMurid;

/**
 * WaliMuridController
 * 
 * CRUD operations for parent/guardian data management.
 */
class WaliMuridController extends Controller
{
    public function index(Request $request)
    {
        $query = WaliMurid::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%");
            });
        }

        $waliMurid = $query->latest()->paginate(20);

        return view('walimurid::index', [
            'title' => 'Data Wali Murid',
            'breadcrumb' => 'Master Data / Wali Murid',
            'waliMurid' => $waliMurid,
        ]);
    }

    public function create()
    {
        return view('walimurid::create', [
            'title' => 'Tambah Wali Murid',
            'breadcrumb' => 'Master Data / Wali Murid / Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:wali_murid,email',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:100',
            'hubungan' => 'required|in:ayah,ibu,wali',
        ]);

        WaliMurid::create($validated);

        return redirect()->route('walimurid.index')
            ->with('success', 'Data wali murid berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $waliMurid = WaliMurid::findOrFail($id);

        return view('walimurid::edit', [
            'title' => 'Edit Wali Murid',
            'breadcrumb' => 'Master Data / Wali Murid / Edit',
            'waliMurid' => $waliMurid,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $waliMurid = WaliMurid::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:wali_murid,email,' . $id,
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:100',
            'hubungan' => 'required|in:ayah,ibu,wali',
        ]);

        $waliMurid->update($validated);

        return redirect()->route('walimurid.index')
            ->with('success', 'Data wali murid berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $waliMurid = WaliMurid::findOrFail($id);
        
        if ($waliMurid->user) {
            return back()->with('error', 'Tidak dapat menghapus wali murid yang memiliki akun user.');
        }

        $waliMurid->delete();

        return redirect()->route('walimurid.index')
            ->with('success', 'Data wali murid berhasil dihapus.');
    }
}
