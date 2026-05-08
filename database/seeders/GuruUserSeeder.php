<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Guru\Models\Guru;

class GuruUserSeeder extends Seeder
{
    /**
     * Buat akun User untuk setiap guru yang belum punya akun.
     */
    public function run(): void
    {
        $gurus = Guru::all();

        if ($gurus->isEmpty()) {
            $this->command->warn('Tidak ada data guru. Jalankan AcademicDummySeeder terlebih dahulu.');
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($gurus as $guru) {
            // Cek apakah akun User sudah ada untuk guru ini (by ref)
            $existingByRef = User::where('ref_type', Guru::class)
                ->where('ref_id', $guru->id)
                ->first();

            if ($existingByRef) {
                $skipped++;
                continue;
            }

            // Buat email berdasarkan email guru, fallback ke NIP
            $email = $guru->email ?: ('guru.' . $guru->nip . '@siakad.local');
 
            // Jika email sudah dipakai user lain, pakai versi NIP
            if (User::where('email', $email)->exists()) {
                $email = 'guru.' . $guru->nip . '@siakad.local';
            }

            // Buat user - gunakan forceFill agar ref_type & ref_id bisa diisi
            $user = new User();
            $user->forceFill([
                'name'           => $guru->nama,
                'email'          => $email,
                'password'       => Hash::make('password'),
                'ref_type'       => Guru::class,
                'ref_id'         => $guru->id,
                'account_status' => 'active',
            ]);
            $user->save();

            $user->assignRole('GURU');
            $created++;

            $this->command->info("✓ {$guru->nama} | {$email} | password: password");
        }

        $this->command->newLine();
        $this->command->info("Selesai! {$created} akun guru baru, {$skipped} sudah ada.");
        $this->command->info('Password default semua akun: password');
    }
}
