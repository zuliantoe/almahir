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
        Schema::create('absensi_pegawai', function (Blueprint $table) {
            $table->id();

            $table->uuid('user_id');
            $table->date('tanggal');
            $table->string('hari', 10);

            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();

            $table->enum('status', ['ontime', 'telambat'])->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('pegawai')
                  ->onDelete('cascade');

            // 1 pegawai hanya 1 absensi per hari
            $table->unique(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawai');
    }
};
