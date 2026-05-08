<?php

namespace Modules\Keuangan\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Keuangan\Models\Pemasukan;
use Modules\Keuangan\Models\Sumber;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class PemasukanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada data di tabel sumbers
        $sumbers = Sumber::all();

        if ($sumbers->isEmpty()) {
            $sampleSumbers = ['Dana BOS', 'Donasi Alumni', 'Sewa Gedung', 'Kantin Sekolah', 'Iuran Bulanan'];
            foreach ($sampleSumbers as $nama) {
                Sumber::create(['nama' => $nama]);
            }
            $sumbers = Sumber::all();
        }

        $sumberIds = $sumbers->pluck('id')->toArray();

        // Data dummy deskripsi
        $deskripsiSamples = [
            'Pembayaran iuran semester ganjil',
            'Sumbangan sukarela dari wali murid',
            'Hasil penjualan kupon bazar sekolah',
            'Pencairan dana bantuan operasional tahap 1',
            'Penerimaan sewa lapangan olahraga',
            'Hibah dari perusahaan mitra',
            'Hasil bunga bank bulan ini',
            'Pengembalian sisa dana kepanitiaan',
            'Pendapatan dari unit produksi',
            null
        ];

        // Buat 10 data dummy
        for ($i = 0; $i < 10; $i++) {
            Pemasukan::create([
                'sumber_id' => Arr::random($sumberIds),
                'jumlah'    => rand(100, 5000) * 1000, // Antara 100rb sampai 5jt
                'tanggal'   => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
                'waktu'     => sprintf('%02d:%02d:%02d', rand(8, 16), rand(0, 59), rand(0, 59)),
                'deskripsi' => Arr::random($deskripsiSamples),
            ]);
        }
    }
}
