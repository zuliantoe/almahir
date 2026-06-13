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
            if (!$event->tanggal_awal) continue;

            $endDateObj  = ($event->tanggal_akhir ?? $event->tanggal_awal);
            $dtStart     = $event->tanggal_awal->format('Ymd');
            $dtEnd       = $endDateObj->copy()->addDay()->format('Ymd');
            $jenis       = $event->jenisKegiatan;
            $jenisLabel  = $jenis ? '[' . $jenis->jeniskegiatan . '] ' : '';
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
     * Export iCal feed per JenisKegiatan.
     *
     * Google Calendar CANNOT display per-event colors from a single subscription feed —
     * it applies ONE color to the entire calendar. The solution is to provide separate
     * feeds per category (JenisKegiatan), so each Google Calendar subscription has its
     * own color that the user can set to match the academic calendar.
     */
    public function exportIcalByJenis(int $kegiatan_id)
    {
        $jenis = JenisKegiatan::findOrFail($kegiatan_id);

        $hexColor  = $jenis->warna ?: ($jenis->is_kbm ? '#007bff' : '#dc3545');
        $calName   = 'Kalender Akademik Almahir — ' . $jenis->jeniskegiatan;

        $events = KalenderAkademik::with('jenisKegiatan')
            ->where('kegiatan_id', $kegiatan_id)
            ->get();

        $ics = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Almahir//Academic Calendar//ID',
            'X-WR-CALNAME:' . $this->escapeIcsText($calName),
            'X-WR-TIMEZONE:Asia/Jakarta',
            'X-WR-CALCOLOR:' . $hexColor,
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($events as $event) {
            if (!$event->tanggal_awal) continue;

            $endDateObj  = ($event->tanggal_akhir ?? $event->tanggal_awal);
            $dtStart     = $event->tanggal_awal->format('Ymd');
            $dtEnd       = $endDateObj->copy()->addDay()->format('Ymd');
            $summary     = $this->escapeIcsText($event->nama_kegiatan);
            $description = $this->escapeIcsText($event->deskripsi ?: 'Agenda Akademik Sekolah');

            $ics[] = 'BEGIN:VEVENT';
            $ics[] = 'UID:jenis-' . $kegiatan_id . '-' . $event->id . '@almahir';
            $ics[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
            $ics[] = 'DTSTART;VALUE=DATE:' . $dtStart;
            $ics[] = 'DTEND;VALUE=DATE:' . $dtEnd;
            $ics[] = 'SUMMARY:' . $summary;
            $ics[] = 'DESCRIPTION:' . $description;
            $ics[] = 'LOCATION:Sekolah Almahir';
            $ics[] = 'STATUS:CONFIRMED';
            $ics[] = 'END:VEVENT';
        }

        $ics[] = 'END:VCALENDAR';

        $filename = 'kalender-' . \Illuminate\Support\Str::slug($jenis->jeniskegiatan) . '.ics';

        return response(implode("\r\n", $ics))
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
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
     */
    private function hexToCalendarColor(string $hex): string
    {
        $hex = ltrim(strtolower($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $palette = [
            'tomato'    => [205,  92,  92],
            'flamingo'  => [250, 128, 114],
            'tangerine' => [242, 133,   0],
            'banana'    => [243, 179,  45],
            'sage'      => [147, 196, 125],
            'basil'     => [ 15, 157,  88],
            'peacock'   => [ 39, 174, 239],
            'blueberry' => [ 63,  81, 181],
            'lavender'  => [121, 134, 203],
            'grape'     => [171,  71, 188],
            'graphite'  => [ 97,  97,  97],
            'pumpkin'   => [230, 124,  16],
        ];

        $nearest = 'blueberry';
        $minDist = PHP_INT_MAX;
        foreach ($palette as $name => $rgb) {
            $dist = ($r-$rgb[0])**2 + ($g-$rgb[1])**2 + ($b-$rgb[2])**2;
            if ($dist < $minDist) { $minDist = $dist; $nearest = $name; }
        }
        return $nearest;
    }
}
