<?php

namespace Modules\Guru\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Guru\Models\Guru;

/**
 * GuruController
 * 
 * CRUD operations for teacher data management.
 */
class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $guru = $query->latest()->paginate(20);

        return view('guru::index', [
            'title' => 'Data Guru',
            'breadcrumb' => 'Master Data / Guru',
            'guru' => $guru,
        ]);
    }

    public function create()
    {
        return view('guru::create', [
            'title' => 'Tambah Guru Baru',
            'breadcrumb' => 'Master Data / Guru / Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:30|unique:guru,nip',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif,pensiun',
        ]);

        Guru::create($validated);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $guru = Guru::findOrFail($id);

        return view('guru::show', [
            'title' => 'Detail Guru',
            'breadcrumb' => 'Master Data / Guru / Detail',
            'guru' => $guru,
        ]);
    }

    public function edit(string $id)
    {
        $guru = Guru::findOrFail($id);

        return view('guru::edit', [
            'title' => 'Edit Guru',
            'breadcrumb' => 'Master Data / Guru / Edit',
            'guru' => $guru,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nip' => 'nullable|string|max:30|unique:guru,nip,' . $id,
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif,pensiun',
        ]);

        $guru->update($validated);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $guru = Guru::findOrFail($id);
        
        // Check if guru has linked user account
        if ($guru->user) {
            return back()->with('error', 'Tidak dapat menghapus guru yang memiliki akun user terdaftar.');
        }

        $guru->delete();

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }
}
