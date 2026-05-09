<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Fixing izin_sakit table...\n";

Schema::disableForeignKeyConstraints();
Schema::dropIfExists('izin_sakit');

Schema::create('izin_sakit', function (Blueprint $table) {
    $table->id();
    $table->uuid('id_siswa');
    $table->unsignedBigInteger('id_kelas');
    $table->unsignedBigInteger('id_mapel')->nullable();
    $table->unsignedBigInteger('id_jadwal_pelajaran')->nullable();
    
    $table->string('jenis');
    $table->string('tipe_izin')->default('Harian');
    
    $table->date('tgl_mulai');
    $table->date('tgl_selesai');
    
    $table->text('keterangan')->nullable();
    $table->string('bukti_foto')->nullable();
    
    $table->string('status')->default('Pending');
    $table->uuid('konfirmasi_oleh')->nullable();
    $table->timestamp('waktu_konfirmasi')->nullable();
    
    $table->unsignedBigInteger('tahunajaran_id')->nullable();
    $table->string('semester')->nullable();
    $table->uuid('author_id')->nullable();
    
    $table->timestamps();

    // No strict foreign keys for now to avoid the constraint error during testing
    // or point them to the correct tables if they exist
    $table->foreign('author_id')->references('id')->on('sys_users')->onDelete('set null');
});

Schema::enableForeignKeyConstraints();

echo "Table izin_sakit recreated successfully.\n";
