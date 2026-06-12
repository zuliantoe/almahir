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
            'kapasitas'  => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kelas.required' => 'Nama kelas harus diisi.',
            'nama_kelas.string'   => 'Nama kelas harus berupa teks.',
            'nama_kelas.max'      => 'Nama kelas maksimal 255 karakter.',
            'nama_kelas.unique'   => 'Nama kelas sudah digunakan.',
            'kapasitas.required'  => 'Kapasitas kelas wajib diisi.',
            'kapasitas.integer'   => 'Kapasitas kelas harus berupa angka.',
            'kapasitas.min'       => 'Kapasitas kelas minimal 1.',
            'kapasitas.max'       => 'Kapasitas kelas maksimal 100.',
        ];
    }
}
