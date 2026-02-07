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
        Schema::create('penilaian_tahfidz', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_kelas');
            $table->date('tanggal');
            $table->string('surat_awal');
            $table->string('surat_akhir');
            $table->integer('ayat_awal');
            $table->integer('ayat_akhir');
            $table->unsignedBigInteger('id_guru');
            $table->integer('nilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_tahfidz');
    }
};
