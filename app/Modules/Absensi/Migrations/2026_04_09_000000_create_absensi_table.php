<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('pegawai_id');
            $table->date('tanggal');
            
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            
            // Status: HADIR, TERLAMBAT, IZIN, SAKIT, ALPHA
            $table->string('status', 20)->default('ALPHA');
            
            $table->string('lat_masuk')->nullable();
            $table->string('long_masuk')->nullable();
            $table->string('lat_pulang')->nullable();
            $table->string('long_pulang')->nullable();
            
            $table->text('keterangan')->nullable();
            
            $table->timestamps();

            $table->foreign('pegawai_id')
                ->references('id')
                ->on('pegawai')
                ->onDelete('cascade');

            // Unique composite: 1 pegawai 1 record per hari
            $table->unique(['pegawai_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
