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
        // Disable foreign key checks momentarily to avoid constraint violations during structural changes
        Schema::disableForeignKeyConstraints();

        // 1. Modifikasi Tabel Kelas
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'tahun_ajaran_id')) {
                $table->dropForeign(['tahun_ajaran_id']);
                $table->dropColumn('tahun_ajaran_id');
            }
            if (Schema::hasColumn('kelas', 'wali_kelas_id')) {
                $table->dropColumn('wali_kelas_id');
            }
        });

        // 2. Modifikasi Tabel Rombel
        Schema::table('rombel', function (Blueprint $table) {
            if (Schema::hasColumn('rombel', 'siswa_id')) {
                // Khusus SQLite, kita perlu hapus index secara manual sebelum hapus kolom
                if (config('database.default') === 'sqlite') {
                    $table->dropIndex('rombel_siswa_id_index');
                }
                $table->dropColumn('siswa_id'); // Drop karena rombel to siswa is Many-to-Many via pivot
            }
            if (!Schema::hasColumn('rombel', 'wali_kelas_id')) {
                // Gunakan tipe yang sesuai dengan tabel guru (assuming bigInteger if id, uuid if uuid)
                // Di update sebelumnya kelas wali_kelas_id adalah uuid. We will use uuid.
                $table->uuid('wali_kelas_id')->nullable()->after('kelas_id');
            }
            if (!Schema::hasColumn('rombel', 'nama_rombel')) {
                $table->string('nama_rombel')->nullable()->after('id');
            }
        });

        // 3. Buat Tabel Pivot rombel_siswa
        if (!Schema::hasTable('rombel_siswa')) {
            Schema::create('rombel_siswa', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rombel_id')->constrained('rombel')->onDelete('cascade');
                // Siswa module usually uses UUID based on architecture, but let's use string/uuid to be safe
                $table->uuid('siswa_id')->index(); 
                $table->timestamps();
            });
        }

        // 4. Modifikasi Tabel jadwal_pelajaran
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_pelajaran', 'kelas_id')) {
                $table->dropForeign(['kelas_id']);
                $table->dropColumn('kelas_id');
            }
            if (Schema::hasColumn('jadwal_pelajaran', 'tahunajaran_id')) {
                $table->dropForeign(['tahunajaran_id']);
                $table->dropColumn('tahunajaran_id');
            }
            if (!Schema::hasColumn('jadwal_pelajaran', 'rombel_id')) {
                $table->foreignId('rombel_id')->nullable()->constrained('rombel')->onDelete('cascade')->after('mapel_id');
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip down because this is adjusting to strict ERD implementation.
    }
};
