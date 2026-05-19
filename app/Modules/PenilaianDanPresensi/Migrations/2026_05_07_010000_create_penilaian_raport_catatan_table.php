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
        Schema::create('penilaian_raport_catatan', function (Blueprint $table) {
            $table->id();
            $table->uuid('siswa_id');
            $table->unsignedBigInteger('tahunajaran_id');
            $table->text('catatan')->nullable();
            $table->text('catatan_tahfidz')->nullable();
            $table->string('semester', 20)->nullable();
            $table->uuid('author_id')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('tahunajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_raport_catatan');
    }
};
