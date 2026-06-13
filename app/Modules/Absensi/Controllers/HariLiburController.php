<?php

namespace Modules\Absensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Absensi\Models\HariLibur;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HariLiburController extends Controller
{
    /**
     * Display a listing of holidays.
     */
    public function index(Request $request): View
    {
        $query = HariLibur::orderBy('tanggal', 'desc');

        if ($request->search) {
            $query->where('keterangan', 'like', "%{$request->search}%");
        }

        $liburs = $query->paginate(15)->withQueryString();

        return view('absensi::hari-libur.index', [
            'title' => 'Setting Hari Libur',
            'liburs' => $liburs
        ]);
    }

    /**
     * Store a newly created holiday in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_liburs,tanggal',
            'keterangan' => 'required|string|max:255',
        ]);

        HariLibur::create($request->all());

        return redirect()->back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Remove the specified holiday from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $libur = HariLibur::findOrFail($id);
        $libur->delete();

        return redirect()->back()->with('success', 'Hari libur berhasil dihapus.');
    }
}
