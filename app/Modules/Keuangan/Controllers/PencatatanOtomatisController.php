<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Keuangan\Models\PencatatanOtomatis;
use Modules\Keuangan\Models\Pemasukan;
use Modules\Keuangan\Models\Pengeluaran;
use Modules\Keuangan\Models\Sumber;
use Modules\Keuangan\Models\Tujuan;
use Carbon\Carbon;

class PencatatanOtomatisController extends Controller
{
    public function index()
    {
        $pencatatans = PencatatanOtomatis::with(['sumber', 'tujuan'])->orderBy('created_at', 'desc')->get();
        return view('keuangan::pencatatanotomatis.index', compact('pencatatans'));
    }

    public function create()
    {
        $sumbers = Sumber::all();
        $tujuans = Tujuan::all();
        return view('keuangan::pencatatanotomatis.create', compact('sumbers', 'tujuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'sumber_id' => 'required_if:tipe,pemasukan',
            'tujuan_id' => 'required_if:tipe,pengeluaran',
            'jumlah' => 'required|numeric|min:0',
            'frekuensi' => 'required|in:sekali,harian,bulanan',
            'tanggal_mulai' => 'required|date',
            'waktu_eksekusi' => 'required',
            'deskripsi' => 'nullable|string'
        ]);

        PencatatanOtomatis::create($request->all());

        return redirect()->route('keuangan.pencatatanotomatis.index')
            ->with('success', 'Pencatatan otomatis berhasil ditambahkan.');
    }

    public function edit(PencatatanOtomatis $pencatatanotomati)
    {
        $pencatatan = $pencatatanotomati; // Route model binding will use snake case of the class name
        $sumbers = Sumber::all();
        $tujuans = Tujuan::all();
        return view('keuangan::pencatatanotomatis.edit', compact('pencatatan', 'sumbers', 'tujuans'));
    }

    public function update(Request $request, PencatatanOtomatis $pencatatanotomati)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'sumber_id' => 'required_if:tipe,pemasukan',
            'tujuan_id' => 'required_if:tipe,pengeluaran',
            'jumlah' => 'required|numeric|min:0',
            'frekuensi' => 'required|in:sekali,harian,bulanan',
            'tanggal_mulai' => 'required|date',
            'waktu_eksekusi' => 'required',
            'deskripsi' => 'nullable|string'
        ]);

        $pencatatanotomati->update($request->all());

        return redirect()->route('keuangan.pencatatanotomatis.index')
            ->with('success', 'Pencatatan otomatis berhasil diperbarui.');
    }

    public function destroy(PencatatanOtomatis $pencatatanotomati)
    {
        $pencatatanotomati->delete();
        return redirect()->route('keuangan.pencatatanotomatis.index')
            ->with('success', 'Pencatatan otomatis berhasil dihapus.');
    }

    /**
     * This method is called by the scheduler to process recurring transactions.
     */
    public function processRecurring()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // Get all active scheduled tasks that should start on or before today
        $tasks = PencatatanOtomatis::where('is_active', true)
            ->where('tanggal_mulai', '<=', $today)
            ->get();

        foreach ($tasks as $task) {
            $shouldRun = false;

            // Check if it's time to run
            if ($currentTime >= $task->waktu_eksekusi) {
                if ($task->frekuensi == 'sekali') {
                    if (!$task->last_run_at) {
                        $shouldRun = true;
                    }
                } elseif ($task->frekuensi == 'harian') {
                    if (!$task->last_run_at || $task->last_run_at->toDateString() < $today) {
                        $shouldRun = true;
                    }
                } elseif ($task->frekuensi == 'bulanan') {
                    // Jika belum pernah jalan atau bulan ini belum jalan
                    if (!$task->last_run_at || 
                        $task->last_run_at->format('Y-m') < $now->format('Y-m') || 
                        ($task->last_run_at->format('Y-m') == $now->format('Y-m') && $task->last_run_at->toDateString() < $today)) {
                        
                        // Handle jumlah hari dalam sebulan yang berbeda (misal: Feb = 28/29 hari, sedangkan target tanggal 30)
                        $startDay = (int) Carbon::parse($task->tanggal_mulai)->format('j');
                        $daysInMonth = (int) $now->daysInMonth;
                        $effectiveDay = min($startDay, $daysInMonth);
                        
                        if ((int) $now->format('j') >= $effectiveDay) {
                            $shouldRun = true;
                        }
                    }
                }
            }

            if ($shouldRun) {
                // Insert into corresponding table
                if ($task->tipe == 'pemasukan') {
                    Pemasukan::create([
                        'sumber_id' => $task->sumber_id,
                        'jumlah' => $task->jumlah,
                        'tanggal' => $today,
                        'waktu' => $currentTime,
                        'deskripsi' => $task->deskripsi,
                        'is_otomatis' => true
                    ]);
                } elseif ($task->tipe == 'pengeluaran') {
                    Pengeluaran::create([
                        'tujuan_id' => $task->tujuan_id,
                        'jumlah' => $task->jumlah,
                        'tanggal' => $today,
                        'waktu' => $currentTime,
                        'deskripsi' => $task->deskripsi,
                        'is_otomatis' => true
                    ]);
                }

                // Update task
                $task->last_run_at = $now;
                if ($task->frekuensi == 'sekali') {
                    $task->is_active = false; // Disable if run once
                }
                $task->save();
            }
        }
    }
}
