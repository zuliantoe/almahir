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
        Schema::table('tagihan_siswas', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('title', 'judul');
            $table->renameColumn('amount', 'jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_siswas', function (Blueprint $table) {
            // Kembalikan ke nama kolom semula
            $table->renameColumn('judul', 'title');
            $table->renameColumn('jumlah', 'amount');
        });
    }
};
