<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $kelas = $this->route('kelas');

        return [
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas', 'nama_kelas')->ignore($kelas)],
            'tingkat' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }
}
