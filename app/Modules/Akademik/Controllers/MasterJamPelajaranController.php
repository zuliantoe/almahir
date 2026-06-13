<?php

namespace Modules\Akademik\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\MasterJamPelajaran;
use App\Http\Requests\AkademikRequest\StoreMasterJamPelajaranRequest;
use App\Http\Requests\AkademikRequest\UpdateMasterJamPelajaranRequest;

class MasterJamPelajaranController extends Controller
{
    public function index()
    {
        $masterJamPelajarans = MasterJamPelajaran::query()
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jamke')
            ->get();
        return view('akademik::master-jam-pelajaran.index', compact('masterJamPelajarans'));
    }

    public function create()
    {
        return view('akademik::master-jam-pelajaran.create');
    }

    public function store(StoreMasterJamPelajaranRequest $request)
    {
        MasterJamPelajaran::create($request->validated());
        return redirect()->route('akademik.master-jam-pelajaran.index')->with('success', 'Master jam pelajaran berhasil ditambahkan.');
    }

    public function edit(MasterJamPelajaran $masterJamPelajaran)
    {
        return view('akademik::master-jam-pelajaran.edit', compact('masterJamPelajaran'));
    }

    public function update(UpdateMasterJamPelajaranRequest $request, MasterJamPelajaran $masterJamPelajaran)
    {
        $masterJamPelajaran->update($request->validated());
        return redirect()->route('akademik.master-jam-pelajaran.index')->with('success', 'Master jam pelajaran berhasil diperbarui.');
    }

    public function duplicate($id)
    {
        $masterJamPelajaran = MasterJamPelajaran::findOrFail($id);
        $isDuplicate = true;
        return view('akademik::master-jam-pelajaran.create', compact('masterJamPelajaran', 'isDuplicate'));
    }

    public function copyHari(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'dari_hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'ke_hari' => 'required|array',
            'ke_hari.*' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
        ]);

        $dariHari = $request->input('dari_hari');
        $keHariList = $request->input('ke_hari');

        $sourceJams = MasterJamPelajaran::where('hari', $dariHari)->get();

        if ($sourceJams->isEmpty()) {
            return redirect()->back()->with('error', "Tidak ada master jam pelajaran pada hari {$dariHari} untuk disalin.");
        }

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($keHariList as $targetHari) {
            if ($targetHari === $dariHari) continue;

            foreach ($sourceJams as $jam) {
                // Check if target day already has this jamke or overlaps
                $exists = MasterJamPelajaran::where('hari', $targetHari)
                    ->where('jamke', $jam->jamke)
                    ->exists();

                $overlap = MasterJamPelajaran::where('hari', $targetHari)
                    ->where(function ($query) use ($jam) {
                        $query->where('jamawal', '<', $jam->jamakhir)
                              ->where('jamakhir', '>', $jam->jamawal);
                    })
                    ->exists();

                if (!$exists && !$overlap) {
                    MasterJamPelajaran::create([
                        'hari' => $targetHari,
                        'jamke' => $jam->jamke,
                        'jamawal' => $jam->jamawal,
                        'jamakhir' => $jam->jamakhir,
                        'is_istirahat' => $jam->is_istirahat,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        $msg = "Berhasil menyalin {$createdCount} jam pelajaran.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} jam pelajaran dilewati karena bentrok/sudah ada).";
        }

        return redirect()->route('akademik.master-jam-pelajaran.index')->with('success', $msg);
    }

    public function destroy(MasterJamPelajaran $masterJamPelajaran)
    {
        $masterJamPelajaran->delete();
        return redirect()->route('akademik.master-jam-pelajaran.index')->with('success', 'Master jam pelajaran berhasil dihapus.');
    }
}

