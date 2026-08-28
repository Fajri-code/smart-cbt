<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'guru_pengawas_id',
        'mata_pelajaran_id',
        'nama',
        'jenis',
        'kelas',
        'kelas_id',
        'ruangan',
        'kode_ujian',
        'token',
        'token_aktif',
        'token_dibuat_at',
        'token_kedaluwarsa_at',
        'durasi_menit',
        'deskripsi',
        'komponen_soal',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'komponen_soal' => 'array',
        'token_aktif' => 'boolean',
        'token_dibuat_at' => 'datetime',
        'token_kedaluwarsa_at' => 'datetime',
    ];

    public function activateToken(): void
    {
        do {
            $token = Str::upper(Str::random(6));
        } while (self::where('token', $token)->where('id', '!=', $this->getKey())->exists());

        $this->update([
            'token' => $token,
            'token_aktif' => true,
            'token_dibuat_at' => now(),
            'token_kedaluwarsa_at' => now()->addMinutes(10),
            'status' => 'aktif',
        ]);
    }

    public function ensureActiveToken(): void
    {
        if (! $this->token || ! $this->token_aktif) {
            $this->activateToken();
            return;
        }

        if ($this->token_kedaluwarsa_at && $this->token_kedaluwarsa_at->lte(now())) {
            $this->activateToken();
            return;
        }

        if (! $this->token_kedaluwarsa_at) {
            $this->update(['token_kedaluwarsa_at' => now()->addMinutes(10)]);
        }
    }

    public function prepareToken(): void
    {
        if ($this->token) {
            return;
        }

        do {
            $token = Str::upper(Str::random(6));
        } while (self::where('token', $token)->exists());

        $this->update([
            'token' => $token,
            'token_aktif' => false,
            'token_dibuat_at' => now(),
            'token_kedaluwarsa_at' => null,
        ]);
    }

    public function rotateExpiredToken(): void
    {
        if (! $this->token_aktif) {
            return;
        }

        if (! $this->token_kedaluwarsa_at) {
            $this->update(['token_kedaluwarsa_at' => now()->addMinutes(10)]);
            return;
        }

        if ($this->token_kedaluwarsa_at->lte(now())) {
            $this->activateToken();
        }
    }

    /**
     * Guru pembuat ujian
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function guruPengawas()
    {
        return $this->belongsTo(Guru::class, 'guru_pengawas_id');
    }

    /**
     * Mata pelajaran
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /**
     * Semua soal ujian
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('urutan');
    }

    /**
     * Semua pengerjaan siswa
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function kelasData()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function supervisors()
    {
        return $this->belongsToMany(Guru::class, 'exam_supervisors')
            ->withPivot('ruangan')
            ->withTimestamps();
    }
}