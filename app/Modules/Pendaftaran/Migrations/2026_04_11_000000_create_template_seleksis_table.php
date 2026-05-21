<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_seleksis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('template_seleksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_seleksi_id')->constrained('template_seleksis')->onDelete('cascade');
            $table->string('nama_tes');
            $table->string('metode')->default('offline'); // online/offline
            $table->string('pengampu')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_seleksi_items');
        Schema::dropIfExists('template_seleksis');
    }
};
