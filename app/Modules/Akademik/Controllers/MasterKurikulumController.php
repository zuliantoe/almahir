<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\MasterKurikulum;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasterKurikulumController extends Controller
{
    public function index(): View
    {
        $masterKurikulums = MasterKurikulum::orderBy('nama_kurikulum')->get();
        return view('akademik::master-kurikulum.index', [
            'title' => 'Master Kurikulum',
            'masterKurikulums' => $masterKurikulums,
        ]);
    }

    public function create(): View
    {
        return view('akademik::master-kurikulum.create', [
            'title' => 'Tambah Master Kurikulum',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kurikulum' => 'required|string|max:255|unique:master_kurikulum,nama_kurikulum',
            'status' => 'required|boolean',
            'beban_mengajar_maksimal' => 'required|integer|min:1',
        ]);

        MasterKurikulum::create($validated);

        return redirect()->route('akademik.master-kurikulum.index')
            ->with('success', 'Master Kurikulum berhasil ditambahkan.');
    }

    public function edit(MasterKurikulum $masterKurikulum): View
    {
        return view('akademik::master-kurikulum.edit', [
            'title' => 'Edit Master Kurikulum',
            'masterKurikulum' => $masterKurikulum,
        ]);
    }

    public function update(Request $request, MasterKurikulum $masterKurikulum): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kurikulum' => 'required|string|max:255|unique:master_kurikulum,nama_kurikulum,' . $masterKurikulum->id,
            'status' => 'required|boolean',
            'beban_mengajar_maksimal' => 'required|integer|min:1',
        ]);

        $masterKurikulum->update($validated);

        return redirect()->route('akademik.master-kurikulum.index')
            ->with('success', 'Master Kurikulum berhasil diperbarui.');
    }

    public function destroy(MasterKurikulum $masterKurikulum): RedirectResponse
    {
        if ($masterKurikulum->detailKurikulum()->exists()) {
            return redirect()->route('akademik.master-kurikulum.index')
                ->with('error', 'Master Kurikulum tidak dapat dihapus karena masih digunakan.');
        }

        $masterKurikulum->delete();

        return redirect()->route('akademik.master-kurikulum.index')
            ->with('success', 'Master Kurikulum berhasil dihapus.');
    }
}
