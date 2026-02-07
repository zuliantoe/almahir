<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kalender_akademik', function (Blueprint $table) {
            $table->renameColumn('deksripsi', 'deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('kalender_akademik', function (Blueprint $table) {
            $table->renameColumn('deskripsi', 'deskirpsi');
        });
    }
};
