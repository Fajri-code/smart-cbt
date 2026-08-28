<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $guru = $this->route('guru');

        return [
            'nip' => ['nullable', 'string', 'max:255', Rule::unique('gurus', 'nip')->ignore($guru)],
            'nama' => ['required', 'string', 'max:255'],
            'kode_guru' => ['required', 'string', 'max:255', Rule::unique('gurus', 'kode_guru')->ignore($guru)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guru?->user_id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
