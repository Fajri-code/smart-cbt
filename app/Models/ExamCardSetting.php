<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCardSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'logo_kiri', 'logo_kanan', 'header_1', 'header_2', 'header_3', 'header_4',
        'judul_kartu', 'tempat_tanggal', 'jabatan_penandatangan', 'nama_penandatangan',
        'nip_penandatangan', 'ttd_image'
    ];
}
