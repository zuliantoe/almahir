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
        Schema::disableForeignKeyConstraints();

        // 1. Remove guru_id from kelas
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'guru_id')) {
                // Drop index if it exists to avoid SQLite error
                try {
                    $table->dropIndex('kelas_guru_id_index');
                } catch (\Exception $e) {
                    // Ignore if index doesn't exist
                }
                $table->dropColumn('guru_id');
            }
        });

        // 2. Rename wali_kelas_id to guru_id in rombel
        Schema::table('rombel', function (Blueprint $table) {
            if (Schema::hasColumn('rombel', 'wali_kelas_id')) {
                $table->renameColumn('wali_kelas_id', 'guru_id');
            } elseif (!Schema::hasColumn('rombel', 'guru_id')) {
                $table->uuid('guru_id')->nullable()->after('kelas_id');
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('kelas', function (Blueprint $table) {
            $table->uuid('guru_id')->nullable();
        });

        Schema::table('rombel', function (Blueprint $table) {
            if (Schema::hasColumn('rombel', 'guru_id')) {
                $table->renameColumn('guru_id', 'wali_kelas_id');
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
