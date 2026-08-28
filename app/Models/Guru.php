<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'kode_guru',
    ];

    /**
     * Akun login guru
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ujian yang dibuat/dikelola guru
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function questionBanks()
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function pengajarans()
    {
        return $this->hasMany(Pengajaran::class);
    }

    public function supervisedExams()
    {
        return $this->belongsToMany(Exam::class, 'exam_supervisors')
            ->withPivot('ruangan')
            ->withTimestamps();
    }
}