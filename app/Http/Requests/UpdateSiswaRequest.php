<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $siswa = $this->route('siswa');

        return [
            'nis' => ['nullable', 'string', 'max:255', 'required_without:nisn', Rule::unique('siswas', 'nis')->ignore($siswa)],
            'nisn' => ['nullable', 'string', 'max:255', 'required_without:nis', Rule::unique('siswas', 'nisn')->ignore($siswa)],
            'nama' => ['required', 'string', 'max:255'],
            'kelas_id' => ['nullable', 'integer', Rule::exists('kelas', 'id')->where('status', 'aktif'), 'required_without:program_tahasus'],
            'kelas' => ['nullable', 'string', 'max:255', 'not_regex:/tahasus/i'],
            'program_tahasus' => ['nullable', 'boolean'],
                'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'status_aktif' => ['nullable', 'boolean'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($siswa?->user_id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
