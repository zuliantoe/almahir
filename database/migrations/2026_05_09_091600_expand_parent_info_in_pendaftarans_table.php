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
        Schema::table('pendaftarans', function (Blueprint $table) {
            // Rename Father's fields for clarity if they exist with old names
            if (Schema::hasColumn('pendaftarans', 'no_hp')) {
                $table->renameColumn('no_hp', 'no_hp_ayah');
            }
            
            // Add Father's Address (split from student address)
            $table->text('alamat_ayah')->nullable()->after('no_hp_ayah');
            
            // Add Mother's Info
            $table->string('nama_ibu')->nullable()->after('alamat_ayah');
            $table->string('pekerjaan_ibu')->nullable()->after('nama_ibu');
            $table->string('no_hp_ibu', 20)->nullable()->after('pekerjaan_ibu');
            $table->text('alamat_ibu')->nullable()->after('no_hp_ibu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->renameColumn('no_hp_ayah', 'no_hp');
            $table->dropColumn(['alamat_ayah', 'nama_ibu', 'pekerjaan_ibu', 'no_hp_ibu', 'alamat_ibu']);
        });
    }
};
