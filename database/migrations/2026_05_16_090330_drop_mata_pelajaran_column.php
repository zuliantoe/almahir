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
        if (Schema::hasColumn('guru', 'mata_pelajaran')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->dropColumn('mata_pelajaran');
            });
        }
        
        if (Schema::hasColumn('pegawai', 'mata_pelajaran')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->dropColumn('mata_pelajaran');
            });
        }
        
        if (Schema::hasColumn('calon_pegawai', 'mata_pelajaran')) {
            Schema::table('calon_pegawai', function (Blueprint $table) {
                $table->dropColumn('mata_pelajaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->string('mata_pelajaran')->nullable();
        });
        
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('mata_pelajaran')->nullable();
        });
        
        Schema::table('calon_pegawai', function (Blueprint $table) {
            $table->string('mata_pelajaran')->nullable();
        });
    }
};
