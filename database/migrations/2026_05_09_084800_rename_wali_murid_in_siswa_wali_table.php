<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa_wali', function (Blueprint $table) {
            if (Schema::hasColumn('siswa_wali', 'wali_murid')) {
                $table->renameColumn('wali_murid', 'wali_murid_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_wali', function (Blueprint $table) {
            $table->renameColumn('wali_murid_id', 'wali_murid');
        });
    }
};
