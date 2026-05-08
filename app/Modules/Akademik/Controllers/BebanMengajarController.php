<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\TahunAjaran;
use Modules\Guru\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class BebanMengajarController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAjaranAktif = TahunAjaran::aktif()->first();
        $tahunAjaranId = $request->get('tahunajaran_id', $tahunAjaranAktif?->id);

        $gurus = Guru::query()
            ->withCount(['jadwalPelajaran as total_jam' => function ($query) use ($tahunAjaranId) {
                $query->whereHas('rombel', function ($q) use ($tahunAjaranId) {
                    $q->where('tahunajaran_id', $tahunAjaranId);
                });
            }])
            ->orderByDesc('total_jam')
            ->paginate(20)
            ->withQueryString();

        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        return view('akademik::beban-mengajar.index', [
            'title' => 'Beban Mengajar Guru',
            'gurus' => $gurus,
            'tahunAjarans' => $tahunAjarans,
            'tahunAjaranAktif' => $tahunAjaranAktif,
        ]);
    }
}
