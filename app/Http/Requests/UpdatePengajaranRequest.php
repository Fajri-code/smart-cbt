<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePengajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $pengajaran = $this->route('pengajaran');

        return [
            'guru_id' => ['required', 'exists:gurus,id', Rule::unique('pengajarans', 'guru_id')
                ->ignore($pengajaran)
                ->where(fn ($query) => $query
                    ->where('mata_pelajaran_id', $this->input('mata_pelajaran_id'))
                    ->where('kelas_id', $this->input('kelas_id')))],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }
}
