<?php

namespace App\Modules\Akademik\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Akademik\Models\JadwalPelajaran;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\MataPelajaran;
use Modules\Guru\Models\Guru;
use Illuminate\Support\Facades\Schema;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        JadwalPelajaran::truncate();
        Schema::enableForeignKeyConstraints();

        $rombel = Rombel::first();
        $mapels = MataPelajaran::all();
        $gurus = Guru::all();

        if (!$rombel || $mapels->isEmpty() || $gurus->isEmpty()) return;

        for ($i = 1; $i <= 10; $i++) {
            JadwalPelajaran::create([
                'rombel_id' => $rombel->id,
                'hari' => rand(1, 6), // 1=Senin, 6=Sabtu
                'jamke' => $i,
                'jamawal' => '07:00:00',
                'jamakhir' => '08:00:00',
                'mapel_id' => $mapels->random()->id,
                'guru_id' => $gurus->random()->id,
            ]);
        }
    }
}
