<?php

namespace Modules\Akademik\Controllers;
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\Http\Requests\AkademikRequest\StoreKelasRequest;
use App\Http\Requests\AkademikRequest\UpdateKelasRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Akademik\Models\kelas as ModelsKelas;
use Modules\Guru\Models\Guru;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $kelas = ModelsKelas::with('walikelas')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('namakelas', 'like', '%' . $request->search . '%')
                      ->orWhere('jenjang', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('namakelas')
            ->paginate(10)
            ->withQueryString();

        return view('akademik::kelas.index', compact('kelas'));
    }

    public function create()
    {
         $guru = Guru::orderBy('nama')->get();

        return view('akademik::kelas.create');
    }

    public function store(StoreKelasRequest $request)
    {
        $validated = $request->validate([
            'namakelas' => 'required|string|max:255',
            'jenjang'   => 'required|string|max:50',
            'guru_id'   => 'nullable|integer',
        ]);

        ModelsKelas::create($validated);

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
        return view('akademik::kelas.edit', compact('kelas'));
    }

    public function update(UpdateKelasRequest $request, ModelsKelas $kelas)
    {
        $validated = $request->validate([
            'namakelas' => 'required|string|max:255',
            'jenjang'   => 'required|string|max:50',
            'guru_id'   => 'nullable|integer',
        ]);

        $kelas->update($validated);

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
