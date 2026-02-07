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
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat','Sabtu']);
            $table->integer('jamke');
            $table->time('jamawal');
            $table->time('jamakhir');
            $table->foreignId('mapel_id')->constrained('mata_pelajaran');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('tahunajaran_id')->constrained('tahun_ajaran');
            $table->bigInteger('guru_id')->index();


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};
