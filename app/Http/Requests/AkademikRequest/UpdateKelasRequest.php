<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kelas', 'nama')->ignore(
                    $this->route('kelas') // ambil ID dari route binding
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kelas harus diisi.',
            'nama.string'   => 'Nama kelas harus berupa teks.',
            'nama.max'      => 'Nama kelas maksimal 255 karakter.',
            'nama.unique'   => 'Nama kelas sudah digunakan.',
        ];
    }
}
