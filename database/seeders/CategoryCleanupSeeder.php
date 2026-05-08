<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Akademik\Models\KategoriPelajaran;
use App\Modules\Akademik\Models\MataPelajaran;

class CategoryCleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $katDiniyyah = KategoriPelajaran::firstOrCreate(['kategori' => 'Diniyyah']);
        $katUmum = KategoriPelajaran::firstOrCreate(['kategori' => 'Umum']);
        
        $diniyyahNames = [
            'Bahasa Arab', 
            'Tahfidz', 
            'Fiqih', 
            'Aqidah', 
            'Tarikh', 
            'Hadits', 
            'Tahfidz Al-Quran',
            'Tahfidz Al-Qur\'an'
        ];
        
        // 1. Move religious subjects to Diniyyah
        MataPelajaran::whereIn('nama', $diniyyahNames)->update(['kategori_id' => $katDiniyyah->id]);
        
        // 2. Move other subjects to Umum (except ones that should stay in Diniyyah)
        MataPelajaran::whereNotIn('nama', $diniyyahNames)->update(['kategori_id' => $katUmum->id]);
        
        echo "Successfully cleaned up subject categories.\n";
    }
}
