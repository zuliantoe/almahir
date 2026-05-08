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
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'guru_id'    => 'nullable|string', // UUID
            'kode_kelas' => 'nullable|string|max:50',
            'tingkat_id' => 'nullable|integer|exists:tingkat,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kelas.required' => 'Nama kelas harus diisi.',
            'nama_kelas.string'   => 'Nama kelas harus berupa teks.',
            'nama_kelas.max'      => 'Nama kelas maksimal 255 karakter.',
            'nama_kelas.unique'   => 'Nama kelas sudah digunakan.',
        ];
    }
}
