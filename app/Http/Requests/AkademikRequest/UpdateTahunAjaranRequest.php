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
            'tahunajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('tahun_ajaran', 'tahunajaran')->ignore($this->route('tahun_ajaran'))
            ],
            'status' => 'nullable|boolean'
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
