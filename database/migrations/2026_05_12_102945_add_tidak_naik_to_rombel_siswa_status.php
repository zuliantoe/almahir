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
        Schema::table('rombel_siswa', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus', 'naik', 'keluar', 'tidak_naik'])
                  ->default('aktif')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rombel_siswa', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus', 'naik', 'keluar'])
                  ->default('aktif')
                  ->change();
        });
    }
};
