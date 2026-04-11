<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sinkronisasi schema agar sesuai dengan controller yang sudah ada.
     */
    public function up(): void
    {
        // 1. Tambah kolom pada tabel kerusakan
        Schema::table('kerusakan', function (Blueprint $table) {
            if (!Schema::hasColumn('kerusakan', 'tanggal_kerusakan')) {
                $table->date('tanggal_kerusakan')->nullable()->after('aset_id');
            }
            if (!Schema::hasColumn('kerusakan', 'tingkat_kerusakan')) {
                $table->enum('tingkat_kerusakan', ['ringan', 'sedang', 'berat'])->default('ringan')->after('deskripsi_kerusakan');
            }
            if (!Schema::hasColumn('kerusakan', 'status_penanganan')) {
                $table->enum('status_penanganan', ['belum_ditangani', 'sedang_ditangani', 'selesai'])->default('belum_ditangani')->after('tingkat_kerusakan');
            }
            if (!Schema::hasColumn('kerusakan', 'catatan')) {
                $table->text('catatan')->nullable()->after('status_penanganan');
            }
        });

        // 2. Tambah kolom pada tabel kamar_penghuni (saat ini kosong)
        // siswa.id bertipe char (UUID), jadi pakai char juga tanpa constraint foreign key
        Schema::table('kamar_penghuni', function (Blueprint $table) {
            if (!Schema::hasColumn('kamar_penghuni', 'kamar_id')) {
                $table->foreignId('kamar_id')->nullable()->constrained('kamar')->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('kamar_penghuni', 'siswa_id')) {
                $table->char('siswa_id', 36)->nullable()->after('kamar_id');
            }
            if (!Schema::hasColumn('kamar_penghuni', 'tanggal_masuk')) {
                $table->date('tanggal_masuk')->nullable()->after('siswa_id');
            }
            if (!Schema::hasColumn('kamar_penghuni', 'tanggal_keluar')) {
                $table->date('tanggal_keluar')->nullable()->after('tanggal_masuk');
            }
            if (!Schema::hasColumn('kamar_penghuni', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('tanggal_keluar');
            }
        });

        // 3. Tambah kolom pada tabel pemeliharaan (controller pakai field berbeda)
        Schema::table('pemeliharaan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemeliharaan', 'tanggal_pemeliharaan')) {
                $table->date('tanggal_pemeliharaan')->nullable()->after('aset_id');
            }
            if (!Schema::hasColumn('pemeliharaan', 'biaya')) {
                $table->decimal('biaya', 15, 2)->default(0)->after('deskripsi_pemeliharaan');
            }
            if (!Schema::hasColumn('pemeliharaan', 'catatan')) {
                $table->text('catatan')->nullable()->after('biaya');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerusakan', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kerusakan', 'tingkat_kerusakan', 'status_penanganan', 'catatan']);
        });

        Schema::table('kamar_penghuni', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
            $table->dropColumn(['kamar_id', 'siswa_id', 'tanggal_masuk', 'tanggal_keluar', 'keterangan']);
        });

        Schema::table('pemeliharaan', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pemeliharaan', 'biaya', 'catatan']);
        });
    }
};
