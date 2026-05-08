<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahunajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                'unique:tahun_ajaran,tahunajaran,' . $this->route('tahun_ajaran')
            ],
            'semester' => 'required|in:Ganjil,Genap',
            'status' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'tahunajaran.required' => 'Tahun ajaran wajib diisi.',
            'tahunajaran.regex' => 'Format harus seperti 2023/2024.',
            'tahunajaran.unique' => 'Tahun ajaran sudah ada.'
        ];
    }
}
