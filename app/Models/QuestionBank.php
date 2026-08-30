<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = ['guru_id', 'mata_pelajaran_id', 'nama', 'source_exam_id', 'keterangan'];

    public function guru() { return $this->belongsTo(Guru::class); }
    public function mataPelajaran() { return $this->belongsTo(MataPelajaran::class); }
    public function sourceExam() { return $this->belongsTo(Exam::class, 'source_exam_id'); }
    public function questions() { return $this->hasMany(BankQuestion::class); }
}