<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * KeuanganController
 * 
 * CRUD operations for Keuangan module.
 */
class KeuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $monthName = now()->locale('id')->translatedFormat('F');

        $totalPemasukan = \Modules\Keuangan\Models\Pemasukan::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->sum('jumlah');
            
        $totalPengeluaran = \Modules\Keuangan\Models\Pengeluaran::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->sum('jumlah');
            
        $saldoKeseluruhanPemasukan = \Modules\Keuangan\Models\Pemasukan::sum('jumlah');
        $saldoKeseluruhanPengeluaran = \Modules\Keuangan\Models\Pengeluaran::sum('jumlah');
        $saldo = $saldoKeseluruhanPemasukan - $saldoKeseluruhanPengeluaran;
        
        $countTransaksi = \Modules\Keuangan\Models\Pemasukan::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->count() + 
            \Modules\Keuangan\Models\Pengeluaran::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->count();
            
        $uangSakuMasuk = \Modules\Keuangan\Models\UangSaku::where('status', '!=', 'Sudah Diterima Santri')->sum('jumlah');
        $uangSakuKeluar = \Modules\Keuangan\Models\UangSaku::where('status', 'Sudah Diterima Santri')->sum('jumlah');
        $saldoUangSaku = $uangSakuMasuk - $uangSakuKeluar;
        
        return view('keuangan::index', [
            'title' => 'Dashboard Keuangan - ' . $monthName,
            'monthName' => $monthName,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'countTransaksi' => $countTransaksi,
            'saldoUangSaku' => $saldoUangSaku,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('keuangan::create', [
            'title' => 'Tambah Keuangan',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Create record

        return redirect()->route('keuangan.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // TODO: Find record
        $keuangan = null;
        
        return view('keuangan::show', [
            'title' => 'Detail Keuangan',
            'keuangan' => $keuangan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // TODO: Find record
        $keuangan = null;
        
        return view('keuangan::edit', [
            'title' => 'Edit Keuangan',
            'keuangan' => $keuangan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            // TODO: Add validation rules
        ]);

        // TODO: Update record

        return redirect()->route('keuangan.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // TODO: Delete record

        return redirect()->route('keuangan.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
