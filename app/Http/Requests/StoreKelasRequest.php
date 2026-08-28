<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => ['required', 'string', 'max:255', 'unique:kelas,nama_kelas'],
            'tingkat' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }
}
