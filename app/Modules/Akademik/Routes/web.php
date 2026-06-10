<?php

// use App\Http\Controllers\TahunAjaranController as ControllersTahunAjaranController;

// use App\Http\Controllers\KelasController;


use Illuminate\Support\Facades\Route;
use Modules\Akademik\Controllers\AkademikController;
use Modules\Akademik\Controllers\JenisKegiatanController as ControllersJenisKegiatanController;
use Modules\Akademik\Controllers\KategoriPelajaranController as ControllersKategoriPelajaranController;
use Modules\Akademik\Controllers\KelasController as ControllersKelasController;
use Modules\Akademik\Controllers\MataPelajaranController;
use Modules\Akademik\Controllers\TahunAjaranController;
use Modules\Akademik\Controllers\JadwalPelajaranController;
use Modules\Akademik\Controllers\KalenderAkademikController;
use Modules\Akademik\Controllers\KurikulumController;
use Modules\Akademik\Controllers\MasterKurikulumController;
use Modules\Akademik\Controllers\BebanMengajarController;
use Modules\Akademik\Controllers\RombelController;
use Modules\Akademik\Controllers\KenaikanKelasController;
use Modules\Akademik\Controllers\MasterJamPelajaranController;
/*
|--------------------------------------------------------------------------
| Akademik Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/akademik' and named 'akademik.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['web', 'auth', \Modules\Akademik\Middleware\ReadOnlyRoleMiddleware::class])->group(function () {
    Route::get('/', [AkademikController::class, 'index'])->name('index');
    // Route::get('/create', [AkademikController::class, 'create'])->name('create');
    // Route::post('/', [AkademikController::class, 'store'])->name('store');
    // Route::get('/{id}', [AkademikController::class, 'show'])->name('show');
    // Route::get('/{id}/edit', [AkademikController::class, 'edit'])->name('edit');
    // Route::put('/{id}', [AkademikController::class, 'update'])->name('update');
    // Route::delete('/{id}', [AkademikController::class, 'destroy'])->name('destroy');

    Route::resource('tahun-ajaran', TahunAjaranController::class);
    Route::resource('kelas', ControllersKelasController::class)->parameters(['kelas' => 'kelas']);
    Route::resource('jenis-kegiatan', ControllersJenisKegiatanController::class);
    Route::resource('kategori-pelajaran', ControllersKategoriPelajaranController::class);
    Route::post('mata-pelajaran/import', [MataPelajaranController::class, 'import'])->name('mata-pelajaran.import');
    Route::post('mata-pelajaran/bulk-store', [MataPelajaranController::class, 'bulkStore'])->name('mata-pelajaran.bulk-store');
    Route::resource('mata-pelajaran', MataPelajaranController::class);

    Route::post('jadwal-pelajaran/copy', [JadwalPelajaranController::class, 'copyJadwal'])->name('jadwal-pelajaran.copy');
    Route::post('jadwal-pelajaran/bulk-store', [JadwalPelajaranController::class, 'bulkStore'])->name('jadwal-pelajaran.bulk-store');
    Route::resource('jadwal-pelajaran', JadwalPelajaranController::class);
    Route::get('beban-mengajar', [BebanMengajarController::class, 'index'])->name('beban-mengajar.index');

    Route::resource('kalender-akademik', KalenderAkademikController::class);
    Route::get('kalender-akademik-events-data', [KalenderAkademikController::class, 'events'])->name('kalender-akademik.events');
    Route::resource('master-kurikulum', MasterKurikulumController::class);
    Route::post('kurikulum/bulk-store', [KurikulumController::class, 'bulkStore'])->name('kurikulum.bulk-store');
    Route::resource('kurikulum', KurikulumController::class);
    Route::get('rombel/history', [RombelController::class, 'history'])->name('rombel.history');
    Route::resource('rombel', RombelController::class);

    Route::get('kenaikan-kelas', [KenaikanKelasController::class, 'index'])->name('kenaikan-kelas.index');
    Route::get('kenaikan-kelas/get-rombel', [KenaikanKelasController::class, 'getRombel'])->name('kenaikan-kelas.get-rombel');
    Route::get('kenaikan-kelas/get-siswa', [KenaikanKelasController::class, 'getSiswa'])->name('kenaikan-kelas.get-siswa');
    Route::post('kenaikan-kelas/process', [KenaikanKelasController::class, 'process'])->name('kenaikan-kelas.process');

    Route::get('laporan', [\Modules\Akademik\Controllers\LaporanAkademikController::class, 'index'])->name('laporan.index');

    // Master Jam Pelajaran
    Route::post('master-jam-pelajaran/copy', [MasterJamPelajaranController::class, 'copyHari'])->name('master-jam-pelajaran.copy');
    Route::get('master-jam-pelajaran/{master_jam_pelajaran}/duplicate', [MasterJamPelajaranController::class, 'duplicate'])->name('master-jam-pelajaran.duplicate');
    Route::resource('master-jam-pelajaran', MasterJamPelajaranController::class);
});


// Public route for Calendar Sync (iCal) - Must be outside 'auth' so Google can fetch it
Route::get('/kalender-akademik-export/ical', [KalenderAkademikController::class, 'exportIcal'])->name('kalender-akademik.export-ical');
