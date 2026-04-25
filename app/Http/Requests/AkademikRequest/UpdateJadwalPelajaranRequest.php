<?php

namespace App\Http\Requests\AkademikRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJadwalPelajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rombel_id' => 'required|exists:rombel,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jamke' => 'required|integer|min:1',
            'jamawal' => 'required|date_format:H:i',
            'jamakhir' => 'required|date_format:H:i|after:jamawal',
        ];
    }

    public function messages(): array
    {
        return [
            'rombel_id.required' => 'Rombel wajib dipilih.',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih.',
            'guru_id.required' => 'Guru wajib dipilih.',
            'hari.required' => 'Hari wajib dipilih.',
            'jamke.required' => 'Jam ke- wajib diisi.',
            'jamawal.required' => 'Jam mulai wajib diisi.',
            'jamakhir.required' => 'Jam selesai wajib diisi.',
            'jamakhir.after' => 'Jam selesai harus setelah jam mulai.',
        ];
    }
}
