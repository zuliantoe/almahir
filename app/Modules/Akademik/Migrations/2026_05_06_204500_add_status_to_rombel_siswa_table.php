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
        if (Schema::hasTable('rombel_siswa')) {
            Schema::table('rombel_siswa', function (Blueprint $table) {
                if (!Schema::hasColumn('rombel_siswa', 'status')) {
                    $table->enum('status', ['aktif', 'lulus', 'naik', 'keluar'])
                        ->default('aktif')
                        ->after('siswa_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rombel_siswa')) {
            Schema::table('rombel_siswa', function (Blueprint $table) {
                if (Schema::hasColumn('rombel_siswa', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
