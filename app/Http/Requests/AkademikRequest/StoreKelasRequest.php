<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bisa tambahkan policy nanti
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:kelas,nama',
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
