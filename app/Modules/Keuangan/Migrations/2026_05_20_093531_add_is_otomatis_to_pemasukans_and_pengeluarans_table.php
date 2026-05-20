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
        Schema::table('pemasukans', function (Blueprint $table) {
            $table->boolean('is_otomatis')->default(false)->after('deskripsi');
        });

        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->boolean('is_otomatis')->default(false)->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemasukans', function (Blueprint $table) {
            $table->dropColumn('is_otomatis');
        });

        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropColumn('is_otomatis');
        });
    }
};
