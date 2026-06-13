<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. STANDARDIZASI TABEL PEGAWAI
        Schema::table('pegawai', function (Blueprint $table) {
            // NOTE (SQLite): dropping columns on sqlite can fail if constraints/indexes
            // (e.g. unique index) still reference the dropped column.
            // To keep migrations reliable, we DO NOT drop columns on SQLite.
            // Instead, we only add missing columns.
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';

            $columns = collect(DB::select("PRAGMA table_info(pegawai)"))->pluck('name')->all();

            if (!$isSqlite) {
                // Drop redundant fields (already in sys_users)
                if (in_array('email', $columns, true)) {
                    $table->dropColumn('email');
                }
                if (in_array('no_hp', $columns, true)) {
                    $table->dropColumn('no_hp');
                }
                if (in_array('foto', $columns, true)) {
                    $table->dropColumn('foto');
                }
            }

            // Add missing HR fields (dari tabel guru)
            if (!in_array('nip', $columns, true)) {
                $table->string('nip', 30)->nullable()->unique()->after('type_pegawai_id');
            }
            if (!in_array('tempat_lahir', $columns, true)) {
                $table->string('tempat_lahir')->nullable()->after('nama');
            }
            if (!in_array('tanggal_lahir', $columns, true)) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!in_array('jenis_kelamin', $columns, true)) {
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            }
            if (!in_array('mata_pelajaran', $columns, true)) {
                $table->string('mata_pelajaran')->nullable()->after('tanggal_masuk');
            }
            if (!in_array('status', $columns, true)) {
                $table->enum('status', ['aktif', 'nonaktif', 'pensiun'])->default('aktif')->after('mata_pelajaran');
            }
        });

        // 2. STANDARDIZASI TABEL GURU
        Schema::table('guru', function (Blueprint $table) {
            // Drop redundant fields
            $table->dropColumn(['email', 'telepon', 'foto', 'jabatan']);

            // Add missing fields (dari tabel pegawai)
            $table->uuid('user_id')->nullable()->unique()->after('id');
            $table->uuid('type_pegawai_id')->nullable()->after('nama');
            $table->date('tanggal_masuk')->nullable()->after('alamat');
            $table->integer('sisa_cuti')->default(12)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ROLLBACK PEGAWAI
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('foto')->nullable();

            $table->dropColumn([
                'nip', 
                'tempat_lahir', 
                'tanggal_lahir', 
                'jenis_kelamin', 
                'mata_pelajaran', 
                'status'
            ]);
        });

        // ROLLBACK GURU
        Schema::table('guru', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('foto')->nullable();
            $table->string('jabatan')->nullable();

            $table->dropColumn([
                'user_id',
                'type_pegawai_id',
                'tanggal_masuk',
                'sisa_cuti'
            ]);
        });
    }
};
