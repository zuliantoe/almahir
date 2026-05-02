<?php

namespace Modules\PegawaiManager\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route sudah diamankan oleh middleware, jadi izinkan request
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'type_pegawai_id' => 'required|uuid',
            'email' => 'required|email|unique:sys_users,email|unique:pegawai,email',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
            'role_name' => 'required|string|exists:sys_roles,name',
        ];
    }
}
