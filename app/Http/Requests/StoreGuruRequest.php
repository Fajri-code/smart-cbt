<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['nullable', 'string', 'max:255', 'unique:gurus,nip'],
            'nama' => ['required', 'string', 'max:255'],
            'kode_guru' => ['required', 'string', 'max:255', 'unique:gurus,kode_guru'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
