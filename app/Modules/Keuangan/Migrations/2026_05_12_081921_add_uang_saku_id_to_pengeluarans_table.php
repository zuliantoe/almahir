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
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->unsignedBigInteger('uang_saku_id')->nullable()->after('id');
            $table->foreign('uang_saku_id')->references('id')->on('uang_sakus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropForeign(['uang_saku_id']);
            $table->dropColumn('uang_saku_id');
        });
    }
};
