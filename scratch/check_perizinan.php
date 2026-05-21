<?php
// Cek struktur data di tabel pengajuan_izin_pegawai
$perizinan = Illuminate\Support\Facades\DB::table('pengajuan_izin_pegawai')->first();
if ($perizinan) {
    echo "Contoh data perizinan:\n";
    echo "  user_id: " . $perizinan->user_id . "\n";
    // Cek apakah user_id cocok dengan pegawai.id atau sys_users.id
    $matchPegawai = \Modules\PegawaiManager\Models\Pegawai::find($perizinan->user_id);
    $matchUser = App\Models\User::find($perizinan->user_id);
    echo "  Cocok dengan pegawai.id? " . ($matchPegawai ? "YA — Nama: {$matchPegawai->nama}" : "TIDAK") . "\n";
    echo "  Cocok dengan sys_users.id? " . ($matchUser ? "YA — Email: {$matchUser->email}" : "TIDAK") . "\n";
} else {
    echo "Tabel pengajuan_izin_pegawai kosong.\n";
}

// Cek data absensi juga
$absensi = Illuminate\Support\Facades\DB::table('absensi')->first();
if ($absensi) {
    echo "\nContoh data absensi:\n";
    echo "  pegawai_id: " . $absensi->pegawai_id . "\n";
    $matchPegawai = \Modules\PegawaiManager\Models\Pegawai::find($absensi->pegawai_id);
    echo "  Cocok dengan pegawai.id? " . ($matchPegawai ? "YA — Nama: {$matchPegawai->nama}" : "TIDAK") . "\n";
} else {
    echo "\nTabel absensi kosong.\n";
}
