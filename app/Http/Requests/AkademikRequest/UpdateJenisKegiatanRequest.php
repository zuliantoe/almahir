<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJenisKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jeniskegiatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jenis_kegiatan', 'jeniskegiatan')
                    ->ignore($this->route('jenis_kegiatan')->id),
            ],
            'deskripsi' => 'nullable|string|max:500',
            'is_kbm' => 'boolean',
            'warna' => 'nullable|string|max:7',
        ];
    }

    public function messages(): array
    {
        return [
            'jeniskegiatan.required' => 'Jenis kegiatan harus diisi.',
            'jeniskegiatan.string' => 'Jenis kegiatan harus berupa teks.',
            'jeniskegiatan.max' => 'Jenis kegiatan maksimal 255 karakter.',
            'jeniskegiatan.unique' => 'Jenis kegiatan sudah digunakan.',
            'deskripsi.max' => 'Deskripsi maksimal 500 karakter.',
        ];
    }
}
