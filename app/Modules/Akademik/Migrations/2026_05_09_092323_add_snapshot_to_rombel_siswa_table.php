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
            if (!Schema::hasColumn('rombel_siswa', 'tahunajaran_id')) {
                $table->foreignId('tahunajaran_id')->nullable()->constrained('tahun_ajaran')->after('siswa_id')->onDelete('cascade');
            }
            if (!Schema::hasColumn('rombel_siswa', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->after('tahunajaran_id')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rombel_siswa', function (Blueprint $table) {
            if (Schema::hasColumn('rombel_siswa', 'tahunajaran_id')) {
                $table->dropForeign(['tahunajaran_id']);
                $table->dropColumn('tahunajaran_id');
            }
            if (Schema::hasColumn('rombel_siswa', 'kelas_id')) {
                $table->dropForeign(['kelas_id']);
                $table->dropColumn('kelas_id');
            }
        });
    }
};
