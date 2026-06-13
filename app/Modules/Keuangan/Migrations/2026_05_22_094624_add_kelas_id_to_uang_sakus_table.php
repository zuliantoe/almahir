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
        Schema::table('uang_sakus', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable()->after('siswa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_sakus', function (Blueprint $table) {
            $table->dropColumn('kelas_id');
        });
    }
};
