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
        if (!Schema::hasColumn('pegawai', 'sisa_cuti')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->integer('sisa_cuti')->default(12)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pegawai', 'sisa_cuti')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->dropColumn('sisa_cuti');
            });
        }
    }
};
