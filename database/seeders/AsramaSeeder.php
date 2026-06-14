<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;
use Modules\Siswa\Models\Siswa;
use App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService;

class AsramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan tabel siswa terisi
        if (Siswa::count() === 0) {
            $this->command->info('Tabel siswa kosong. Menjalankan SiswaDataSeeder terlebih dahulu...');
            $siswaSeeder = new SiswaDataSeeder();
            $siswaSeeder->run();
        }

        $allSiswa = Siswa::aktif()->get();
        if ($allSiswa->isEmpty()) {
            $this->command->error('Tidak ada siswa dengan status "aktif". Seeder asrama dihentikan.');
            return;
        }

        // 2. Bersihkan data asrama lama (Truncate/Delete)
        $this->command->info('Membersihkan data lama modul Manajemen Aset & Asrama...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        JadwalPiket::truncate();
        KamarPenghuni::truncate();
        Kamar::truncate();
        Aset::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Buat Kamar Asrama baru
        $this->command->info('Membuat data Kamar Asrama...');
        $kamars = [
            [
                'nama_kamar' => 'Kamar Abu Bakar Ash-Shiddiq',
                'kapasitas' => 10,
                'deskripsi' => 'Gedung Asrama Lantai 1 - Khusus Santri Baru',
            ],
            [
                'nama_kamar' => 'Kamar Umar bin Khattab',
                'kapasitas' => 10,
                'deskripsi' => 'Gedung Asrama Lantai 1',
            ],
            [
                'nama_kamar' => 'Kamar Utsman bin Affan',
                'kapasitas' => 10,
                'deskripsi' => 'Gedung Asrama Lantai 2',
            ],
            [
                'nama_kamar' => 'Kamar Ali bin Abi Thalib',
                'kapasitas' => 10,
                'deskripsi' => 'Gedung Asrama Lantai 2',
            ],
        ];

        $createdKamars = [];
        foreach ($kamars as $k) {
            $createdKamars[] = Kamar::create($k);
        }

        // 4. Masukkan Santri ke Kamar (Kamar Penghuni)
        $this->command->info('Memasukkan santri ke dalam Kamar (Kamar Penghuni)...');
        $siswaChunks = $allSiswa->chunk(8); // Bagi santri ke 4 kamar (maks 8 per kamar)
        
        $kamarIndex = 0;
        foreach ($siswaChunks as $chunk) {
            if ($kamarIndex >= count($createdKamars)) break;
            $kamar = $createdKamars[$kamarIndex];
            
            $isFirst = true;
            $isSecond = false;
            foreach ($chunk as $siswa) {
                $jabatan = 'Anggota';
                if ($isFirst) {
                    $jabatan = 'Ketua Kamar';
                    $isFirst = false;
                    $isSecond = true;
                } elseif ($isSecond) {
                    $jabatan = 'Wakil Ketua Kamar';
                    $isSecond = false;
                }

                KamarPenghuni::create([
                    'kamar_id' => $kamar->id,
                    'siswa_id' => $siswa->id,
                    'tanggal_masuk' => now()->subMonths(3),
                    'tanggal_keluar' => null,
                    'jabatan' => $jabatan,
                    'keterangan' => $jabatan . ' - Ditugaskan via System Seeder',
                ]);
            }
            $kamarIndex++;
        }

        // 5. Buat Jadwal Piket Kebersihan untuk 7 Hari ke Depan
        $this->command->info('Membuat Jadwal Piket Harian untuk 7 hari ke depan...');
        $locations = [
            ['nama' => 'Masjid', 'kuota' => 2],
            ['nama' => 'Kantor', 'kuota' => 1],
            ['nama' => 'Halaman Asrama', 'kuota' => 2],
            ['nama' => 'Koridor Lantai 1', 'kuota' => 1],
            ['nama' => 'Koridor Lantai 2', 'kuota' => 1],
        ];

        $piketService = new JadwalPiketService();
        
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(7)->format('Y-m-d');

        // Jalankan untuk shift pagi
        $piketService->generateSmart($startDate, $endDate, 'pagi', $locations);
        // Jalankan untuk shift sore
        $piketService->generateSmart($startDate, $endDate, 'sore', $locations);

        // 6. Buat Data Master Aset
        $this->command->info('Membuat data inventaris/Master Aset...');
        $assets = [
            [
                'kode_aset' => 'AST-2026-0001',
                'nama_aset' => 'Kasur Busa Inoac No. 3',
                'harga' => 350000,
                'status' => 'baik',
                'tanggal_pengadaan' => now()->subYears(1),
                'kondisi' => 'Baik',
                'deskripsi_aset' => 'Kasur busa untuk ranjang susun asrama',
            ],
            [
                'kode_aset' => 'AST-2026-0002',
                'nama_aset' => 'Lemari Pakaian Kayu 2 Pintu',
                'harga' => 450000,
                'status' => 'baik',
                'tanggal_pengadaan' => now()->subYears(1),
                'kondisi' => 'Baik',
                'deskripsi_aset' => 'Lemari pakaian kayu bersama untuk 2 santri',
            ],
            [
                'kode_aset' => 'AST-2026-0003',
                'nama_aset' => 'Kipas Angin Dinding Cosmos',
                'harga' => 220000,
                'status' => 'rusak',
                'tanggal_pengadaan' => now()->subMonths(6),
                'kondisi' => 'Baling-baling patah',
                'deskripsi_aset' => 'Kipas angin dinding di Kamar Abu Bakar',
            ],
            [
                'kode_aset' => 'AST-2026-0004',
                'nama_aset' => 'Sapu Serat Rayung Tebal',
                'harga' => 25000,
                'status' => 'baik',
                'tanggal_pengadaan' => now()->subWeeks(2),
                'kondisi' => 'Sangat Baik',
                'deskripsi_aset' => 'Sapu untuk kebersihan koridor',
            ],
        ];

        foreach ($assets as $a) {
            Aset::create($a);
        }

        $this->command->info('✓ Seeder Asrama berhasil dijalankan.');
    }
}
