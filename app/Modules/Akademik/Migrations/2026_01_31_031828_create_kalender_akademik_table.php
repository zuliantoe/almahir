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
        Schema::create('kalender_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tahunajaran')->constrained('tahun_ajaran');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->text("deksripsi");
            $table->foreignId('id_kegiatan')->constrained('jenis_kegiatan');
            // $table->string('acara');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalender_akademik');
    }
};
