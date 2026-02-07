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
        Schema::create('seleksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pendaftaran_id')
                ->constrained('pendaftaran')
                ->cascadeOnDelete();

            $table->string('nama_tes');
            $table->date('tanggal')->nullable();
            $table->time('jam')->nullable();
            $table->string('pengampu')->nullable();

            $table->enum('metode', ['offline', 'online'])->nullable();
            $table->string('lokasi')->nullable();
            $table->string('link')->nullable();

            $table->decimal('nilai', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seleksi');
    }
};
