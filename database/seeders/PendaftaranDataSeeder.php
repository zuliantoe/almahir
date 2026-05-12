<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Pendaftaran\Models\Pendaftaran;
use Modules\Pendaftaran\Models\Seleksi;
use Modules\Pendaftaran\Models\TemplateSeleksi;
use Faker\Factory as Faker;

class PendaftaranDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Cek atau buat template seleksi
        $template = TemplateSeleksi::first();
        if (!$template) {
            $template = TemplateSeleksi::create([
                'nama_template' => 'Template Seleksi Standar',
                'deskripsi' => 'Template otomatis untuk testing'
            ]);
            $template->items()->createMany([
                ['nama_tes' => 'Tes Baca Al-Quran', 'metode' => 'offline', 'pengampu' => 'Ust. Ahmad'],
                ['nama_tes' => 'Tes Akademik', 'metode' => 'offline', 'pengampu' => 'Bpk. Budi'],
                ['nama_tes' => 'Wawancara Orang Tua', 'metode' => 'offline', 'pengampu' => 'Ibu Siti']
            ]);
        }

        $statuses = [
            'pending' => 10,  // Ditunda
            'diproses' => 10,
            'diterima' => 10
        ];

        $this->command->info('Menciptakan data Pendaftaran (10 per status)...');

        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $pendaftaran = Pendaftaran::create([
                    'nisn' => $faker->unique()->numerify('##########'),
                    'nama_lengkap' => $faker->name,
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $faker->date('Y-m-d', '2015-12-31'),
                    'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                    'berat_badan' => $faker->numberBetween(20, 50),
                    'tinggi_badan' => $faker->numberBetween(110, 160),
                    'kelurahan' => $faker->citySuffix,
                    'kecamatan' => $faker->city,
                    'kota' => $faker->city,
                    'provinsi' => $faker->state,
                    'alamat' => $faker->address,
                    'nama_ayah' => $faker->name('male'),
                    'pekerjaan_ayah' => $faker->jobTitle,
                    'no_hp_ayah' => $faker->numerify('08##########'),
                    'nama_ibu' => $faker->name('female'),
                    'pekerjaan_ibu' => $faker->jobTitle,
                    'no_hp_ibu' => $faker->numerify('08##########'),
                    'email' => $faker->unique()->safeEmail,
                    'status' => $status,
                    'tanggal_daftar' => now()->subDays($faker->numberBetween(1, 30)),
                    'tanggal_diterima' => ($status === 'diterima') ? now() : null,
                ]);

                // Jika status diproses, tambahkan jadwal seleksi
                if ($status === 'diproses') {
                    foreach ($template->items as $item) {
                        Seleksi::create([
                            'pendaftaran_id' => $pendaftaran->id,
                            'nama_tes' => $item->nama_tes,
                            'tanggal' => now()->addDays($faker->numberBetween(1, 7)),
                            'jam' => '08:00:00',
                            'pengampu' => $item->pengampu ?? 'Staf Akademik',
                            'metode' => $item->metode ?? 'offline',
                            'lokasi' => 'Gedung Utama',
                        ]);
                    }
                }
            }
        }
    }
}
