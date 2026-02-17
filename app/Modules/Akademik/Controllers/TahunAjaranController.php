<?php

namespace Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTahunAjaranRequest;
use App\Http\Requests\UpdateTahunAjaranRequest;
use Illuminate\Routing\Controller;

class TahunAjaranController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('tahun_ajaran', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderByDesc('status')
            ->orderByDesc('tahun_ajaran')
            ->paginate(10)
            ->withQueryString();

        // Gunakan namespace 'akademik'::view
        return view('akademik::tahun-ajaran.index', compact('tahunAjaran'));
    }

    public function create()
    {
        return view('akademik::tahun-ajaran.create');
    }

    public function store(StoreTahunAjaranRequest $request)
{
    try {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        // Jika status aktif, nonaktifkan yang lain
        if ($data['status']) {
            TahunAjaran::where('status', true)->update(['status' => false]);
        }

        TahunAjaran::create($data);

        return redirect()
            ->route('akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
    }
}

    public function show(TahunAjaran $tahunAjaran)
    {
        return view('akademik::tahun-ajaran.show', compact('tahunAjaran'));
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('akademik::tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(UpdateTahunAjaranRequest $request, TahunAjaran $tahunAjaran)
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        if ($data['status']) {
            TahunAjaran::where('status', true)
                ->where('id', '!=', $tahunAjaran->id)
                ->update(['status' => false]);
        }

        $tahunAjaran->update($data);

        return redirect()
            ->route('akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->status) {
            return redirect()
                ->route('akademik.tahun-ajaran.index')
                ->with('error', 'Tahun ajaran aktif tidak dapat dihapus.');
        }

        $tahunAjaran->delete();

        return redirect()
            ->route('akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
