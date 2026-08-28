<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['nama' => 'Adipati Brata Setiaji', 'nis' => '2627030701', 'email' => '2627030701@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Adskhan Aldrica Arhabu', 'nis' => '2627030702', 'email' => '2627030702@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Akhdan Latif Azizan', 'nis' => '2627030704', 'email' => '2627030704@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Albiyan Agung Dikara', 'nis' => '2627030705', 'email' => '2627030705@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Dewi Nitdya Kasim', 'nis' => '2627030707', 'email' => '2627030707@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Dina Khoirun Nissa', 'nis' => '2627030708', 'email' => '2627030708@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Indhira Azzahra Maheswara', 'nis' => '2627030710', 'email' => '2627030710@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Jihan Bilqis Ufaira', 'nis' => '2627030711', 'email' => '2627030711@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Khansa Airela Badzlina', 'nis' => '2627030712', 'email' => '2627030712@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Mahira Hasna Kamila', 'nis' => '2627030713', 'email' => '2627030713@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Muhammad Naufal Farhan', 'nis' => '2627030716', 'email' => '2627030716@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Nabilah Kanza Azzalfa', 'nis' => '2627030717', 'email' => '2627030717@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Narayana Arkan Shakeil', 'nis' => '2627030718', 'email' => '2627030718@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Naura Nadhifa Akmal', 'nis' => '2627030719', 'email' => '2627030719@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => "Putrawira Maulana Sya'bani", 'nis' => '2627030720', 'email' => '2627030720@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Mikheyl abreal darmawan', 'nis' => '2627030715', 'email' => '2627030715@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'M Adnan Dennuri', 'nis' => '2627030714', 'email' => '2627030714@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Khanza Annisa Safiyya', 'nis' => '26270307112', 'email' => '26270307112@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'Azzahra Tirta Ramadhani', 'nis' => '2627030722', 'email' => '2627030722@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],
            ['nama' => 'FIKRI MUHDOROTUL MUBAROK', 'nis' => '26270307008', 'email' => '26270307008@smart.sch.id', 'kelas' => 'VII - Ibnu Taimiyah'],

            ['nama' => 'Muhammad Azmi', 'nis' => '2627.03.07.28', 'email' => '2627.03.07.28@smart.sch.id', 'kelas' => 'VII - Tahasus'],
            ['nama' => 'Habibah Azmi Azzahra', 'nis' => '2627.03.07.31', 'email' => '2627.03.07.31@smart.sch.id', 'kelas' => 'VII - Tahasus'],
            ['nama' => 'Ahmad Azril Ilham', 'nis' => '2627.03.07.30', 'email' => '2627.03.07.30@smart.sch.id', 'kelas' => 'VII - Tahasus'],
            ['nama' => 'Diana Zafirah putri', 'nis' => '2627.03.07.32', 'email' => '2627.03.07.32@smart.sch.id', 'kelas' => 'VII - Tahasus'],
            ['nama' => 'Salsabila Khoirunnisa', 'nis' => '2627.03.07.29', 'email' => '2627.03.07.29@smart.sch.id', 'kelas' => 'VII - Tahasus'],

            ['nama' => 'Ahmad Akhtar Rayyan', 'nis' => '3091375841', 'email' => '3091375841@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'Aisyah Jelita Permatasari', 'nis' => '3135655179', 'email' => '3135655179@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'ARYA BIMA PRAKOSO', 'nis' => '0129750813', 'email' => '0129750813@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'HAURA REVELY PUTRI NUGROHO', 'nis' => '3137723449', 'email' => '3137723449@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'JIHAN AINUL MARHAMAH', 'nis' => '3128604094', 'email' => '3128604094@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'KEYLA OKTAVIA HUSEIN', 'nis' => '3133277223', 'email' => '3133277223@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'MIKHAILA CAHAYA LANGIT', 'nis' => '0129050887', 'email' => '0129050887@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'MONIC PRATHAMA', 'nis' => '0134252498', 'email' => '0134252498@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'Moreno Kang', 'nis' => '3123650796', 'email' => '3123650796@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'MUHAMMAD ZEIN', 'nis' => '3123360067', 'email' => '3123360067@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'PANJI ASMORO DWI RAIHANDOKO', 'nis' => '3123617379', 'email' => '3123617379@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'Putri Marwah Salsabila', 'nis' => '3130229169', 'email' => '3130229169@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'RASYA AHMAD NOVRYAN', 'nis' => '3121328187', 'email' => '3121328187@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'SAKA IBADIL KIRAM', 'nis' => '3128891249', 'email' => '3128891249@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'WINDIANI FITRIA NINGSIH', 'nis' => '3139893211', 'email' => '3139893211@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'ZLATAN AHMAD ZULFIKAR', 'nis' => '0116306864', 'email' => '0116306864@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'Zorigio Lyan Macky Rajo Alam', 'nis' => '2627030817', 'email' => '2627030817@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'Edi Ladio Lakaende', 'nis' => '2627030818', 'email' => '2627030818@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],
            ['nama' => 'Revi Andinyasah', 'nis' => '2627030819', 'email' => '2627030819@smart.sch.id', 'kelas' => 'VIII - Ibnu Khaldun'],

            ['nama' => 'Jauharatun Nafisa', 'nis' => '2526.03.07.19', 'email' => '2526.03.07.19@smart.sch.id', 'kelas' => 'VIII - Tahasus'],
            ['nama' => 'Ummu Fatimah', 'nis' => '2526.03.07.22', 'email' => '2526.03.07.22@smart.sch.id', 'kelas' => 'VIII - Tahasus'],
            ['nama' => "Syafa'atul Husna", 'nis' => '2526.03.07.21', 'email' => '2526.03.07.21@smart.sch.id', 'kelas' => 'VIII - Tahasus'],

            ['nama' => 'Aminanti Setya Dewi', 'nis' => '3117095223', 'email' => '3117095223@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Aura Safira', 'nis' => '3121752940', 'email' => '3121752940@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Azna Helva Ramadhania', 'nis' => '0116101537', 'email' => '0116101537@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'AZZURA TALITHA EL FIRDAUSI', 'nis' => '0121009495', 'email' => '0121009495@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'FATIMAH HANAA RAMADHANI', 'nis' => '0126023963', 'email' => '0126023963@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'HAIKAL FADHIL PUTRA HADIAN', 'nis' => '3127929595', 'email' => '3127929595@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'INDRA WIJAYA', 'nis' => '3120203416', 'email' => '3120203416@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Intan Ayuwanda Wijaya', 'nis' => '3113009432', 'email' => '3113009432@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Intan Novaliza', 'nis' => '0116749356', 'email' => '0116749356@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Januarizal', 'nis' => '121628160', 'email' => '121628160@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'MUHAMAD ARFI WIJAYA', 'nis' => '0129234708', 'email' => '0129234708@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'MUHAMAD REZA FAUZI', 'nis' => '0111446857', 'email' => '0111446857@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'MUHAMMAD MIFTAKHURROZIQIN', 'nis' => '0117402084', 'email' => '0117402084@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'MUHAMMAD TEGAR', 'nis' => '0112533988', 'email' => '0112533988@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Muhammad Zidan Faisal Abdul Azis', 'nis' => '0129428783', 'email' => '0129428783@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Nicholas Nabil Wilson', 'nis' => '0113000068', 'email' => '0113000068@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Randitri Alfattah', 'nis' => '0115724875', 'email' => '0115724875@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'Rayhan Saputra', 'nis' => '3125225220', 'email' => '3125225220@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'SAFITRI RAHAYU NINGSIH', 'nis' => '0118922792', 'email' => '0118922792@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'SHERLY FELICIA MUNTHE', 'nis' => '3113471583', 'email' => '3113471583@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'SILVI MAYSAROH', 'nis' => '0122716438', 'email' => '0122716438@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'VICKY ADI WIJAYA', 'nis' => '0115179181', 'email' => '0115179181@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'AISYAH JUWITA PERMATASARI', 'nis' => '2425030702', 'email' => '2425030702@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],
            ['nama' => 'MUHAMMAD ALFIN RAMADHANI', 'nis' => '2425030713', 'email' => '2425030713@smart.sch.id', 'kelas' => 'IX - Ibnu Hisyam'],

            ['nama' => 'Aisha Nurrohmah', 'nis' => '2425.03.07.29', 'email' => '2425.03.07.29@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => "Aufa Mil'ul Ajri", 'nis' => '2425.03.07.30', 'email' => '2425.03.07.30@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => 'Husna Ahda Sabila', 'nis' => '24.25.03.07.31', 'email' => '24.25.03.07.31@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => "Isma' Dua' Ana", 'nis' => '2425.03.07.32', 'email' => '24.25.03.07.32@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => "M. 'Ibadurrahman Maqbul", 'nis' => '2425.03.07.33', 'email' => '2425.03.07.33@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => 'Nadia Larasati', 'nis' => '2425.03.07.34', 'email' => '2425.03.07.34@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => 'Salsabila Asy Syifa Qolbi', 'nis' => '2425.03.07.35', 'email' => '2425.03.07.35@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => 'Shofy Kamila Auliaunnisa', 'nis' => '2425.03.07.36', 'email' => '2425.03.07.36@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => 'Muzay Hawyasifa', 'nis' => '2425.03.07.37', 'email' => '2425.03.07.37@smart.sch.id', 'kelas' => 'IX - Tahasus'],
            ['nama' => 'Rika Dzakirah', 'nis' => '2425.03.07.38', 'email' => '2425.03.07.38@smart.sch.id', 'kelas' => 'IX - Tahasus'],
        ];

        $tahasusClasses = [
            'VII - Tahasus',
            'VIII - Tahasus',
            'IX - Tahasus',
        ];

        foreach ($students as $student) {
            $isTahasus = in_array($student['kelas'], $tahasusClasses, true);
            $schoolClass = $isTahasus ? null : $student['kelas'];
            $kelas = $schoolClass ? Kelas::where('nama_kelas', $schoolClass)->firstOrFail() : null;

            $user = User::updateOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['nama'],
                    'password' => Hash::make('12345678'),
                    'role' => 'siswa',
                ]
            );

            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis' => $student['nis'],
                    'nisn' => $student['nis'],
                    'nama' => $student['nama'],
                    'kelas' => $schoolClass,
                    'kelas_id' => $kelas?->id,
                    'program_tahasus' => $isTahasus,
                    'tahun_ajaran' => '2026/2027',
                    'status_aktif' => true,
                ]
            );
        }
    }
}
