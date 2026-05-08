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
        // Seed roles first
        $this->call(RoleSeeder::class);
        
        // Then seed users
        $this->call(UserSeeder::class);
        // Guru & Siswa Data (Orang & Akun)
        $this->call([
            GuruDataSeeder::class,
            SiswaDataSeeder::class,
        ]);

        // Academic Dummy Data
        if (class_exists(\App\Modules\Akademik\Database\Seeders\AcademicDummySeeder::class)) {
            $this->call(\App\Modules\Akademik\Database\Seeders\AcademicDummySeeder::class);
        }
    }
}
