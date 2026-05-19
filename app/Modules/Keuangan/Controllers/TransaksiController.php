<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Keuangan\Models\Pemasukan;
use Modules\Keuangan\Models\Pengeluaran;
use Carbon\Carbon;

/**
 * TransaksiController
 * 
 * Menangani halaman rangkuman transaksi (Pemasukan & Pengeluaran)
 */
class TransaksiController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n')); // Default ke bulan ini
        $currentType = $request->get('type', 'all'); // Default ke semua transaksi

        // Mengambil dan memformat data Pemasukan
        $pemasukans = Pemasukan::with('sumber')->get()->map(function ($item) {
            // Gunakan created_at dengan timezone Jakarta dan format H.i untuk Jam WIB
            $waktu = optional($item->created_at)->setTimezone('Asia/Jakarta')->format('H.i') ?? '-';
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'waktu' => $waktu,
                'jenis' => 'Pemasukan',
                'keterangan' => $item->sumber ? $item->sumber->nama : '-',
                'deskripsi' => $item->deskripsi,
                'jumlah' => $item->jumlah,
            ];
        });

        // Mengambil dan memformat data Pengeluaran
        $pengeluarans = Pengeluaran::with('tujuan')->get()->map(function ($item) {
            $waktu = optional($item->created_at)->setTimezone('Asia/Jakarta')->format('H.i') ?? '-';
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'waktu' => $waktu,
                'jenis' => 'Pengeluaran',
                'keterangan' => $item->tujuan ? $item->tujuan->nama : '-',
                'deskripsi' => $item->deskripsi,
                'jumlah' => $item->jumlah,
            ];
        });

        // Gabungkan semua transaksi
        $allTransactions = collect($pemasukans)->merge($pengeluarans);

        // Filter Tahun
        $transactionsForYear = $allTransactions->filter(function($item) use ($currentYear) {
            return Carbon::parse($item['tanggal'])->format('Y') == $currentYear;
        });

        // Kalkulasi Total Saldo Tahun (semua bulan di tahun tersebut)
        $totalPemasukanYear = $transactionsForYear->where('jenis', 'Pemasukan')->sum('jumlah');
        $totalPengeluaranYear = $transactionsForYear->where('jenis', 'Pengeluaran')->sum('jumlah');
        $totalSaldoYear = $totalPemasukanYear - $totalPengeluaranYear;

        // Filter Bulan
        $filteredTransactions = $transactionsForYear;
        if ($currentMonth != 'all') {
            $filteredTransactions = $filteredTransactions->filter(function($item) use ($currentMonth) {
                return Carbon::parse($item['tanggal'])->format('n') == $currentMonth;
            });
        }

        // Filter Jenis
        if ($currentType != 'all') {
            $filteredTransactions = $filteredTransactions->filter(function($item) use ($currentType) {
                return $item['jenis'] == $currentType;
            });
        }

        // Kalkulasi untuk Kartu Statistik (Berdasarkan Filter)
        $totalPemasukanFilter = $filteredTransactions->where('jenis', 'Pemasukan')->sum('jumlah');
        $totalPengeluaranFilter = $filteredTransactions->where('jenis', 'Pengeluaran')->sum('jumlah');
        $totalTransaksiFilter = $filteredTransactions->count();

        // Urutkan berdasarkan tanggal & waktu Ascending (Kronologis) untuk Buku Tabungan
        $sortedTransactions = $filteredTransactions->sortBy(function($item) {
            return Carbon::parse($item['tanggal'])->format('Y-m-d') . ' ' . $item['waktu'];
        })->values();

        // Group by Tanggal
        $groupedTransactions = $sortedTransactions->groupBy(function($item) {
            return Carbon::parse($item['tanggal'])->format('Y-m-d');
        });

        // Ambil daftar tahun untuk dropdown filter
        $allYears = $allTransactions->map(function($item) {
            return Carbon::parse($item['tanggal'])->format('Y');
        })->unique()->sort()->values();
        if ($allYears->isEmpty()) {
            $allYears = collect([date('Y')]);
        }

        return view('keuangan::transaksis.index', compact(
            'groupedTransactions', 'currentYear', 'currentMonth', 'currentType', 'allYears',
            'totalSaldoYear', 'totalPemasukanFilter', 'totalPengeluaranFilter', 'totalTransaksiFilter'
        ));
    }

    public function print(Request $request): View
    {
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n')); 
        $currentType = $request->get('type', 'all');

        $pemasukans = Pemasukan::with('sumber')->get()->map(function ($item) {
            $waktu = optional($item->created_at)->setTimezone('Asia/Jakarta')->format('H.i') ?? '-';
            return [
                'tanggal' => $item->tanggal,
                'waktu' => $waktu,
                'jenis' => 'Pemasukan',
                'keterangan' => $item->sumber ? $item->sumber->nama : '-',
                'deskripsi' => $item->deskripsi,
                'jumlah' => $item->jumlah,
            ];
        });

        $pengeluarans = Pengeluaran::with('tujuan')->get()->map(function ($item) {
            $waktu = optional($item->created_at)->setTimezone('Asia/Jakarta')->format('H.i') ?? '-';
            return [
                'tanggal' => $item->tanggal,
                'waktu' => $waktu,
                'jenis' => 'Pengeluaran',
                'keterangan' => $item->tujuan ? $item->tujuan->nama : '-',
                'deskripsi' => $item->deskripsi,
                'jumlah' => $item->jumlah,
            ];
        });

        $allTransactions = collect($pemasukans)->merge($pengeluarans);

        $filteredTransactions = $allTransactions->filter(function($item) use ($currentYear) {
            return Carbon::parse($item['tanggal'])->format('Y') == $currentYear;
        });

        if ($currentMonth != 'all') {
            $filteredTransactions = $filteredTransactions->filter(function($item) use ($currentMonth) {
                return Carbon::parse($item['tanggal'])->format('n') == $currentMonth;
            });
        }

        if ($currentType != 'all') {
            $filteredTransactions = $filteredTransactions->filter(function($item) use ($currentType) {
                return $item['jenis'] == $currentType;
            });
        }

        $totalPemasukanFilter = $filteredTransactions->where('jenis', 'Pemasukan')->sum('jumlah');
        $totalPengeluaranFilter = $filteredTransactions->where('jenis', 'Pengeluaran')->sum('jumlah');
        $totalSaldoFilter = $totalPemasukanFilter - $totalPengeluaranFilter;

        $sortedTransactions = $filteredTransactions->sortBy(function($item) {
            return Carbon::parse($item['tanggal'])->format('Y-m-d') . ' ' . $item['waktu'];
        })->values();

        $groupedTransactions = $sortedTransactions->groupBy(function($item) {
            return Carbon::parse($item['tanggal'])->format('Y-m-d');
        });

        return view('keuangan::transaksis.print', compact(
            'groupedTransactions', 'currentYear', 'currentMonth', 'currentType',
            'totalPemasukanFilter', 'totalPengeluaranFilter', 'totalSaldoFilter'
        ));
    }
}
