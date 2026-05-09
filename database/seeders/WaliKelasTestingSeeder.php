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
        // Get all available gurus
        $gurus = Guru::all();
        
        if ($gurus->isEmpty()) {
            $this->command->error("× No Guru data found in database. Please seed Guru data first.");
            return;
        }

        $rombels = Rombel::all();
        
        foreach ($rombels as $index => $rombel) {
            // Pick a guru based on index (loop back if more rombels than gurus)
            $guru = $gurus[$index % $gurus->count()];
            
            $rombel->guru_id = $guru->id;
            $rombel->save();
            
            $this->command->info("✓ Assigned {$guru->nama} as Wali Kelas for {$rombel->nama_rombel}");
            
            // Ensure they have the correct roles if necessary
            $user = User::where('ref_type', Guru::class)->where('ref_id', $guru->id)->first();
            if ($user && !$user->hasRole('GURU')) {
                $user->assignRole('GURU');
            }
        }
    }
}
