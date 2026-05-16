<?php

namespace App\Services;

use Modules\PegawaiManager\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HrAlertService
{
    /**
     * Ambil semua notifikasi HR yang relevan untuk hari ini.
     * Return: Collection of alert arrays with keys: type, icon, color, message, pegawai_id
     */
    public function getAlerts(): Collection
    {
        $alerts = collect();
        $today  = Carbon::today();

        $pegawaiList = Pegawai::with(['user', 'typePegawai'])
            ->where('status', 'aktif')
            ->get();

        foreach ($pegawaiList as $p) {

            // ─────────────────────────────────────────────
            // 1. ULTAH HARI INI / 7 HARI KE DEPAN
            // ─────────────────────────────────────────────
            if ($p->tanggal_lahir) {
                try {
                    $tl = Carbon::parse($p->tanggal_lahir);
                    $ultahTahunIni = $tl->copy()->year($today->year);

                    $selisihHari = $today->diffInDays($ultahTahunIni, false);

                    if ($selisihHari === 0) {
                        $alerts->push([
                            'type'       => 'birthday_today',
                            'icon'       => 'fa-birthday-cake',
                            'color'      => '#f72585',
                            'bg'         => '#fff0f7',
                            'label'      => 'Ulang Tahun Hari Ini 🎂',
                            'message'    => "<b>{$p->nama}</b> berulang tahun hari ini! Jangan lupa ucapkan selamat. 🎉",
                            'pegawai_id' => $p->id,
                        ]);
                    } elseif ($selisihHari > 0 && $selisihHari <= 7) {
                        $alerts->push([
                            'type'       => 'birthday_soon',
                            'icon'       => 'fa-gift',
                            'color'      => '#b5179e',
                            'bg'         => '#fdf0ff',
                            'label'      => "Ulang Tahun {$selisihHari} Hari Lagi 🎁",
                            'message'    => "<b>{$p->nama}</b> akan berulang tahun pada <b>" . $ultahTahunIni->translatedFormat('d F Y') . "</b>.",
                            'pegawai_id' => $p->id,
                        ]);
                    }
                } catch (\Exception $e) {}
            }

            // ─────────────────────────────────────────────
            // 2. ANNIVERSARI KERJA (Tepat / 7 Hari ke Depan)
            // ─────────────────────────────────────────────
            if ($p->tanggal_masuk) {
                $tm = $p->tanggal_masuk; // already cast to Carbon
                $tahunKerja = $today->year - $tm->year;

                if ($tahunKerja > 0) {
                    $anniversaryTahunIni = $tm->copy()->year($today->year);
                    $selisih = $today->diffInDays($anniversaryTahunIni, false);

                    if ($selisih === 0) {
                        $alerts->push([
                            'type'       => 'anniversary_today',
                            'icon'       => 'fa-star',
                            'color'      => '#ffd60a',
                            'bg'         => '#fffbea',
                            'label'      => "Anniversari Kerja ke-{$tahunKerja} Tahun ⭐",
                            'message'    => "<b>{$p->nama}</b> hari ini genap <b>{$tahunKerja} tahun</b> bergabung. Apresiasi pencapaiannya!",
                            'pegawai_id' => $p->id,
                        ]);
                    } elseif ($selisih > 0 && $selisih <= 7) {
                        $alerts->push([
                            'type'       => 'anniversary_soon',
                            'icon'       => 'fa-medal',
                            'color'      => '#fb8500',
                            'bg'         => '#fff5ea',
                            'label'      => "Anniversari Kerja {$selisih} Hari Lagi 🏅",
                            'message'    => "<b>{$p->nama}</b> akan genap <b>{$tahunKerja} tahun</b> bekerja pada <b>" . $anniversaryTahunIni->translatedFormat('d F Y') . "</b>.",
                            'pegawai_id' => $p->id,
                        ]);
                    }
                }
            }

            // ─────────────────────────────────────────────
            // 3. MASA PENSIUN (Jika umur >= 58 tahun dalam 30 hari ke depan)
            // ─────────────────────────────────────────────
            if ($p->tanggal_lahir) {
                try {
                    $tl  = Carbon::parse($p->tanggal_lahir);
                    $umur = $today->diffInYears($tl);

                    if ($umur >= 57) {
                        $pensiun = $tl->copy()->addYears(58);
                        $hariMenujuPensiun = $today->diffInDays($pensiun, false);

                        if ($hariMenujuPensiun >= 0 && $hariMenujuPensiun <= 90) {
                            $alerts->push([
                                'type'       => 'retirement_soon',
                                'icon'       => 'fa-user-clock',
                                'color'      => '#ef233c',
                                'bg'         => '#fff0f0',
                                'label'      => "Mendekati Masa Pensiun ⚠️",
                                'message'    => "<b>{$p->nama}</b> akan memasuki usia pensiun (58 th) dalam <b>{$hariMenujuPensiun} hari</b> lagi.",
                                'pegawai_id' => $p->id,
                            ]);
                        } elseif ($hariMenujuPensiun < 0 && $hariMenujuPensiun >= -30) {
                            $alerts->push([
                                'type'       => 'retirement_overdue',
                                'icon'       => 'fa-exclamation-triangle',
                                'color'      => '#d62828',
                                'bg'         => '#ffe5e5',
                                'label'      => "Sudah Melewati Usia Pensiun ❗",
                                'message'    => "<b>{$p->nama}</b> sudah melewati usia pensiun. Segera proses perubahan status kepegawaian.",
                                'pegawai_id' => $p->id,
                            ]);
                        }
                    }
                } catch (\Exception $e) {}
            }
        }

        return $alerts;
    }
}
