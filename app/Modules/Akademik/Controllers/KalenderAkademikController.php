<?php

namespace Modules\Akademik\Controllers;

use App\Http\Requests\AkademikRequest\StoreKalenderAkademikRequest;
use App\Http\Requests\AkademikRequest\UpdateKalenderAkademikRequest;
use App\Modules\Akademik\Models\KalenderAkademik;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\JenisKegiatan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KalenderAkademikController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->events($request);
        }

        if ($request->has('view') && $request->view == 'calendar') {
            $jenisKegiatanList = JenisKegiatan::orderBy('jeniskegiatan')->get();
            return view('akademik::kalender-akademik.calendar', compact('jenisKegiatanList'));
        }

        $kalenderAkademik = KalenderAkademik::query()
            ->with(['tahunAjaran', 'jenisKegiatan'])
            ->when($request->filled('tahunajaran_id'), function ($query) use ($request) {
                $query->where('tahunajaran_id', $request->tahunajaran_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nama_kegiatan', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('tanggal_awal')
            ->paginate(10)
            ->withQueryString();

        $tahunAjarans = TahunAjaran::all();
        return view('akademik::kalender-akademik.index', compact('kalenderAkademik', 'tahunAjarans'));
    }

    public function events(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $colorPalette = [
            '#007bff', // primary (blue)
            '#17a2b8', // info (teal)
            '#28a745', // success (green)
            '#ffc107', // warning (yellow)
            '#dc3545', // danger (red)
            '#6610f2', // indigo
            '#fd7e14', // orange
            '#e83e8c', // pink
            '#20c997', // teal
            '#6c757d', // secondary (gray)
        ];

        $events = KalenderAkademik::query()
            ->with(['jenisKegiatan'])
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('tanggal_awal', [$start, $end])
                      ->orWhereBetween('tanggal_akhir', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('tanggal_awal', '<=', $start)
                            ->where('tanggal_akhir', '>=', $end);
                      });
            })
            ->get();

        $formattedEvents = $events->map(function ($event) use ($colorPalette) {
            $isKbm = optional($event->jenisKegiatan)->is_kbm ?? true;
            
            if (!$isKbm) {
                $color = '#dc3545'; // Danger/Red for Non-KBM
            } else {
                $colorIndex = ($event->kegiatan_id - 1) % count($colorPalette);
                $color = $colorPalette[$colorIndex];
            }

            return [
                'id'          => $event->id,
                'title'       => $event->nama_kegiatan,
                'start'       => $event->tanggal_awal,
                'end'         => date('Y-m-d', strtotime($event->tanggal_akhir . ' +1 day')),
                'color'       => $color,
                'borderColor' => $color,
                'textColor'   => '#ffffff',
                'extendedProps' => [
                    'jenis'     => $event->jenisKegiatan ? $event->jenisKegiatan->jeniskegiatan : '-',
                    'is_kbm'    => $isKbm,
                    'deskripsi' => $event->deskripsi,
                    'color'     => $color,
                ]
            ];
        });


        return response()->json($formattedEvents);
    }

    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        $jenisKegiatans = JenisKegiatan::all();
        return view('akademik::kalender-akademik.create', compact('tahunAjarans', 'jenisKegiatans'));
    }

    public function store(StoreKalenderAkademikRequest $request)
    {
        try {
            KalenderAkademik::create($request->validated());

            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('success', 'Kegiatan kalender akademik berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function show(KalenderAkademik $kalenderAkademik)
    {
        $kalenderAkademik->load(['tahunAjaran', 'jenisKegiatan']);
        return view('akademik::kalender-akademik.show', compact('kalenderAkademik'));
    }

    public function edit(KalenderAkademik $kalenderAkademik)
    {
        $tahunAjarans = TahunAjaran::all();
        $jenisKegiatans = JenisKegiatan::all();
        return view('akademik::kalender-akademik.edit', compact('kalenderAkademik', 'tahunAjarans', 'jenisKegiatans'));
    }

    public function update(UpdateKalenderAkademikRequest $request, KalenderAkademik $kalenderAkademik)
    {
        try {
            $kalenderAkademik->update($request->validated());

            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('success', 'Kegiatan kalender akademik berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(KalenderAkademik $kalenderAkademik)
    {
        try {
            $kalenderAkademik->delete();

            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('success', 'Kegiatan kalender akademik berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
