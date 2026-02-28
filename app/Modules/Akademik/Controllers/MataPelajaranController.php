<?php

namespace Modules\Akademik\Controllers;

// use App\Http\Controllers\Controller;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\KategoriPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::with('kategori');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $mataPelajaran = $query
            ->orderBy('kode', 'asc')
            ->paginate(10)
            ->withQueryString();

        $kategoriList = KategoriPelajaran::orderBy('kategori')->get();

        return view('akademik::mata-pelajaran.index', compact(
            'mataPelajaran',
            'kategoriList'
        ));
    }

    public function create()
    {
        $kategoriList = KategoriPelajaran::orderBy('kategori')->get();
        return view('akademik::mata-pelajaran.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:mata_pelajaran,kode',
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_pelajaran,id',
        ]);

        MataPelajaran::create($request->only([
            'kode',
            'nama',
            'kategori_id'
        ]));

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load('kategori');
        return view('akademik::mata-pelajaran.show', compact('mataPelajaran'));
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $kategoriList = KategoriPelajaran::orderBy('kategori')->get();
        return view('akademik::mata-pelajaran.edit', compact(
            'mataPelajaran',
            'kategoriList'
        ));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mata_pelajaran', 'kode')
                    ->ignore($mataPelajaran->id),
            ],
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_pelajaran,id',
        ]);

        $mataPelajaran->update($request->only([
            'kode',
            'nama',
            'kategori_id'
        ]));

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
