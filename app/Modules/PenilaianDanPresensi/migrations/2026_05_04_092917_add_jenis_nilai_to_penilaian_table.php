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
        Schema::table('penilaian', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian', 'jenis_nilai')) {
                $table->string('jenis_nilai')->nullable()->after('id_tahun_ajaran')->comment('Harian, UTS, UAS');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropColumn('jenis_nilai');
        });
    }
};
