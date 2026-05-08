<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Presensi
        if (Schema::hasTable('presensi')) {
            if (!Schema::hasColumn('presensi', 'tahunajaran_id')) {
                DB::statement('ALTER TABLE presensi ADD COLUMN tahunajaran_id INTEGER NULL');
            }
            if (!Schema::hasColumn('presensi', 'semester')) {
                DB::statement('ALTER TABLE presensi ADD COLUMN semester VARCHAR(20) NULL');
            }
            if (!Schema::hasColumn('presensi', 'author_id')) {
                DB::statement('ALTER TABLE presensi ADD COLUMN author_id INTEGER NULL');
            }
            
            // Renaming via DB statement is NOT supported in SQLite (only since 3.25.0)
            // But we can try it or just use the old names in models.
        }

        // ... same for others ...
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
