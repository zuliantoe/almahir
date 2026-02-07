<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_piket', function (Blueprint $table) {
            $table->id(); // id_jadwal otomatis

            // Bulan jadwal (1-12)
            $table->tinyInteger('bulan');

            // Pekan ke berapa dalam bulan (1-5)
            $table->tinyInteger('pekan');

            // Hari (Senin, Selasa, dst)
            $table->string('hari');

            // Tempat piket
            $table->string('tempat');

            // FK ke siswa
            $table->foreignId('siswa_id')
                  ->constrained('siswa')
                  ->onDelete('cascade');

            // Status piket
            $table->enum('status', [
                'belum',
                'sudah'
            ])->default('belum');

            $table->timestamps();

            // Biar siswa gak dobel piket di slot yang sama
            $table->unique(['bulan', 'pekan', 'hari', 'tempat', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_piket');
    }
};
