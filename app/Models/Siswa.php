<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'nama',
        'kelas',
        'kelas_id',
        'program_tahasus',
        'tahun_ajaran',
        'status_aktif',
    ];

    protected $casts = [
        'program_tahasus' => 'boolean',
        'status_aktif' => 'boolean',
    ];

    /**
     * Akun login siswa
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasData()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Riwayat pengerjaan ujian
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}