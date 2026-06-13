<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CalonPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari tipe pegawai secara acak jika ada
        $typeGuru = \Modules\PegawaiManager\Models\TypePegawai::where('nama_type', 'like', '%Guru%')->first();
        $typeStaff = \Modules\PegawaiManager\Models\TypePegawai::where('nama_type', 'like', '%Staff%')->first();

        $data = [
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'nama' => 'Budi Santoso',
                'email' => 'budisantoso.lamar@gmail.com',
                'no_hp' => '081234567890',
                'type_pegawai_id' => $typeGuru ? $typeGuru->id : null,
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1995-05-10',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta Selatan',
                'status_seleksi' => 'baru',
                'tanggal_melamar' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'nama' => 'Siti Aminah',
                'email' => 'sitiaminah.pelamar@yahoo.com',
                'no_hp' => '085678901234',
                'type_pegawai_id' => $typeGuru ? $typeGuru->id : null,
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1998-08-15',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asia Afrika No. 45, Bandung',
                'status_seleksi' => 'wawancara',
                'tanggal_melamar' => now()->subDays(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'nama' => 'Andi Wijaya',
                'email' => 'andi.wijaya.kerja@gmail.com',
                'no_hp' => '081987654321',
                'type_pegawai_id' => $typeStaff ? $typeStaff->id : null,
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1996-12-01',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Pemuda No. 10, Surabaya',
                'status_seleksi' => 'baru',
                'tanggal_melamar' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \Modules\PegawaiManager\Models\CalonPegawai::insert($data);
    }
}
