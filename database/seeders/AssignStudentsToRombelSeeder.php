<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;

class AssignStudentsToRombelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rombels = Rombel::all();
        if ($rombels->isEmpty()) {
            return;
        }

        $siswas = Siswa::all();
        foreach ($siswas as $siswa) {
            $randomRombel = $rombels->random();
            
            // Ensure RombelSiswa entry exists
            RombelSiswa::updateOrCreate(
                ['siswa_id' => $siswa->id],
                ['rombel_id' => $randomRombel->id]
            );

            // Sync the direct kelas_id on the siswa record
            $siswa->update(['kelas_id' => $randomRombel->kelas_id]);
        }
    }
}
