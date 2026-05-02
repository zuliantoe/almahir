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
        Schema::table('absensi', function (Blueprint $table) {
            // Composite index untuk pencarian efisien berdasarkan tanggal dan relasi pegawai
            $table->index(['tanggal', 'pegawai_id'], 'abs_tanggal_pegawai_index');
            
            // Index individual untuk kolom pencarian yang sering dipakai
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropIndex('abs_tanggal_pegawai_index');
            $table->dropIndex(['status']);
        });
    }
};
