<?php

use Modules\WaliMurid\Models\WaliMurid;
use Modules\Siswa\Models\Siswa;
use App\Models\User;
use App\Models\Role;
use Modules\Keuangan\Models\UangSaku;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

$siswaIds = [
    "019df194-9863-7156-9b54-9b0f7e663ad4",
    "019df195-74ad-7219-b377-f2e03a645ef0"
];

$userExists = User::where('email', 'walitest@almahir.id')->first();
if ($userExists) {
    echo "User already exists! Email: walitest@almahir.id, Password: password123\n";
    return;
}

$wali = WaliMurid::create([
    'nama' => 'Bapak Test Wali',
    'email' => 'walitest@almahir.id',
    'telepon' => '081234567899',
    'alamat' => 'Jl. Test Dummy No. 123',
    'pekerjaan' => 'Pengusaha',
    'hubungan' => 'Ayah',
]);

foreach($siswaIds as $sId) {
    DB::table('siswa_wali')->insert([
        'id' => (string) Str::uuid(),
        'siswa_id' => $sId,
        'wali_murid_id' => $wali->id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);
}

$role = Role::where('name', 'WALI_MURID')->first();

$user = User::create([
    'name' => 'Bapak Test Wali',
    'email' => 'walitest@almahir.id',
    'password' => Hash::make('password123'),
    'ref_type' => WaliMurid::class,
    'ref_id' => $wali->id,
    'account_status' => 'active'
]);

$user->syncRoles([$role->id]);

foreach($siswaIds as $index => $sId) {
    $siswa = Siswa::find($sId);
    if (!$siswa) continue;
    
    UangSaku::create([
        'siswa_id' => $sId,
        'kelas_id' => $siswa->kelas_id,
        'jumlah' => 100000 + ($index * 50000),
        'tanggal' => Carbon::now()->format('Y-m-d'),
        'status' => 'Belum Diterima Santri',
        'deskripsi' => 'Uang Saku Bulan Ini (' . $siswa->nama . ')',
    ]);
    
    UangSaku::create([
        'siswa_id' => $sId,
        'kelas_id' => $siswa->kelas_id,
        'jumlah' => 200000,
        'tanggal' => Carbon::now()->subDays(5)->format('Y-m-d'),
        'status' => 'Sudah Diterima Santri',
        'deskripsi' => 'Sisa uang saku bulan lalu (' . $siswa->nama . ')',
    ]);
}

echo "Berhasil! Wali Murid Dummy dibuat.\n";
echo "Email: walitest@almahir.id\n";
echo "Password: password123\n";
