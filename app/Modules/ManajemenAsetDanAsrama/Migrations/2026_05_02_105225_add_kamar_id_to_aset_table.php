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
        Schema::table('aset', function (Blueprint $table) {
            $table->unsignedBigInteger('kamar_id')->nullable()->after('pengadaan_id');
            $table->foreign('kamar_id')->references('id')->on('kamar')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
            $table->dropColumn('kamar_id');
        });
    }
};
