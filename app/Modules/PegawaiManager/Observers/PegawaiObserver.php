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
                        'user_id'         => $pegawai->user_id,
                        'type_pegawai_id' => $pegawai->type_pegawai_id,
                        'nip'             => $pegawai->nip,
                        'nama'            => $pegawai->nama,
                        'tempat_lahir'    => $pegawai->tempat_lahir,
                        'tanggal_lahir'   => $pegawai->tanggal_lahir,
                        'jenis_kelamin'   => $pegawai->jenis_kelamin,
                        'alamat'          => $pegawai->alamat,
                        'tanggal_masuk'   => $pegawai->tanggal_masuk,
                        'status'          => $pegawai->status ?? 'aktif',
                        'sisa_cuti'       => $pegawai->sisa_cuti ?? 12,
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
