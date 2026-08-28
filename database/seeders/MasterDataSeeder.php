<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['kode' => 'MTK', 'nama' => 'Matematika'],
            ['kode' => 'BIO', 'nama' => 'Biologi'],
            ['kode' => 'IPS', 'nama' => 'Ilmu Pengetahuan Sosial'],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Kewarganegaraan'],
            ['kode' => 'PJOK', 'nama' => 'Pendidikan Jasmani Olahraga dan Kesehatan'],
            ['kode' => 'TIK', 'nama' => 'Teknologi Informasi dan Komunikasi'],
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama Islam'],
            ['kode' => 'PRA', 'nama' => 'Prakarya'],
            ['kode' => 'FIS', 'nama' => 'Fisika'],
            ['kode' => 'BTQ', 'nama' => "Baca Tulis Qur'an"],
            ['kode' => 'ARB', 'nama' => 'Bahasa Arab'],
            ['kode' => 'TIL', 'nama' => 'Tilawati'],
        ];

        foreach ($subjects as $subject) {
            MataPelajaran::firstOrCreate(
                ['nama' => $subject['nama']],
                ['kode' => $subject['kode']]
            );
        }

        $classes = [
            ['nama_kelas' => 'VII - Ibnu Taimiyah', 'tingkat' => 'VII'],
            ['nama_kelas' => 'VIII - Ibnu Khaldun', 'tingkat' => 'VIII'],
            ['nama_kelas' => 'IX - Ibnu Hisyam', 'tingkat' => 'IX'],
        ];

        foreach ($classes as $class) {
            Kelas::firstOrCreate(
                ['nama_kelas' => $class['nama_kelas']],
                ['tingkat' => $class['tingkat'], 'status' => 'aktif']
            );
        }
    }
}