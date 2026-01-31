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
        Schema::create('wali_murid', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->enum('hubungan', ['ayah', 'ibu', 'wali'])->default('wali');
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table: siswa <-> wali_murid (many-to-many)
        Schema::create('siswa_wali', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('siswa_id');
            $table->uuid('wali_murid_id');
            $table->enum('hubungan', ['ayah', 'ibu', 'wali'])->default('wali');
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('wali_murid_id')->references('id')->on('wali_murid')->onDelete('cascade');
            $table->unique(['siswa_id', 'wali_murid_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_wali');
        Schema::dropIfExists('wali_murid');
    }
};
