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
        // Add PEGAWAI role
        $exists = DB::table('sys_roles')->where('name', 'PEGAWAI')->exists();
        if (!$exists) {
            DB::table('sys_roles')->insert([
                'id' => Str::uuid(),
                'name' => 'PEGAWAI',
                'display_name' => 'Pegawai',
                'description' => 'Akses untuk Staf / Pegawai (TU, Keuangan, dll)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Optional: The user might want to transition STAF_TU, KEUANGAN, STAFF users to PEGAWAI
        // but for now we just add the role.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sys_roles')->where('name', 'PEGAWAI')->delete();
    }
};
