<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['question_bank_id', 'tipe', 'pertanyaan', 'petunjuk_jawaban', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'kunci', 'bobot'];

    public function bank() { return $this->belongsTo(QuestionBank::class, 'question_bank_id'); }
}