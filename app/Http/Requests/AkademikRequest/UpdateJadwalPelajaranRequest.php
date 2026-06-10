<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJadwalPelajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        // Jika user pilih master jam, derive jamke/jamawal/jamakhir dari master jam
        $masterJamId = $this->input('master_jam_pelajaran_id');
        if (!$masterJamId) {
            return;
        }

        $masterJam = \App\Modules\Akademik\Models\MasterJamPelajaran::find($masterJamId);
        if (!$masterJam) {
            return;
        }

        $this->merge([
            'jamke' => $masterJam->jamke,
            'jamawal' => $masterJam->jamawal ? substr($masterJam->jamawal, 0, 5) : null,
            'jamakhir' => $masterJam->jamakhir ? substr($masterJam->jamakhir, 0, 5) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'rombel_id' => 'required|exists:rombel,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',

            'master_jam_pelajaran_id' => 'nullable|exists:master_jam_pelajarans,id',

            'jamke' => 'required|integer|min:1',
            'jamawal' => 'required|date_format:H:i',
            'jamakhir' => 'required|date_format:H:i|after:jamawal',
        ];
    }

    public function messages(): array
    {
        return [
            'rombel_id.required' => 'Rombel wajib dipilih.',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
            'hari.required' => 'Hari wajib dipilih.',
            'master_jam_pelajaran_id.exists' => 'Master jam pelajaran tidak ditemukan.',

            'jamke.required' => 'Jam ke- wajib diisi.',
            'jamawal.required' => 'Jam mulai wajib diisi.',
            'jamakhir.required' => 'Jam selesai wajib diisi.',
            'jamakhir.after' => 'Jam selesai harus setelah jam mulai.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hari = $this->input('hari');
            $jamke = $this->input('jamke');
            $guruId = $this->input('guru_id');
            $rombelId = $this->input('rombel_id');
            $currentId = $this->route('jadwalPelajaran') ? $this->route('jadwalPelajaran')->id : null;

            if ($hari && $jamke && $rombelId) {
                $rombel = \App\Modules\Akademik\Models\Rombel::find($rombelId);
                $tahunAjaranId = $rombel ? $rombel->tahunajaran_id : null;

                // Cek bentrok Guru
                if ($guruId && $tahunAjaranId) {
                    $currentMapel = \App\Modules\Akademik\Models\MataPelajaran::find($this->input('mapel_id'));
                    $isDoubleMapel = $currentMapel && $currentMapel->bisa_double;

                    $guruConflict = \App\Modules\Akademik\Models\JadwalPelajaran::where('guru_id', $guruId)
                        ->where('hari', $hari)
                        ->where('jamke', $jamke)
                        ->whereHas('rombel', function($q) use ($tahunAjaranId) {
                            $q->where('tahunajaran_id', $tahunAjaranId);
                        })
                        ->when($currentId, function ($query, $currentId) {
                            return $query->where('id', '!=', $currentId);
                        })
                        ->first();

                    if ($guruConflict) {
                        $conflictingMapel = $guruConflict->mataPelajaran;
                        $bothDouble = $isDoubleMapel && $conflictingMapel && $conflictingMapel->bisa_double;

                        if (!$bothDouble) {
                            $validator->errors()->add('guru_id', "Guru ini sudah mengajar di kelas lain pada hari {$hari} jam ke-{$jamke} di tahun ajaran yang sama.");
                        }
                    }
                }

                // Cek bentrok Rombel
                if ($rombelId) {
                    $rombelConflict = \App\Modules\Akademik\Models\JadwalPelajaran::where('rombel_id', $rombelId)
                        ->where('hari', $hari)
                        ->where('jamke', $jamke)
                        ->when($currentId, function ($query, $currentId) {
                            return $query->where('id', '!=', $currentId);
                        })
                        ->first();

                    if ($rombelConflict) {
                        $validator->errors()->add('rombel_id', "Rombel ini sudah memiliki mata pelajaran lain pada hari {$hari} jam ke-{$jamke}.");
                    }
                }
            }
        });
    }
}
