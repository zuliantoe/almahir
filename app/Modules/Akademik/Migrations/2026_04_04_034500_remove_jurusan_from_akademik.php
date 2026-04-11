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

        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'jurusan_id')) {
                // Drop foreign key if exists
                try {
                    $table->dropForeign(['jurusan_id']);
                } catch (\Exception $e) {
                    // It's okay if not explicitly foreign
                }
                $table->dropColumn('jurusan_id');
            }
        });

        Schema::dropIfExists('jurusan');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
