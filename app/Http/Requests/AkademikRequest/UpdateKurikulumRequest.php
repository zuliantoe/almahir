<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKurikulumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxJamSeminggu = \App\Modules\Akademik\Models\MasterJamPelajaran::where('is_istirahat', false)->count() ?: 48;
        return [
            'master_kurikulum_id' => 'required|exists:master_kurikulum,id',
            'tingkat_id' => 'required|exists:tingkat,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'totaljam' => 'required|integer|min:0|max:' . $maxJamSeminggu,
            'kkm' => 'required|integer|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        $maxJamSeminggu = \App\Modules\Akademik\Models\MasterJamPelajaran::where('is_istirahat', false)->count() ?: 48;
        return [
            'master_kurikulum_id.required' => 'Master kurikulum wajib dipilih.',
            'tingkat_id.required' => 'Tingkat wajib dipilih.',
            'tahunajaran_id.required' => 'Tahun ajaran wajib dipilih.',

            'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
            'totaljam.required' => 'Total jam wajib diisi.',
            'totaljam.max'      => "Total jam pelajaran per minggu tidak boleh melebihi {$maxJamSeminggu} jam.",
            'kkm.required'      => 'KKM wajib diisi.',
            'kkm.max'           => 'Nilai KKM tidak boleh melebihi 100.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $masterKurikulumId = $this->input('master_kurikulum_id');
            $tingkatId = $this->input('tingkat_id');
            $tahunajaranId = $this->input('tahunajaran_id');
            $kelasId = $this->input('kelas_id');
            $totalJamInput = $this->input('totaljam', 0);
            
            $currentKurikulum = $this->route('kurikulum');
            $currentId = $currentKurikulum ? $currentKurikulum->id : null;

            if ($masterKurikulumId && $tingkatId && $tahunajaranId) {
                // Sum existing jam for the same kurikulum group, excluding self
                $existingSum = \App\Modules\Akademik\Models\Kurikulum::where('master_kurikulum_id', $masterKurikulumId)
                    ->where('tingkat_id', $tingkatId)
                    ->where('tahunajaran_id', $tahunajaranId)
                    ->where(function ($query) use ($kelasId) {
                        if ($kelasId === null || $kelasId === '') {
                            $query->whereNull('kelas_id');
                        } else {
                            $query->where('kelas_id', $kelasId);
                        }
                    })
                    ->when($currentId, function ($query, $currentId) {
                        return $query->where('id', '!=', $currentId);
                    })
                    ->sum('totaljam');

                $maxJamSeminggu = \App\Modules\Akademik\Models\MasterJamPelajaran::where('is_istirahat', false)->count() ?: 48;
                if (($existingSum + $totalJamInput) > $maxJamSeminggu) {
                    $validator->errors()->add('totaljam', "Total akumulasi jam pelajaran per minggu untuk kurikulum ini tidak boleh melebihi {$maxJamSeminggu} jam pelajaran (Saat ini sudah terisi {$existingSum} jam, Baru diinput: {$totalJamInput} jam).");
                }
            }
        });
    }
}
