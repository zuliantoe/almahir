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
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            if (Schema::hasColumn('tahun_ajaran', 'semester')) {
                $table->dropColumn('semester');
            }
        });

        Schema::table('kalender_akademik', function (Blueprint $table) {
            if (!Schema::hasColumn('kalender_akademik', 'semester')) {
                $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil')->after('tahunajaran_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            if (!Schema::hasColumn('tahun_ajaran', 'semester')) {
                $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil')->after('tahunajaran');
            }
        });

        Schema::table('kalender_akademik', function (Blueprint $table) {
            if (Schema::hasColumn('kalender_akademik', 'semester')) {
                $table->dropColumn('semester');
            }
        });
    }
};
