<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the table if it already exists to avoid duplicate creation errors
        if (Schema::hasTable('pengadaan_aset')) {
            Schema::dropIfExists('pengadaan_aset');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed – the original create migration will recreate the table when rolled back
    }
};
