<?php

namespace Modules\PegawaiManager\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\PegawaiManager\Models\Pegawai;

class UpdatePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Mengambil parameter {id} dari routing
        $id = $this->route('id');
        $pegawai = Pegawai::findOrFail($id);

        return [
            'nama' => 'required|string|max:255',
            'type_pegawai_id' => 'required|uuid',
            'email' => [
                'required',
                'email',
                Rule::unique('sys_users', 'email')->ignore($pegawai->user_id),
                Rule::unique('pegawai', 'email')->ignore($pegawai->id),
            ],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
            'role_name' => 'required|string|exists:sys_roles,name',
        ];
    }
}
