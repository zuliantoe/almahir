@extends('layouts.app')

@section('title', 'Kalender Akademik')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
<style>
    /* ── Base Variables ── */
    :root {
        --fc-border-color: #edf0f5;
        --fc-today-bg-color: rgba(0, 123, 255, 0.06);
        --fc-neutral-bg-color: #f8fafc;
        --fc-event-border-radius: 6px;
        --primary: #007bff;
        --primary-hover: #0056b3;
    }

    /* ── Page Header ── */
    .kalender-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 12px;
        padding: 24px 28px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.2);
    }
    .kalender-header::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .kalender-header h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 4px;
        position: relative; z-index: 1;
    }
    .kalender-header p {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
        margin-bottom: 0;
        position: relative; z-index: 1;
    }
    .kalender-header .header-actions {
        position: relative; z-index: 1;
    }
    .header-actions .btn-header {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-header-ghost {
        background: rgba(255,255,255,0.15);
        color: #fff;
        backdrop-filter: blur(4px);
    }
    .btn-header-ghost:hover {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }
    .btn-header-solid {
        background: #fff;
        color: #1e3c72;
    }
    .btn-header-solid:hover {
        background: #f8f9fa;
        color: #2a5298;
    }

    /* ── Legend Badges ── */
    .legend-bar {
        background: #fff;
        border-radius: 10px;
        padding: 12px 18px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }
    .legend-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        margin-right: 4px;
    }
    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #f8f9fa;
        color: #495057;
        border: 1px solid #dee2e6;
    }
    .legend-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* ── FullCalendar Overrides ── */
    .fc .fc-toolbar.fc-header-toolbar {
        margin-bottom: 20px;
    }
    .fc .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #343a40 !important;
    }
    .fc-header-toolbar .fc-toolbar-chunk {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .fc .fc-button-group {
        gap: 6px;
    }
    .fc .fc-button-group > .fc-button {
        border-radius: 6px !important;
        margin-left: 0 !important;
    }
    .fc .fc-button {
        border-radius: 6px !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        padding: 5px 12px !important;
        text-transform: capitalize !important;
    }
    .fc .fc-button-primary {
        background-color: #007bff !important;
        border-color: #007bff !important;
    }
    .fc .fc-button-primary:hover {
        background-color: #0069d9 !important;
        border-color: #0062cc !important;
    }
    .fc .fc-button-active {
        background-color: #0056b3 !important;
        border-color: #004085 !important;
    }
    .fc .fc-event {
        border-radius: 4px !important;
        padding: 1px 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        border: none !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
    }
    .fc .fc-daygrid-day-number {
        font-weight: 600;
        color: #495057;
        font-size: 0.85rem;
    }
    .fc .fc-col-header-cell-cushion {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        color: #6c757d;
    }
    .fc .fc-day-today {
        background-color: rgba(0, 123, 255, 0.05) !important;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
        background: #007bff;
        color: #fff;
        border-radius: 50%;
        width: 26px; height: 26px;
        display: flex; align-items: center; justify-content: center;
        margin: 2px;
    }

    /* ── Modal ── */
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }
    .event-info-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    .event-info-row:last-child { border-bottom: none; }
    .event-info-icon {
        width: 32px; height: 32px;
        border-radius: 6px;
        background: #f8f9fa;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        color: #6c757d;
    }
    .event-info-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #adb5bd;
        margin-bottom: 2px;
    }
    .event-info-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }

    /* ── Responsive Styling ── */
    @media (max-width: 767.98px) {
        .kalender-header {
            padding: 16px 20px;
            margin-bottom: 16px;
        }
        .kalender-header h1 {
            font-size: 1.35rem;
        }
        .kalender-header p {
            font-size: 0.8rem;
        }
        .kalender-header .header-actions {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 8px !important;
            margin-top: 15px !important;
        }
        .kalender-header .header-actions .btn-header {
            width: 100%;
            justify-content: center;
        }
        .legend-bar {
            padding: 10px 14px;
            gap: 8px !important;
        }
        .legend-title {
            width: 100%;
            margin-bottom: 4px;
            font-size: 0.7rem;
        }
        .legend-chip {
            font-size: 0.7rem;
            padding: 2px 8px;
        }
        /* FullCalendar Toolbar Responsiveness */
        .fc .fc-toolbar.fc-header-toolbar {
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        .fc .fc-toolbar-title {
            font-size: 1.1rem !important;
            text-align: center;
            margin: 4px 0;
        }
        .fc .fc-button {
            padding: 5px 8px !important;
            font-size: 0.8rem !important;
        }
        .fc .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 4px;
        }
    }
</style>

@endpush

@section('content')
<div class="container-fluid">

    {{-- ── Page Header (Project Style) ── --}}
    <div class="kalender-header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div>
                <h1><i class="fas fa-calendar-alt mr-2"></i>Kalender Akademik</h1>
                <p>Jadwal dan agenda kegiatan akademik sekolah tahun ajaran ini.</p>
            </div>
            <div class="header-actions d-flex flex-wrap gap-2 mt-3 mt-md-0" style="gap:8px;">
                <a href="{{ route('akademik.kalender-akademik.index') }}" class="btn-header btn-header-ghost">
                    <i class="fas fa-list"></i> Mode List
                </a>
                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                <a href="{{ route('akademik.kalender-akademik.create') }}" class="btn-header btn-header-solid">
                    <i class="fas fa-plus"></i> Tambah
                </a>
                @endif
                <button type="button" class="btn-header btn-header-ghost" data-toggle="modal" data-target="#syncModal">
                    <i class="fas fa-sync"></i> Sinkron
                </button>
            </div>
        </div>
    </div>

    {{-- ── Jenis Kegiatan Legend ── --}}
    @if(isset($jenisKegiatanList) && count($jenisKegiatanList))
    <div class="legend-bar shadow-sm">
        <span class="legend-title">Keterangan Warna:</span>
        @foreach($jenisKegiatanList as $jk)
        <span class="legend-chip">
            <span class="legend-dot" style="background: {{ $jk->warna ?: '#6c757d' }};"></span>
            {{ $jk->jeniskegiatan }}
        </span>
        @endforeach
    </div>
    @endif

    {{-- ── Calendar Card ── --}}
    <x-card type="primary" outline>
        <div class="position-relative">
            <div class="calendar-loader" id="calendarLoader" style="position: absolute; inset: 0; background: rgba(255,255,255,0.7); z-index: 10; display: none; align-items: center; justify-content: center;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div id='calendar'></div>
        </div>
    </x-card>

</div>

{{-- ── Event Detail Modal ── --}}
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header text-white border-0" id="modalHeader">
                <h5 class="modal-title font-weight-bold">Detail Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="event-info-row">
                    <div class="event-info-icon"><i class="fas fa-bookmark"></i></div>
                    <div>
                        <div class="event-info-label">Nama Kegiatan</div>
                        <p class="event-info-value" id="modalTitle">-</p>
                    </div>
                </div>
                <div class="event-info-row">
                    <div class="event-info-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="flex-fill">
                        <div class="event-info-label">Waktu Pelaksanaan</div>
                        <p class="event-info-value" id="modalPeriod">-</p>
                    </div>
                </div>
                <div class="event-info-row">
                    <div class="event-info-icon"><i class="fas fa-tag"></i></div>
                    <div>
                        <div class="event-info-label">Jenis</div>
                        <div><span id="modal-jenis-badge" class="badge badge-pill text-white px-3 py-2">-</span></div>
                    </div>
                </div>
                <div class="event-info-row">
                    <div class="event-info-icon"><i class="fas fa-info-circle"></i></div>
                    <div>
                        <div class="event-info-label">Keterangan</div>
                        <p class="event-info-value" id="modalDescription" style="font-weight: 400; color: #6c757d;">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <div class="mr-auto">
                    <a href="#" id="btnAddToGoogle" target="_blank" class="btn btn-outline-danger btn-sm" title="Tambahkan ke Google Calendar">
                        <i class="fab fa-google"></i> + Google Calendar
                    </a>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
                <a href="#" id="modalEditBtn" class="btn btn-warning btn-sm">Edit</a>
                <a href="#" id="modalDetailBtn" class="btn btn-info btn-sm">Detail</a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Sync to Google Modal ── --}}
<div class="modal fade" id="syncModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 text-white" style="background:linear-gradient(135deg,#1e3c72,#2a5298);">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-calendar-plus mr-2"></i>Sinkronisasi ke Google Calendar
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">

                {{-- Penjelasan singkat --}}
                <div class="alert alert-info py-2 small mb-3">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Cara Kerja:</strong> Google Calendar hanya bisa menampilkan <strong>satu warna per kalender</strong>.
                    Agar warna sesuai dengan tampilan di sini, subscribe ke <strong>masing-masing jenis kegiatan</strong> di bawah — setiap link sudah otomatis berwarna sesuai.
                </div>

                {{-- Per-Jenis Kegiatan subscribe cards --}}
                <h6 class="font-weight-bold mb-2"><i class="fas fa-palette mr-1 text-primary"></i> Subscribe Per Jenis Kegiatan (Warna Sesuai)</h6>

                <div class="row mb-3">
                    @foreach($jenisKegiatanList as $jk)
                    @php
                        $warna    = $jk->warna ?: ($jk->is_kbm ? '#007bff' : '#dc3545');
                        // Bangun URL langsung agar tidak bergantung pada route cache di production
                        $jenisPath = '/akademik/kalender-akademik-export/jenis/' . $jk->id . '/ical.ics';
                        $jenisUrl  = request()->getSchemeAndHttpHost() . $jenisPath;
                        $gcalUrl   = 'https://calendar.google.com/calendar/r?cid=' . urlencode('webcal://' . request()->getHost() . $jenisPath);
                    @endphp
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center border rounded px-3 py-2" style="gap:10px; background:#f8f9fa;">
                            <span style="width:18px;height:18px;border-radius:4px;background:{{ $warna }};flex-shrink:0;display:inline-block;"></span>
                            <span class="font-weight-bold small flex-fill">{{ $jk->jeniskegiatan }}</span>
                            <a href="{{ $gcalUrl }}"
                               target="_blank" class="btn btn-sm btn-outline-danger mr-1" title="Hubungkan ke Google Calendar">
                                <i class="fab fa-google mr-1"></i> Hubungkan
                            </a>
                            <button class="btn btn-sm btn-outline-secondary btn-copy-jenis"
                                    data-url="{{ $jenisUrl }}"
                                    onclick="copyJenisUrl(this)" title="Salin link iCal">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr>

                {{-- Semua event (satu feed, tanpa warna berbeda) --}}
                <h6 class="font-weight-bold mb-2"><i class="fas fa-calendar mr-1 text-secondary"></i> Atau: Semua Kegiatan (1 Link, Warna Seragam)</h6>
                <div class="d-flex align-items-center border rounded px-3 py-2 mb-3" style="background:#f8f9fa;gap:8px;">
                    <input type="text" id="icalUrl" class="form-control form-control-sm font-monospace bg-white" readonly value="">
                    <a id="btnSyncGoogle" href="#" target="_blank" class="btn btn-sm btn-danger flex-shrink-0">
                        <i class="fab fa-google mr-1"></i> Hubungkan
                    </a>
                    <button class="btn btn-sm btn-outline-secondary flex-shrink-0" id="btnCopyIcal" onclick="copyIcalUrl()" title="Salin link">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                {{-- Cara manual --}}
                <div class="collapse" id="caraManual">
                    <h6 class="font-weight-bold"><i class="fas fa-hand-point-right text-primary mr-1"></i> Cara Tambahkan URL ke Google Calendar:</h6>
                    <ol class="text-muted small mb-0">
                        <li>Klik tombol <strong>Salin</strong> di salah satu link di atas.</li>
                        <li>Buka <a href="https://calendar.google.com" target="_blank">calendar.google.com</a> di browser desktop.</li>
                        <li>Klik ikon <strong>"+"</strong> di sebelah <strong>"Kalender lainnya"</strong>.</li>
                        <li>Pilih <strong>"Dari URL"</strong> → tempel link → klik <strong>"Tambahkan kalender"</strong>.</li>
                        <li>Setelah ditambahkan, klik titik tiga di nama kalender → <strong>Edit</strong> → pilih warna yang sesuai.</li>
                    </ol>
                </div>
                <button class="btn btn-link btn-sm p-0 text-muted" type="button" data-toggle="collapse" data-target="#caraManual">
                    <i class="fas fa-question-circle mr-1"></i> Cara manual / tidak bisa sekali klik?
                </button>

            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyIcalUrl() {
    var copyText = document.getElementById("icalUrl");
    var btn = document.getElementById("btnCopyIcal");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(function() {
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        setTimeout(function() {
            btn.innerHTML = original;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2500);
    }).catch(function() {
        document.execCommand('copy');
        alert("Link berhasil disalin!");
    });
}

function copyJenisUrl(btn) {
    var url = btn.getAttribute('data-url');
    navigator.clipboard.writeText(url).then(function() {
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        setTimeout(function() {
            btn.innerHTML = original;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2500);
    }).catch(function() {
        alert("Salin link ini: " + url);
    });
}
</script>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Set up Google Calendar Sync URLs based on current browser URL
    const relativePath = '{{ route("akademik.kalender-akademik.export-ical", [], false) }}';
    const protocol = window.location.protocol;
    const host = window.location.host;
    
    // Construct URLs dynamically
    const absoluteUrl = protocol + '//' + host + relativePath;
    
    // Set value for manual input field
    const icalInput = document.getElementById('icalUrl');
    if (icalInput) {
        icalInput.value = absoluteUrl;
    }
    
    // Set href for one-click button — CORRECT format:
    // https://calendar.google.com/calendar/r?cid=webcal://host/path
    // (using /calendar/r not the deprecated /calendar/render)
    const syncButton = document.getElementById('btnSyncGoogle');
    if (syncButton) {
        const webcalUrl = 'webcal://' + host + relativePath;
        syncButton.href = 'https://calendar.google.com/calendar/r?cid=' + encodeURIComponent(webcalUrl);
    }

    var calendarEl = document.getElementById('calendar');
    var loaderEl = document.getElementById('calendarLoader');

    var isMobile = window.innerWidth < 768;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobile ? 'listMonth' : 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        headerToolbar: isMobile ? {
            left: 'prev,next',
            center: 'title',
            right: 'today listMonth'
        } : {
            left: 'prev,next today',
            center: 'title',
            right: 'multiMonthYear,dayGridMonth,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            year: 'Tahun',
            month: 'Bulan',
            list: 'Daftar'
        },
        events: '{{ route("akademik.kalender-akademik.events") }}',
        loading: function (isLoading) {
            loaderEl.style.display = isLoading ? 'flex' : 'none';
        },
        eventClick: function (info) {
            var ev = info.event;
            var props = ev.extendedProps;
            var color = props.color || '#007bff';

            document.getElementById('modalHeader').style.backgroundColor = color;
            document.getElementById('modalTitle').textContent = ev.title;

            var startStr = ev.start.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            var endDate = ev.end ? new Date(ev.end) : new Date(ev.start);
            if (ev.end) endDate.setDate(endDate.getDate() - 1);
            var endStr = endDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

            document.getElementById('modalPeriod').textContent = (startStr === endStr) ? startStr : startStr + ' – ' + endStr;

            var jenisBadge = document.getElementById('modal-jenis-badge');
            jenisBadge.textContent = props.jenis || '-';
            jenisBadge.style.backgroundColor = color;

            document.getElementById('modalDescription').textContent = props.deskripsi || 'Tidak ada keterangan.';

            @if(Auth::check() && !Auth::user()->hasRole('GURU') && !Auth::user()->hasRole('SISWA'))
            var baseUrl = '{{ url("/akademik/kalender-akademik") }}';
            document.getElementById('modalEditBtn').href = baseUrl + '/' + ev.id + '/edit';
            document.getElementById('modalDetailBtn').href = baseUrl + '/' + ev.id;
            @endif

            // Generate Google Calendar Link
            var startDate = new Date(ev.start);
            var endDate = ev.end ? new Date(ev.end) : new Date(ev.start);
            
            // Format to YYYYMMDDTHHMMSSZ
            function formatGoogleDate(date) {
                return date.toISOString().replace(/-|:|\.\d+/g, '');
            }

            var googleUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
                '&text=' + encodeURIComponent(ev.title) +
                '&dates=' + formatGoogleDate(startDate) + '/' + formatGoogleDate(endDate) +
                '&details=' + encodeURIComponent(props.deskripsi || 'Agenda Akademik Almahir') +
                '&location=' + encodeURIComponent('Sekolah Almahir');

            document.getElementById('btnAddToGoogle').href = googleUrl;

            $('#eventModal').modal('show');
        },
        eventDidMount: function (info) {
            $(info.el).tooltip({
                title: info.event.title,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        }
    });

    calendar.render();
});
</script>
@endpush
