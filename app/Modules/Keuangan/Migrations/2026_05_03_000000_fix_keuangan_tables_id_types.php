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
        // Fix uang_sakus table
        if (Schema::hasTable('uang_sakus')) {
            Schema::table('uang_sakus', function (Blueprint $table) {
                $table->uuid('siswa_id')->change();
            });
        }

        // Fix tagihan_siswas table (morphs)
        if (Schema::hasTable('tagihan_siswas')) {
            Schema::table('tagihan_siswas', function (Blueprint $table) {
                $table->string('target_id')->change(); // Change bigint to string/uuid
            });
        }

        // Fix pembayaran_siswas table
        if (Schema::hasTable('pembayaran_siswas')) {
            Schema::table('pembayaran_siswas', function (Blueprint $table) {
                $table->uuid('siswa_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to go back to bigint if UUIDs are present, 
        // but we can try to revert for completeness if needed.
    }
};
