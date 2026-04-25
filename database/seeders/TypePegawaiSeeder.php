<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PegawaiManager\Models\TypePegawai;
use Illuminate\Support\Str;

class TypePegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clear existing types first (to ensure a clean state)
        TypePegawai::truncate();

        // 2. Add exactly the three types requested
        $types = [
            ['nama_type' => 'Admin (Super Admin)'],
            ['nama_type' => 'Guru'],
            ['nama_type' => 'Pegawai'],
        ];

        foreach ($types as $type) {
            TypePegawai::create([
                'id' => (string) Str::uuid(),
                'nama_type' => $type['nama_type']
            ]);
        }

        $this->command->info('✓ Type Pegawai seeded with exact requirements.');
    }
}
