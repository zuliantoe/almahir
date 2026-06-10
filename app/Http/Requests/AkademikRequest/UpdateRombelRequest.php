<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRombelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_rombel' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'keterangan' => 'nullable|string',
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $tahunAjaranId = $this->input('tahunajaran_id');
            $guruId = $this->input('guru_id');
            $siswaIds = $this->input('siswa_ids', []);
            $currentRombel = $this->route('rombel');
            $currentId = $currentRombel ? $currentRombel->id : null;

            if ($tahunAjaranId) {
                // 1. Validasi Guru (Wali Kelas)
                $isWaliKelas = \App\Modules\Akademik\Models\Rombel::where('tahunajaran_id', $tahunAjaranId)
                    ->where('guru_id', $guruId)
                    ->when($currentId, function ($query, $currentId) {
                        return $query->where('id', '!=', $currentId);
                    })
                    ->exists();
                
                if ($isWaliKelas) {
                    $validator->errors()->add('guru_id', 'Guru ini sudah menjadi Wali Kelas di Rombel lain pada tahun ajaran yang sama.');
                }

                // 2. Validasi Siswa (Double Enrollment)
                if (!empty($siswaIds)) {
                    $siswaSudahAdaKelas = \App\Modules\Akademik\Models\RombelSiswa::whereIn('siswa_id', $siswaIds)
                        ->whereHas('rombel', function($q) use ($tahunAjaranId, $currentId) {
                            $q->where('tahunajaran_id', $tahunAjaranId);
                            if ($currentId) {
                                $q->where('id', '!=', $currentId);
                            }
                        })->with(['siswa', 'rombel'])->get();

                    if ($siswaSudahAdaKelas->count() > 0) {
                        foreach ($siswaSudahAdaKelas as $rs) {
                            $validator->errors()->add('siswa_ids', "Siswa {$rs->siswa->nama} sudah terdaftar di Rombel {$rs->rombel->nama_rombel} pada tahun ajaran ini.");
                        }
                    }
                }

                // 3. Validasi Kelas (1 Kelas hanya untuk 1 Rombel per Tahun Ajaran)
                $kelasId = $this->input('kelas_id');
                if ($kelasId) {
                    $kelasDipakai = \App\Modules\Akademik\Models\Rombel::where('tahunajaran_id', $tahunAjaranId)
                        ->where('kelas_id', $kelasId)
                        ->when($currentId, function ($query, $currentId) {
                            return $query->where('id', '!=', $currentId);
                        })
                        ->exists();

                    if ($kelasDipakai) {
                        $validator->errors()->add('kelas_id', 'Kelas ini sudah digunakan oleh Rombel lain pada tahun ajaran yang sama.');
                    }

                    // 4. Validasi Kapasitas Kelas terhadap Jumlah Siswa Terpilih
                    $kelas = \App\Modules\Akademik\Models\Kelas::find($kelasId);
                    if ($kelas && $kelas->kapasitas && count($siswaIds) > $kelas->kapasitas) {
                        $validator->errors()->add('siswa_ids', "Jumlah siswa terpilih (" . count($siswaIds) . ") melebihi kapasitas maksimal kelas ini ({$kelas->kapasitas} siswa).");
                    }
                }
            }
        });
    }
}
