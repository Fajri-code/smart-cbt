<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMataPelajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $mataPelajaran = $this->route('mata_pelajaran');

        return [
            'kode' => ['required', 'string', 'max:255', Rule::unique('mata_pelajarans', 'kode')->ignore($mataPelajaran)],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
