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
        // Insert WALI_MURID role if it doesn't exist
        $exists = DB::table('sys_roles')->where('name', 'WALI_MURID')->exists();
        
        if (!$exists) {
            DB::table('sys_roles')->insert([
                'id' => Str::uuid(),
                'name' => 'WALI_MURID',
                'display_name' => 'Wali Murid',
                'description' => 'Akses untuk Orang Tua / Wali Murid',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sys_roles')->where('name', 'WALI_MURID')->delete();
    }
};
