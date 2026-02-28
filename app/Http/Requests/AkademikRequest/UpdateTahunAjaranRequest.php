<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTahunAjaranRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tahun_ajaran' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_ajaran', 'tahun_ajaran')->ignore($this->tahunAjaran)
            ],
            'status' => 'sometimes|boolean'
        ];
    }

    public function messages()
    {
        return [
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi',
            'tahun_ajaran.unique' => 'Tahun ajaran sudah ada'
        ];
    }
}
