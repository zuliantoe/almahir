<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\KategoriPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = KategoriPelajaran::query();

        if ($request->filled('search')) {
            $query->where('kategori', 'like', '%' . $request->search . '%');
        }

        $kategoriPelajaran = $query
            ->withCount('mataPelajaran')
            ->orderBy('kategori', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('akademik::kategori-pelajaran.index', compact('kategoriPelajaran'));
    }

    public function create()
    {
        return view('akademik::kategori-pelajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:100|unique:kategori_pelajaran,kategori',
        ], [
            'kategori.required' => 'Nama kategori wajib diisi.',
            'kategori.max' => 'Nama kategori maksimal 100 karakter.',
            'kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        KategoriPelajaran::create([
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('akademik.kategori-pelajaran.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(KategoriPelajaran $kategoriPelajaran)
    {
        $kategoriPelajaran->loadCount('mataPelajaran');

        return view('akademik::kategori-pelajaran.show', compact('kategoriPelajaran'));
    }

    public function edit(KategoriPelajaran $kategoriPelajaran)
    {
        return view('akademik::kategori-pelajaran.edit', compact('kategoriPelajaran'));
    }

    public function update(Request $request, KategoriPelajaran $kategoriPelajaran)
    {
        $request->validate([
            'kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori_pelajaran', 'kategori')
                    ->ignore($kategoriPelajaran->id),
            ],
        ], [
            'kategori.required' => 'Nama kategori wajib diisi.',
            'kategori.max' => 'Nama kategori maksimal 100 karakter.',
            'kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $kategoriPelajaran->update([
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('akademik.kategori-pelajaran.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriPelajaran $kategoriPelajaran)
    {
        if ($kategoriPelajaran->mataPelajaran()->exists()) {
            return redirect()->route('akademik.kategori-pelajaran.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }

        $kategoriPelajaran->delete();

        return redirect()->route('akademik.kategori-pelajaran.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
