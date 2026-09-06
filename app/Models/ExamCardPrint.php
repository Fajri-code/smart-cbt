<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCardPrint extends Model
{
    protected $fillable = [
        'nama_ujian',
        'kelas_id',
        'ruangan',
        'jumlah_kartu',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}

