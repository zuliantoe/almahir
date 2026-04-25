<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKalenderAkademikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'kegiatan_id' => 'required|exists:jenis_kegiatan,id',
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'tahunajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'kegiatan_id.required' => 'Jenis kegiatan wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tanggal_awal.required' => 'Tanggal awal wajib diisi.',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ];
    }
}
