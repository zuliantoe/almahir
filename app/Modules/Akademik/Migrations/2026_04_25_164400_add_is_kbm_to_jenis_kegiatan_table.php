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
        Schema::table('jenis_kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('jenis_kegiatan', 'is_kbm')) {
                $table->boolean('is_kbm')->default(true)->after('jeniskegiatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('jenis_kegiatan', 'is_kbm')) {
                $table->dropColumn('is_kbm');
            }
        });
    }
};
