<?php

namespace Modules\PegawaiManager\Observers;

use Modules\PegawaiManager\Models\Pegawai;
use Modules\Guru\Models\Guru;
use Illuminate\Support\Facades\Log;

class PegawaiObserver
{
    /**
     * Handle the Pegawai "saved" event.
     * This will sync data to the guru table if the pegawai has the GURU role.
     */
    public function saved(Pegawai $pegawai): void
    {
        $user = $pegawai->user;

        if ($user && $user->hasRole('GURU')) {
            try {
                Guru::updateOrCreate(
                    ['id' => $pegawai->id],
                    [
                        'nama'   => $pegawai->nama,
                        'email'  => $pegawai->email,
                        'telepon'=> $pegawai->no_hp,
                        'alamat' => $pegawai->alamat,
                        'foto'   => $pegawai->foto,
                        'status' => 'aktif',
                    ]
                );
            } catch (\Exception $e) {
                Log::error("Failed to sync Pegawai to Guru: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Pegawai "deleted" event.
     */
    public function deleted(Pegawai $pegawai): void
    {
        $guru = Guru::find($pegawai->id);
        if ($guru) {
            $guru->delete();
        }
    }

    /**
     * Handle the Pegawai "restored" event.
     */
    public function restored(Pegawai $pegawai): void
    {
        $guru = Guru::onlyTrashed()->find($pegawai->id);
        if ($guru) {
            $guru->restore();
        }
    }
}
