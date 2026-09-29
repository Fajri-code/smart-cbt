<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
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
        'bobot'
    ];

    public function bank() { return $this->belongsTo(QuestionBank::class, 'question_bank_id'); }
}
