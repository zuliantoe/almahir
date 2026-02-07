<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_piket', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('bulan');
            $table->tinyInteger('pekan');
            $table->string('hari');
            $table->string('tempat');

            // UUID FK ke siswa
            $table->char('siswa_id', 36);

            $table->foreign('siswa_id')
                ->references('id')
                ->on('siswa')
                ->onDelete('cascade');

            $table->enum('status', ['belum', 'sudah'])
                ->default('belum');

            $table->timestamps();

            $table->unique(['bulan', 'pekan', 'hari', 'tempat', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_piket');
    }
};
