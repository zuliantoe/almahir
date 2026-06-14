<?php

namespace App\Modules\ManajemenAsetDanAsrama\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\Siswa\Models\Siswa;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use App\Modules\ManajemenAsetDanAsrama\Models\Kerusakan;
use App\Modules\ManajemenAsetDanAsrama\Models\Pemeliharaan;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\PengadaanAset;

class ManajemenAsetDanAsramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan minimal ada 1 Admin
        if (User::count() === 0) {
            $this->call(\Database\Seeders\RoleSeeder::class);
            $this->call(\Database\Seeders\UserSeeder::class);
        }
        
        $admin = User::first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin Utama',
                'username' => 'admin',
                'email' => 'admin@siakad.local',
                'password' => bcrypt('password'),
                'account_status' => 'active',
            ]);
            $admin->assignRole('ADMIN');
        }

        // 2. Pastikan minimal ada data Siswa
        if (Siswa::count() === 0) {
            $this->call(\Database\Seeders\SiswaDataSeeder::class);
        }

        $siswas = Siswa::aktif()->get();

        // 3. Seed Kamar
        $kamarData = [
            [
                'nama_kamar' => 'Kamar Abu Bakar (A-1)',
                'kapasitas' => 6,
                'deskripsi' => 'Kamar santri putra tingkat Aliyah, gedung barat lantai 1.',
            ],
            [
                'nama_kamar' => 'Kamar Umar bin Khattab (A-2)',
                'kapasitas' => 6,
                'deskripsi' => 'Kamar santri putra tingkat Aliyah, gedung barat lantai 1.',
            ],
            [
                'nama_kamar' => 'Kamar Utsman bin Affan (B-1)',
                'kapasitas' => 8,
                'deskripsi' => 'Kamar santri putra tingkat Tsanawiyah, gedung timur lantai 1.',
            ],
            [
                'nama_kamar' => 'Kamar Ali bin Abi Thalib (B-2)',
                'kapasitas' => 8,
                'deskripsi' => 'Kamar santri putra tingkat Tsanawiyah, gedung timur lantai 1.',
            ],
        ];

        $kamars = [];
        foreach ($kamarData as $data) {
            $kamars[] = Kamar::updateOrCreate(
                ['nama_kamar' => $data['nama_kamar']],
                $data
            );
        }

        // 4. Seed KamarPenghuni (Tempatkan Siswa ke Kamar)
        $index = 0;
        foreach ($kamars as $kamar) {
            // Assign 2 santri untuk setiap kamar
            for ($i = 0; $i < 2; $i++) {
                if (isset($siswas[$index])) {
                    $siswa = $siswas[$index];
                    
                    KamarPenghuni::updateOrCreate(
                        [
                            'kamar_id' => $kamar->id,
                            'siswa_id' => $siswa->id,
                        ],
                        [
                            'tanggal_masuk' => now()->subMonths(3),
                            'tanggal_keluar' => null,
                            'jabatan' => $i === 0 ? 'Ketua Kamar' : 'Anggota',
                            'keterangan' => $i === 0 ? 'Ketua Kamar' : 'Anggota Kamar',
                        ]
                    );
                    $index++;
                }
            }
        }

        // 5. Seed PengajuanAset
        $pengajuans = [
            [
                'nomor_pengajuan' => 'REQ-2026-0001',
                'nama_aset' => 'Kasur Lipat Busa Super',
                'deskripsi_pengajuan' => 'Pengajuan 10 unit kasur lipat busa untuk kenyamanan santri baru.',
                'estimasi_harga' => 3500000.00,
                'tanggal_pengajuan' => now()->subWeeks(4),
                'pengaju_id' => $admin->id,
                'status' => PengajuanAset::STATUS_PROSES_PENGADAAN,
            ],
            [
                'nomor_pengajuan' => 'REQ-2026-0002',
                'nama_aset' => 'Lemari Pakaian Kayu 2 Pintu',
                'deskripsi_pengajuan' => 'Pengajuan 4 unit lemari pakaian kayu untuk Kamar Utsman.',
                'estimasi_harga' => 4800000.00,
                'tanggal_pengajuan' => now()->subWeeks(3),
                'pengaju_id' => $admin->id,
                'status' => PengajuanAset::STATUS_DISETUJUI,
                'approved_by' => $admin->id,
                'approved_at' => now()->subWeeks(2),
            ],
            [
                'nomor_pengajuan' => 'REQ-2026-0003',
                'nama_aset' => 'Kipas Angin Dinding Cosmos',
                'deskripsi_pengajuan' => 'Pengajuan kipas angin dinding untuk mengganti kipas angin yang rusak di Kamar Umar.',
                'estimasi_harga' => 250000.00,
                'tanggal_pengajuan' => now()->subWeeks(1),
                'pengaju_id' => $admin->id,
                'status' => PengajuanAset::STATUS_DIAJUKAN,
            ],
        ];

        $seededRequests = [];
        foreach ($pengajuans as $pData) {
            $seededRequests[] = PengajuanAset::updateOrCreate(
                ['nomor_pengajuan' => $pData['nomor_pengajuan']],
                $pData
            );
        }

        // 6. Seed PengadaanAset
        $pengadaans = [];
        $req1 = $seededRequests[0] ?? null;
        if ($req1) {
            $pengadaans[] = PengadaanAset::updateOrCreate(
                ['nomor_po' => 'PO-2026-0001'],
                [
                    'pengajuan_id' => $req1->id,
                    'vendor' => 'CV. Jaya Mandiri Furniture',
                    'tanggal_pesan' => now()->subWeeks(3),
                    'estimasi_datang' => now()->subWeeks(2),
                    'tanggal_datang' => now()->subWeeks(2),
                    'biaya_riil' => 3450000.00,
                    'catatan_pengadaan' => 'Barang sudah datang dan diletakkan di gudang asrama.',
                    'status' => 'datang',
                ]
            );
        }

        // 7. Seed Aset
        $asetsData = [
            [
                'kode_aset' => 'AST-0001',
                'nama_aset' => 'Kasur Lipat Busa Super',
                'tanggal_pengajuan' => now()->subWeeks(4),
                'harga' => 350000.00,
                'status_kondisi' => 'baik',
                'tanggal_pengadaan' => now()->subWeeks(2),
                'kondisi' => '100% baru dan empuk',
                'deskripsi_aset' => 'Ditempatkan di Kamar Abu Bakar.',
                'kamar_id' => $kamars[0]->id,
                'pengadaan_id' => isset($pengadaans[0]) ? $pengadaans[0]->id : null,
            ],
            [
                'kode_aset' => 'AST-0002',
                'nama_aset' => 'Kipas Angin Dinding Cosmos',
                'tanggal_pengajuan' => now()->subWeeks(8),
                'harga' => 245000.00,
                'status_kondisi' => 'rusak',
                'tanggal_pengadaan' => now()->subMonths(6),
                'kondisi' => 'Putaran lambat dan berbunyi bising',
                'deskripsi_aset' => 'Kipas angin dinding di Kamar Umar.',
                'kamar_id' => $kamars[1]->id,
            ],
            [
                'kode_aset' => 'AST-0003',
                'nama_aset' => 'Lemari Pakaian Kayu 2 Pintu',
                'tanggal_pengajuan' => now()->subWeeks(6),
                'harga' => 1200000.00,
                'status_kondisi' => 'baik',
                'tanggal_pengadaan' => now()->subMonths(3),
                'kondisi' => 'Kondisi pintu dan kunci baik',
                'deskripsi_aset' => 'Ditempatkan di Kamar Utsman.',
                'kamar_id' => $kamars[2]->id,
            ],
            [
                'kode_aset' => 'AST-0004',
                'nama_aset' => 'Dispenser Air Sharp',
                'tanggal_pengajuan' => now()->subWeeks(10),
                'harga' => 850000.00,
                'status_kondisi' => 'dalam_perbaikan',
                'tanggal_pengadaan' => now()->subMonths(8),
                'kondisi' => 'Kran air panas bocor',
                'deskripsi_aset' => 'Dispenser air di lobi asrama putra.',
                'kamar_id' => $kamars[3]->id,
            ],
        ];

        $seededAsets = [];
        foreach ($asetsData as $aData) {
            $seededAsets[] = Aset::updateOrCreate(
                ['kode_aset' => $aData['kode_aset']],
                $aData
            );
        }

        // 8. Seed Kerusakan
        $kerusakans = [
            [
                'aset_id' => $seededAsets[1]->id, // Kipas angin rusak
                'tanggal_rusak' => now()->subWeeks(2),
                'tanggal_kerusakan' => now()->subWeeks(2),
                'deskripsi_kerusakan' => 'Kipas angin mati total tidak berputar sama sekali.',
                'tingkat_kerusakan' => 'berat',
                'status_penanganan' => 'belum_ditangani',
                'catatan' => 'Perlu segera dikoordinasikan dengan teknisi.',
            ],
            [
                'aset_id' => $seededAsets[3]->id, // Dispenser air bocor
                'tanggal_rusak' => now()->subWeeks(3),
                'tanggal_kerusakan' => now()->subWeeks(3),
                'deskripsi_kerusakan' => 'Kran pemanas bocor merembes ke lantai.',
                'tingkat_kerusakan' => 'sedang',
                'status_penanganan' => 'sedang_ditangani',
                'catatan' => 'Sudah dipanggil teknisi luar untuk perbaikan kran.',
            ],
        ];

        $seededKerusakans = [];
        foreach ($kerusakans as $kData) {
            $seededKerusakans[] = Kerusakan::updateOrCreate(
                [
                    'aset_id' => $kData['aset_id'],
                    'deskripsi_kerusakan' => $kData['deskripsi_kerusakan'],
                ],
                $kData
            );
        }

        // 9. Seed Pemeliharaan
        $pemeliharaans = [
            [
                'aset_id' => $seededAsets[3]->id, // Dispenser air sedang diperbaiki
                'tanggal_mulai_pemeliharaan' => now()->subDays(3),
                'tanggal_pemeliharaan' => now()->subDays(3),
                'deskripsi_pemeliharaan' => 'Penggantian sparepart kran pemanas dispenser Sharp.',
                'biaya_pemeliharaan' => 150000.00,
                'biaya' => 150000.00,
                'status' => 'proses',
                'catatan' => 'Menunggu kran cadangan dari toko.',
            ]
        ];

        foreach ($pemeliharaans as $pemData) {
            Pemeliharaan::updateOrCreate(
                [
                    'aset_id' => $pemData['aset_id'],
                    'deskripsi_pemeliharaan' => $pemData['deskripsi_pemeliharaan'],
                ],
                $pemData
            );
        }

        // 10. Seed JadwalPiket
        $days = [now()->subDay(), now(), now()->addDay(), now()->addDays(2)];
        $shifts = ['pagi', 'sore'];
        $lokasiPikets = ['Kamar Mandi', 'Halaman Asrama', 'Teras Kamar', 'Koridor'];

        $siswaPenghuni = KamarPenghuni::aktif()->get();

        if ($siswaPenghuni->count() > 0) {
            foreach ($days as $day) {
                foreach ($shifts as $shift) {
                    $randomPenghuni = $siswaPenghuni->random();
                    JadwalPiket::updateOrCreate(
                        [
                            'tanggal' => $day->format('Y-m-d'),
                            'shift' => $shift,
                            'siswa_id' => $randomPenghuni->siswa_id,
                        ],
                        [
                            'kamar_id' => $randomPenghuni->kamar_id,
                            'lokasi_piket' => collect($lokasiPikets)->random(),
                            'status' => $day->isPast() ? 'sudah' : 'belum',
                        ]
                    );
                }
            }
        }
    }
}
