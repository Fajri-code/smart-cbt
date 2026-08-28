<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'siswa_id',
        'started_at',
        'submitted_at',
        'status',
        'nilai_akhir',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'nilai_akhir' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}