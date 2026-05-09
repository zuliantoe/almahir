<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class); // Default Admin
        if (class_exists(AcademicRoleSeeder::class)) {
            $this->call(AcademicRoleSeeder::class);
        }

        // 2. Base User Data (Guru & Siswa)
        $this->call(GuruDataSeeder::class);
        $this->call(SiswaDataSeeder::class);

        // 3. Academic Structure
        if (class_exists(\App\Modules\Akademik\Database\Seeders\SpecificAcademicSeeder::class)) {
            $this->call(\App\Modules\Akademik\Database\Seeders\SpecificAcademicSeeder::class);
        }
        $this->call(JadwalPelajaranSeeder::class);

        // 4. Assignments
        $this->call(AssignStudentsToRombelSeeder::class);

        // 5. Module Specific Integration (Penilaian & Presensi)
        if (class_exists(\Modules\PenilaianDanPresensi\Database\Seeders\PenilaianIntegrasiSeeder::class)) {
            $this->call(\Modules\PenilaianDanPresensi\Database\Seeders\PenilaianIntegrasiSeeder::class);
        }
    }
}
