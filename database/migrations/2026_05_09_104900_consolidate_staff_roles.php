<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add PEGAWAI role
        $exists = DB::table('sys_roles')->where('name', 'PEGAWAI')->exists();
        if (!$exists) {
            DB::table('sys_roles')->insert([
                'id' => Str::uuid(),
                'name' => 'PEGAWAI',
                'display_name' => 'Pegawai',
                'description' => 'Akses untuk Staf / Pegawai Sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Remove old staff roles as requested
        DB::table('sys_roles')->whereIn('name', ['STAF_TU', 'KEUANGAN', 'STAFF'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add them if rolled back
        $roles = [
            ['name' => 'STAF_TU', 'display_name' => 'Tata Usaha'],
            ['name' => 'KEUANGAN', 'display_name' => 'Keuangan'],
            ['name' => 'STAFF', 'display_name' => 'Pegawai'],
        ];

        foreach ($roles as $role) {
            if (!DB::table('sys_roles')->where('name', $role['name'])->exists()) {
                DB::table('sys_roles')->insert([
                    'id' => Str::uuid(),
                    'name' => $role['name'],
                    'display_name' => $role['display_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::table('sys_roles')->where('name', 'PEGAWAI')->delete();
    }
};
