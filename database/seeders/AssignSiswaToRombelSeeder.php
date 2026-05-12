<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignSiswaToRombelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // 1. Pastikan ada Tingkat
            $tingkatId = DB::table('tingkat')->insertGetId([
                'kode_tingkat' => '10',
                'nama_tingkat' => 'Kelas 10',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Pastikan ada Kelas
            $guruId = DB::table('guru')->value('id');
            $kelasId = DB::table('kelas')->insertGetId([
                'nama_kelas' => '10',
                'kode_kelas' => 'K10',
                'tingkat_id' => $tingkatId,
                'guru_id' => $guruId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 3. Buat Rombel 10-A
            $rombelId = DB::table('rombel')->insertGetId([
                'nama_rombel' => '10-A',
                'kelas_id' => $kelasId,
                'tahunajaran_id' => 1, // Berdasarkan pengecekan sebelumnya
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 4. Ambil semua siswa dan masukkan ke rombel serta update kelas_id di tabel siswa
            $siswas = DB::table('siswa')->get();
            foreach ($siswas as $siswa) {
                // Insert ke pivot
                DB::table('rombel_siswa')->insert([
                    'rombel_id' => $rombelId,
                    'siswa_id' => $siswa->id,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update kolom kelas_id di tabel siswa agar muncul di UI
                DB::table('siswa')->where('id', $siswa->id)->update([
                    'kelas_id' => $kelasId
                ]);
            }

            DB::commit();
            $this->command->info("Berhasil membuat Rombel 10-A dan memasukkan " . count($siswas) . " siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Gagal: " . $e->getMessage());
        }
    }
}
