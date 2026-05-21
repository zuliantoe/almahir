<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreKurikulumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'master_kurikulum_id' => 'required|exists:master_kurikulum,id',
            'tingkat_id' => 'required|exists:tingkat,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'totaljam' => 'required|integer|min:0|max:48',
            'kkm' => 'required|integer|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'master_kurikulum_id.required' => 'Master kurikulum wajib dipilih.',
            'tingkat_id.required' => 'Tingkat wajib dipilih.',
            'tahunajaran_id.required' => 'Tahun ajaran wajib dipilih.',

            'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
            'totaljam.required' => 'Total jam wajib diisi.',
            'kkm.required' => 'KKM wajib diisi.',
        ];
    }
}
