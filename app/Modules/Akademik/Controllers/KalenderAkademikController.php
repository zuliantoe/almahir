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
        if ($request->ajax() || $request->wantsJson() || $request->has('view_json')) {
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

        $activeYear = TahunAjaran::where('status', 1)->first() ?? TahunAjaran::orderBy('id', 'desc')->first();
        $tahunAjarans = $activeYear 
            ? TahunAjaran::where('id', '>=', $activeYear->id)->orderBy('id', 'asc')->get()
            : TahunAjaran::orderBy('id', 'asc')->get();
        
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

        $activeYear = TahunAjaran::where('status', 1)->first();

        $events = KalenderAkademik::query()
            ->with(['jenisKegiatan'])
            ->when($start && $end, function($query) use ($start, $end) {
                // Filter berdasarkan range tanggal yang dikirim oleh FullCalendar (overlap logic)
                return $query->where('tanggal_awal', '<=', $end)
                             ->where('tanggal_akhir', '>=', $start);
            })
            ->get();

        $formattedEvents = $events->map(function ($event) {
            $jenis = $event->jenisKegiatan;
            $isKbm = $jenis ? $jenis->is_kbm : true;
            $color = ($jenis && $jenis->warna) ? $jenis->warna : '#007bff';
            
            if (!$isKbm && (!$jenis || !$jenis->warna)) {
                $color = '#dc3545';
            }

            // Pastikan tanggal valid sebelum diformat
            $start = $event->tanggal_awal ? $event->tanggal_awal->format('Y-m-d') : null;
            
            // FullCalendar all-day events are exclusive, so we must add 1 day to the end date
            $endDateObj = ($event->tanggal_akhir ?? $event->tanggal_awal);
            $end = $endDateObj ? $endDateObj->copy()->addDay()->format('Y-m-d') : $start;

            if (!$start) return null; // Skip jika tidak ada tanggal

            return [
                'id'          => $event->id,
                'title'       => ($jenis ? '[' . $jenis->jeniskegiatan . '] ' : '') . $event->nama_kegiatan,
                'start'       => $start,
                'end'         => $end,
                'color'       => $color,
                'borderColor' => $color,
                'textColor'   => '#ffffff',
                'allDay'      => true,
                'extendedProps' => [
                    'jenis'     => $jenis ? $jenis->jeniskegiatan : '-',
                    'is_kbm'    => $isKbm,
                    'deskripsi' => $event->deskripsi,
                    'color'     => $color,
                ]
            ];
        })->filter()->values();


        return response()->json($formattedEvents);
    }

    public function create()
    {
        $activeYear = TahunAjaran::where('status', 1)->first() ?? TahunAjaran::orderBy('id', 'desc')->first();
        $tahunAjarans = $activeYear 
            ? TahunAjaran::where('id', '>=', $activeYear->id)->orderBy('id', 'asc')->get()
            : TahunAjaran::orderBy('id', 'asc')->get();
        
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
        $activeYear = TahunAjaran::where('status', 1)->first() ?? TahunAjaran::orderBy('id', 'desc')->first();
        // Untuk edit, tambahkan juga tahun ajaran yang sedang diedit jika dia tahun lalu (agar tidak pecah)
        if ($activeYear) {
            $tahunAjarans = TahunAjaran::where('id', '>=', $activeYear->id)
                ->orWhere('id', $kalenderAkademik->tahunajaran_id)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $tahunAjarans = TahunAjaran::orderBy('id', 'asc')->get();
        }
            
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

    public function exportIcal()
    {
        $events = KalenderAkademik::with('jenisKegiatan')->get();

        $ics = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Almahir//Academic Calendar//ID',
            'X-WR-CALNAME:Kalender Akademik Almahir',
            'X-WR-TIMEZONE:Asia/Jakarta',
            'X-WR-CALCOLOR:#1e3c72',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($events as $event) {
            if (!$event->tanggal_awal) {
                continue;
            }

            $endDateObj  = ($event->tanggal_akhir ?? $event->tanggal_awal);
            $dtStart     = $event->tanggal_awal->format('Ymd');
            $dtEnd       = $endDateObj->copy()->addDay()->format('Ymd');

            $jenis       = $event->jenisKegiatan;
            $isKbm       = $jenis ? $jenis->is_kbm : true;
            $hexColor    = ($jenis && $jenis->warna) ? $jenis->warna : '#007bff';
            if (!$isKbm && (!$jenis || !$jenis->warna)) {
                $hexColor = '#dc3545';
            }

            // Map hex → RFC 7986 named color (the only colors Google Calendar supports)
            $namedColor  = $this->hexToCalendarColor($hexColor);
            $jenisLabel  = $jenis ? '[' . $jenis->jeniskegiatan . '] ' : '';

            // Escape ICS text fields
            $summary     = $this->escapeIcsText($jenisLabel . $event->nama_kegiatan);
            $description = $this->escapeIcsText($event->deskripsi ?: 'Agenda Akademik Sekolah');

            $ics[] = 'BEGIN:VEVENT';
            $ics[] = 'UID:' . $event->id . '@almahir';
            $ics[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
            $ics[] = 'DTSTART;VALUE=DATE:' . $dtStart;
            $ics[] = 'DTEND;VALUE=DATE:' . $dtEnd;
            $ics[] = 'SUMMARY:' . $summary;
            $ics[] = 'DESCRIPTION:' . $description;
            $ics[] = 'LOCATION:Sekolah Almahir';
            $ics[] = 'COLOR:' . $namedColor;
            $ics[] = 'X-APPLE-CALENDAR-COLOR:' . $hexColor;
            $ics[] = 'STATUS:CONFIRMED';
            $ics[] = 'END:VEVENT';
        }

        $ics[] = 'END:VCALENDAR';

        return response(implode("\r\n", $ics))
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Content-Disposition', 'inline; filename="kalender_akademik_almahir.ics"');
    }

    /**
     * Escape special characters in iCalendar text fields.
     */
    private function escapeIcsText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';',  '\;',   $text);
        $text = str_replace(',',  '\,',   $text);
        $text = str_replace("\n", '\n',   $text);
        $text = str_replace("\r", '',     $text);
        return $text;
    }

    /**
     * Map a hex color to the nearest RFC 7986 named color.
     * These are the ONLY color values Google Calendar recognizes via the COLOR property.
     *
     * Google Calendar color palette (named): alice-blue, amethyst, avocado, banana,
     * basil, blueberry, cherry, citron, cocoa, flamingo, grape, graphite, lavender,
     * peacock, pistachio, pumpkin, radicchio, sage, tangerine, tomato, wisteria.
     */
    private function hexToCalendarColor(string $hex): string
    {
        // Normalize hex: remove # and convert to lowercase
        $hex = ltrim(strtolower($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // RFC 7986 / Google Calendar named colors with approximate RGB values
        $palette = [
            'tomato'     => [205,  92,  92],  // red
            'flamingo'   => [250, 128, 114],  // salmon/light-red
            'tangerine'  => [242, 133,   0],  // orange-yellow
            'banana'     => [243, 179,  45],  // yellow
            'sage'       => [147, 196, 125],  // light green
            'basil'      => [ 15, 157,  88],  // dark green
            'peacock'    => [ 39, 174, 239],  // light blue
            'blueberry'  => [ 63,  81, 181],  // indigo/dark blue
            'lavender'   => [121, 134, 203],  // medium blue-purple
            'grape'      => [171,  71, 188],  // purple
            'graphite'   => [ 97,  97,  97],  // gray
            'amethyst'   => [171,  71, 188],  // purple variant
            'cherry'     => [211,  72,  54],  // dark red
            'pumpkin'    => [230, 124,  16],  // orange
            'cocoa'      => [143,  86,  56],  // brown
        ];

        $nearest = 'blueberry';
        $minDist = PHP_INT_MAX;
        foreach ($palette as $name => $rgb) {
            $dist = pow($r - $rgb[0], 2) + pow($g - $rgb[1], 2) + pow($b - $rgb[2], 2);
            if ($dist < $minDist) {
                $minDist = $dist;
                $nearest = $name;
            }
        }

        return $nearest;
    }
}
