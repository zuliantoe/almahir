<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AkademikRequest\StoreJenisKegiatanRequest as AkademikRequestStoreJenisKegiatanRequest;
use App\Http\Requests\AkademikRequest\UpdateJenisKegiatanRequest as AkademikRequestUpdateJenisKegiatanRequest;

use App\Modules\Akademik\Models\JenisKegiatan as ModelsJenisKegiatan;
use Illuminate\Http\Request;

class JenisKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = ModelsJenisKegiatan::query();

        // Search
        if ($request->filled('search')) {
            $query->where('jeniskegiatan', 'like', '%' . $request->search . '%');
        }

        $jenisKegiatan = $query
            ->withCount('kalenderAkademik')
            ->orderBy('jeniskegiatan', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('akademik::jenis-kegiatan.index', compact('jenisKegiatan'));
    }

    public function create()
    {
        return view('akademik::jenis-kegiatan.create');
    }

    public function store(AkademikRequestStoreJenisKegiatanRequest $request)
    {
        ModelsJenisKegiatan::create($request->validated());

        return redirect()
            ->route('akademik.jenis-kegiatan.index')
            ->with('success', 'Jenis kegiatan berhasil ditambahkan.');
    }

    public function show(ModelsJenisKegiatan $jenisKegiatan)
    {
        $jenisKegiatan->loadCount('kalenderAkademik');

        return view('akademik::jenis-kegiatan.show', compact('jenisKegiatan'));
    }

    public function edit(ModelsJenisKegiatan $jenisKegiatan)
    {
        return view('akademik::jenis-kegiatan.edit', compact('jenisKegiatan'));
    }

    public function update(AkademikRequestUpdateJenisKegiatanRequest $request, ModelsJenisKegiatan $jenisKegiatan)
    {
        $jenisKegiatan->update($request->validated());

        return redirect()
            ->route('akademik.jenis-kegiatan.index')
            ->with('success', 'Jenis kegiatan berhasil diperbarui.');
    }

    public function destroy(ModelsJenisKegiatan $jenisKegiatan)
    {
        if ($jenisKegiatan->kalenderAkademik()->exists()) {
            return redirect()
                ->route('akademik.jenis-kegiatan.index')
                ->with('error', 'Jenis kegiatan tidak dapat dihapus karena masih digunakan.');
        }

        $jenisKegiatan->delete();

        return redirect()
            ->route('akademik.jenis-kegiatan.index')
            ->with('success', 'Jenis kegiatan berhasil dihapus.');
    }
}
