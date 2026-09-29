<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'tipe',
        'pertanyaan',
        'image',
        'petunjuk_jawaban',
        'opsi_a',
        'opsi_a_image',
        'opsi_b',
        'opsi_b_image',
        'opsi_c',
        'opsi_c_image',
        'opsi_d',
        'opsi_d_image',
        'opsi_e',
        'opsi_e_image',
        'kunci',
        'bobot',
        'urutan',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
