<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Guru\Models\Guru;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Kelas;
use App\Models\User;

class WaliKelasTestingSeeder extends Seeder
{
    public function run(): void
    {
        $teacherNames = [
            'Budi Santoso, S.Pd',
            'Siti Aminah, M.Pd',
            'Agus Hermawan, S.T',
            'Dewi Lestari, S.S'
        ];

        $gurus = Guru::whereIn('nama', $teacherNames)->get();
        
        // Ensure they have the correct roles if necessary
        foreach ($gurus as $guru) {
            $user = User::where('ref_type', Guru::class)->where('ref_id', $guru->id)->first();
            if ($user) {
                // Ensure they have GURU role at least
                if (!$user->hasRole('GURU')) {
                    $user->assignRole('GURU');
                }
            }
        }

        $rombels = Rombel::all();
        
        foreach ($gurus as $index => $guru) {
            if (isset($rombels[$index])) {
                $rombel = $rombels[$index];
                $rombel->wali_kelas_id = $guru->id;
                $rombel->save();
                $this->command->info("✓ Assigned {$guru->nama} as Wali Kelas for {$rombel->nama_rombel}");
            }
        }
    }
}
