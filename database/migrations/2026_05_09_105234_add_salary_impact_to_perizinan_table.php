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
            $table->boolean('potong_gaji')->default(false)->after('status');
            $table->boolean('potong_kuota')->default(false)->after('potong_gaji');
            $table->integer('total_hari')->default(1)->after('potong_kuota');
        });

        // Update existing data: 'cuti' should have potong_kuota = true
        // 'izin' and 'sakit' should have potong_gaji = true (based on user request)
        DB::table('pengajuan_izin_pegawai')->where('jenis_izin', 'cuti')->update([
            'potong_kuota' => true,
            'potong_gaji' => false
        ]);
        
        DB::table('pengajuan_izin_pegawai')->whereIn('jenis_izin', ['izin', 'sakit'])->update([
            'potong_kuota' => false,
            'potong_gaji' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_izin_pegawai', function (Blueprint $table) {
            $table->dropColumn(['potong_gaji', 'potong_kuota', 'total_hari']);
        });
    }
};
