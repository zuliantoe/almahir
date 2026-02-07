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
        Schema::create('pengajuan_izin_pegawai', function (Blueprint $table) {
            $table->id();

            $table->uuid('user_id');

            $table->string('jenis_izin');
            // contoh: izin, sakit, cuti, dinas luar

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->text('alasan')->nullable();
            $table->string('bukti')->nullable();
            // path file (foto/pdf)

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak'
            ])->default('menunggu');

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('pegawai')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin_pegawai');
    }
};
