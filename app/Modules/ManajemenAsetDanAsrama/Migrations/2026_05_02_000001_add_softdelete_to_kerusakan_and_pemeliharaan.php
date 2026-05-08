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
        // Tambah soft delete columns ke tabel kerusakan
        Schema::table('kerusakan', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
            $table->string('deleted_by')->nullable()->after('deleted_at');
            $table->text('alasan_hapus')->nullable()->after('deleted_by');
        });

        // Tambah soft delete columns ke tabel pemeliharaan
        Schema::table('pemeliharaan', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
            $table->string('deleted_by')->nullable()->after('deleted_at');
            $table->text('alasan_hapus')->nullable()->after('deleted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerusakan', function (Blueprint $table) {
            $table->dropSoftDeletes();
            if (Schema::hasColumn('kerusakan', 'deleted_by')) {
                $table->dropColumn('deleted_by');
            }
            if (Schema::hasColumn('kerusakan', 'alasan_hapus')) {
                $table->dropColumn('alasan_hapus');
            }
        });

        Schema::table('pemeliharaan', function (Blueprint $table) {
            $table->dropSoftDeletes();
            if (Schema::hasColumn('pemeliharaan', 'deleted_by')) {
                $table->dropColumn('deleted_by');
            }
            if (Schema::hasColumn('pemeliharaan', 'alasan_hapus')) {
                $table->dropColumn('alasan_hapus');
            }
        });
    }
};
