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
        Schema::table('pengadaan_aset', function (Blueprint $blueprint) {
            $blueprint->softDeletes();
            // Tambahin juga deleted_by buat tracking siapa yang hapus (standard kita)
            $blueprint->uuid('deleted_by')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengadaan_aset', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['deleted_at', 'deleted_by']);
        });
    }
};
