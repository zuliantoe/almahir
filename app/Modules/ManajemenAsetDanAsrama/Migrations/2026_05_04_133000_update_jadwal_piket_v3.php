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
        Schema::table('jadwal_piket', function (Blueprint $table) {
            // Ubah kamar_id jadi nullable agar fleksibel
            $table->unsignedBigInteger('kamar_id')->nullable()->change();

            if (!Schema::hasColumn('jadwal_piket', 'lokasi_piket')) {
                $table->string('lokasi_piket')->nullable()->after('kamar_id');
            }
            
            if (!Schema::hasColumn('jadwal_piket', 'shift')) {
                $table->enum('shift', ['pagi', 'sore', 'malam'])->default('pagi')->after('tanggal');
            }
            
            // Tambah unique constraint baru untuk mencegah double assignment di shift yang sama
            // Kita bungkus dalam try-catch karena mungkin index sudah terbuat sebagian
            try {
                $table->unique(['tanggal', 'siswa_id', 'shift'], 'piket_unique_shift');
            } catch (\Exception $e) {
                // Ignore if already exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_piket', function (Blueprint $table) {
            try {
                $table->dropUnique('piket_unique_shift');
            } catch (\Exception $e) {}
            
            $table->unsignedBigInteger('kamar_id')->nullable(false)->change();
            $table->dropColumn(['lokasi_piket', 'shift']);
        });
    }
};
