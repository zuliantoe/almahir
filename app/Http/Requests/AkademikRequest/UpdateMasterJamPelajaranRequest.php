<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdateMasterJamPelajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $param = $this->route('master_jam_pelajaran');
        $id = is_object($param) ? $param->id : $param;

        return [
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jamke' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('master_jam_pelajarans')->ignore($id)->where(function ($query) {
                    return $query->where('hari', $this->input('hari'));
                })
            ],
            'jamawal' => 'required|date_format:H:i',
            'jamakhir' => 'required|date_format:H:i|after:jamawal',
            'is_istirahat' => 'nullable|boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hari = $this->input('hari');
            $jamawal = $this->input('jamawal');
            $jamakhir = $this->input('jamakhir');

            if ($hari && $jamawal && $jamakhir) {
                $param = $this->route('master_jam_pelajaran');
                $id = is_object($param) ? $param->id : $param;

                $overlap = \App\Modules\Akademik\Models\MasterJamPelajaran::where('hari', $hari)
                    ->when($id, function ($query) use ($id) {
                        return $query->where('id', '!=', $id);
                    })
                    ->where(function ($query) use ($jamawal, $jamakhir) {
                        $query->where('jamawal', '<', $jamakhir)
                              ->where('jamakhir', '>', $jamawal);
                    })
                    ->first();

                if ($overlap) {
                    $validator->errors()->add('jamawal', "Waktu belajar bentrok dengan Jam Ke-{$overlap->jamke} ({$overlap->jamawal} - {$overlap->jamakhir}) pada hari {$hari}.");
                    $validator->errors()->add('jamakhir', "Waktu belajar bentrok dengan Jam Ke-{$overlap->jamke} ({$overlap->jamawal} - {$overlap->jamakhir}) pada hari {$hari}.");
                }
            }
        });
    }
}

