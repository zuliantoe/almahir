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
        Schema::table('pengajuan_izin_pegawai', function (Blueprint $table) {
            $table->uuid('approved_by')->nullable()->after('status');
            $table->text('keterangan_admin')->nullable()->after('approved_by');
            
            $table->foreign('approved_by')
                ->references('id')
                ->on('sys_users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_izin_pegawai', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'keterangan_admin']);
        });
    }
};
