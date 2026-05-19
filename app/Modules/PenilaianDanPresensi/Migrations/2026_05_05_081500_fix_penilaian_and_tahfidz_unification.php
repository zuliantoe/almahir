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
        // 1. Fix Penilaian Table
        Schema::table('penilaian', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian', 'tahunajaran_id')) {
                $table->unsignedBigInteger('tahunajaran_id')->nullable()->after('id_tahun_ajaran');
            }
            if (!Schema::hasColumn('penilaian', 'semester')) {
                $table->string('semester', 20)->nullable()->after('tahunajaran_id');
            }
            if (!Schema::hasColumn('penilaian', 'author_id')) {
                $table->uuid('author_id')->nullable()->after('semester');
            }
            if (!Schema::hasColumn('penilaian', 'kkm')) {
                $table->integer('kkm')->default(75)->after('nilai');
            }
            // Add foreign key if possible (SQLite has limitations but Laravel handles it)
            // $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
        });

        // 2. Fix Penilaian Tahfidz Table
        Schema::table('penilaian_tahfidz', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian_tahfidz', 'tahunajaran_id')) {
                $table->unsignedBigInteger('tahunajaran_id')->nullable()->after('id_guru');
            }
            if (!Schema::hasColumn('penilaian_tahfidz', 'semester')) {
                $table->string('semester', 20)->nullable()->after('tahunajaran_id');
            }
            if (!Schema::hasColumn('penilaian_tahfidz', 'author_id')) {
                $table->uuid('author_id')->nullable()->after('semester');
            }
            if (!Schema::hasColumn('penilaian_tahfidz', 'status_capaian')) {
                $table->string('status_capaian')->nullable()->after('nilai');
            }
        });

        // 3. Fix Presensi Table (Ensure consistency)
        Schema::table('presensi', function (Blueprint $table) {
            if (!Schema::hasColumn('presensi', 'tahunajaran_id')) {
                $table->unsignedBigInteger('tahunajaran_id')->nullable()->after('id_jadwal_pelajaran');
            }
            if (!Schema::hasColumn('presensi', 'semester')) {
                $table->string('semester', 20)->nullable()->after('tahunajaran_id');
            }
            if (!Schema::hasColumn('presensi', 'author_id')) {
                $table->uuid('author_id')->nullable()->after('semester');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropColumn(['tahunajaran_id', 'semester', 'author_id', 'kkm']);
        });
        
        Schema::table('penilaian_tahfidz', function (Blueprint $table) {
            $table->dropColumn(['tahunajaran_id', 'semester', 'author_id', 'status_capaian']);
        });

        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn(['tahunajaran_id', 'semester', 'author_id']);
        });
    }
};
