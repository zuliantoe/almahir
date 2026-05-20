<?php

namespace Modules\PenilaianDanPresensi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Models
use App\Models\User;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Tingkat;
use App\Modules\Akademik\Models\KategoriPelajaran;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\JenisKegiatan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\RombelSiswa;
use App\Modules\Akademik\Models\JadwalPelajaran;

use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use Modules\PenilaianDanPresensi\Models\PenilaianAkademik;
use Modules\PenilaianDanPresensi\Models\Presensi;

class PenilaianIntegrasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder INTEGRATES Penilaian and Presensi with existing data from
     * Akademik, Siswa, and Guru modules.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->command->info('Integrating Penilaian and Presensi with existing Academic data...');

        // 1. Get Base Data
        $tahun = TahunAjaran::whereIn('status', [1, 'aktif'])->first() ?: TahunAjaran::latest()->first();
        if (!$tahun) {
            $this->command->error('Tahun Ajaran not found! Run Academic seeders first.');
            return;
        }

        $siswas = Siswa::all();
        if ($siswas->isEmpty()) {
            $this->command->error('No students found! Run SiswaDataSeeder first.');
            return;
        }

        $gurus = Guru::all();
        if ($gurus->isEmpty()) {
            $this->command->error('No gurus found! Run GuruDataSeeder first.');
            return;
        }

        // 2. Generate Assessment Data (Penilaian Akademik)
        $this->command->info('Generating Penilaian Akademik (Harian, UTS, UAS)...');
        $jenisNilais = ['Harian', 'UTS', 'UAS'];
        
        foreach ($siswas as $siswa) {
            // Find the student's current Rombel
            $rombelSiswa = RombelSiswa::where('siswa_id', $siswa->id)->first();
            if (!$rombelSiswa) continue;

            $rombel = Rombel::find($rombelSiswa->rombel_id);
            if (!$rombel) continue;

            // Get subjects for this rombel from JadwalPelajaran
            $mapelIds = JadwalPelajaran::where('rombel_id', $rombel->id)
                ->pluck('mapel_id')
                ->unique();

            foreach ($mapelIds as $mapelId) {
                // Determine who the teacher is for this subject in this rombel
                $jadwal = JadwalPelajaran::where('rombel_id', $rombel->id)
                    ->where('mapel_id', $mapelId)
                    ->first();
                
                $guruId = $jadwal ? $jadwal->guru_id : $gurus->random()->id;

                foreach ($jenisNilais as $jenis) {
                    PenilaianAkademik::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'mapel_id' => $mapelId,
                            'tahunajaran_id' => $tahun->id,
                            'jenis_nilai' => $jenis,
                        ],
                        [
                            'guru_id' => $guruId,
                            'nilai' => rand(70, 98),
                            'kkm' => 75,
                            'semester' => $tahun->semester,
                            'author_id' => 1, // Admin
                        ]
                    );
                }
            }
        }

        // 3. Generate Attendance Data (Presensi)
        $this->command->info('Generating Presensi for the current week...');
        
        // For the last 5 days (Mon-Fri)
        $today = now();
        for ($i = 0; $i < 5; $i++) {
            $date = clone $today;
            $date->subDays($i);
            
            // Skip weekends
            if ($date->isWeekend()) continue;

            $dayNum = $date->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
            
            // Fetch schedules for this day
            $jadwals = JadwalPelajaran::where('hari', $dayNum)->get();
            
            foreach ($jadwals as $jadwal) {
                // Find students in this rombel
                $siswaIds = RombelSiswa::where('rombel_id', $jadwal->rombel_id)->pluck('siswa_id');
                
                foreach ($siswaIds as $siswaId) {
                    $status = rand(1, 20) === 1 ? 'Izin' : (rand(1, 30) === 1 ? 'Sakit' : 'Hadir');
                    
                    Presensi::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'jadwal_pelajaran_id' => $jadwal->id,
                            'created_at' => $date->format('Y-m-d') . ' ' . $jadwal->jamawal,
                        ],
                        [
                            'guru_id' => $jadwal->guru_id,
                            'mapel_id' => $jadwal->mapel_id,
                            'tahunajaran_id' => $tahun->id,
                            'semester' => $tahun->semester,
                            'jam' => substr($jadwal->jamawal, 0, 5),
                            'status' => $status,
                            'kategori' => $status,
                            'author_id' => 1,
                        ]
                    );
                }
            }
        }

        Schema::enableForeignKeyConstraints();
        $this->command->info('Successfully integrated Penilaian and Presensi modules with existing Academic data.');
    }
}
