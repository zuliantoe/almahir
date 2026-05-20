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
        Schema::create('pencatatan_otomatis', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->unsignedBigInteger('sumber_id')->nullable();
            $table->unsignedBigInteger('tujuan_id')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->text('deskripsi')->nullable();
            
            // Scheduling logic
            $table->enum('frekuensi', ['sekali', 'harian', 'bulanan'])->default('sekali');
            $table->date('tanggal_mulai');
            $table->time('waktu_eksekusi');
            $table->dateTime('last_run_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            $table->foreign('sumber_id')->references('id')->on('sumbers')->onDelete('set null');
            $table->foreign('tujuan_id')->references('id')->on('tujuans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencatatan_otomatis');
    }
};
