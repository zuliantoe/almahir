<?php

namespace Modules\Akademik\Controllers;
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\Http\Requests\AkademikRequest\StoreKelasRequest;
use App\Http\Requests\AkademikRequest\UpdateKelasRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Akademik\Models\kelas as ModelsKelas;
use App\Modules\Akademik\Models\Tingkat;
use Modules\Guru\Models\Guru;


class KelasController extends Controller
{
    public function index(Request $request)
    {
        $kelas = ModelsKelas::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama_kelas', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('nama_kelas')
            ->paginate(10)
            ->withQueryString();

        return view('akademik::kelas.index', compact('kelas'));
    }

    public function create()
    {
        $guru = Guru::orderBy('nama')->get();
        $tingkat = Tingkat::orderBy('nama_tingkat')->get();

        return view('akademik::kelas.create', compact('guru', 'tingkat'));
    }

    public function store(StoreKelasRequest $request)
    {
        ModelsKelas::create($request->validated());

        return redirect()
            ->route('akademik.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(ModelsKelas $kelas)
    {
        $kelas->loadCount(['jadwalPelajaran', 'kurikulum']);

        return view('akademik::kelas.show', compact('kelas'));
    }

    public function edit(ModelsKelas $kelas)
    {
        $guru = Guru::orderBy('nama')->get();
        $tingkat = Tingkat::orderBy('nama_tingkat')->get();

        return view('akademik::kelas.edit', compact('kelas', 'guru', 'tingkat'));
    }

    public function update(UpdateKelasRequest $request, ModelsKelas $kelas)
    {
        $kelas->update($request->validated());

        return redirect()
            ->route('akademik.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(ModelsKelas $kelas)
    {
        if (
            $kelas->jadwalPelajaran()->exists() ||
            $kelas->kurikulum()->exists()
        ) {
            return redirect()
                ->route('akademik.kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih digunakan.');
        }

        $kelas->delete();

        return redirect()
            ->route('akademik.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
