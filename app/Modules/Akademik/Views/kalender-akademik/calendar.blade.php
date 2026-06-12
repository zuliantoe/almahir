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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-sync mr-2"></i>Sinkronisasi Google Calendar</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i> Metode ini memungkinkan Google Calendar menarik data otomatis dari aplikasi tanpa perlu login ulang.
                </div>
                
                <div class="text-center mb-4">
                    <a id="btnSyncGoogle" href="#" 
                       target="_blank" class="btn btn-danger btn-lg shadow-sm">
                        <i class="fab fa-google mr-2"></i> Hubungkan ke Google Calendar (Sekali Klik)
                    </a>
                    <p class="text-muted small mt-2">Tombol ini akan otomatis membuka Google Calendar Anda.</p>
                </div>

                <hr>

                <h6><strong>Atau Cara Manual:</strong></h6>
                <ol class="text-muted mb-4">
                    <li>Salin link di bawah ini.</li>
                    <li>Buka <a href="https://calendar.google.com" target="_blank">Google Calendar</a> Anda.</li>
                    <li>Di sisi kiri, cari bagian <strong>"Kalender lainnya"</strong> (Other calendars).</li>
                    <li>Klik ikon <strong>+</strong> lalu pilih <strong>"Dari URL"</strong> (From URL).</li>
                    <li>Tempelkan link yang sudah disalin tadi dan klik <strong>"Tambahkan kalender"</strong>.</li>
                </ol>

                <div class="form-group mb-0">
                    <label class="text-xs font-weight-bold text-uppercase">Link Kalender Akademik (iCal URL)</label>
                    <div class="input-group">
                        <input type="text" id="icalUrl" class="form-control font-weight-bold bg-light" readonly 
                               value="">
                        <div class="input-group-append">
                            <button class="btn btn-primary" onclick="copyIcalUrl()">
                                <i class="fas fa-copy mr-1"></i> Salin Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyIcalUrl() {
    var copyText = document.getElementById("icalUrl");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    
    alert("Link berhasil disalin! Silakan tempelkan di Google Calendar Anda.");
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
    const webcalUrl = 'webcal://' + host + relativePath;
    
    // Set value for manual input field
    const icalInput = document.getElementById('icalUrl');
    if (icalInput) {
        icalInput.value = absoluteUrl;
    }
    
    // Set href for one-click button
    const syncButton = document.getElementById('btnSyncGoogle');
    if (syncButton) {
        syncButton.href = 'https://www.google.com/calendar/render?cid=' + encodeURIComponent(webcalUrl);
    }

    var calendarEl = document.getElementById('calendar');
    var loaderEl = document.getElementById('calendarLoader');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        headerToolbar: {
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
