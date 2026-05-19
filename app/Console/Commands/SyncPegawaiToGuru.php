<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\Guru\Models\Guru;

class SyncPegawaiToGuru extends Command
{
    protected $signature = 'sync:pegawai-guru';
    protected $description = 'Sync existing Pegawai with GURU role to guru table';

    public function handle()
    {
        $this->info('Starting sync...');
        
        $pegawais = Pegawai::whereHas('user', function($q) {
            $q->whereHas('roles', function($rq) {
                $rq->where('name', 'GURU');
            });
        })->get();

        $bar = $this->output->createProgressBar(count($pegawais));

        foreach ($pegawais as $pegawai) {
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
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sync completed! ' . count($pegawais) . ' records processed.');
    }
}
