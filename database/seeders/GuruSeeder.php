<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $gurus = [
            [
                'name' => 'Rapi Manurung, S.S, M.M',
                'email' => 'rapimanurung@smart.sch.id',
            ],
            [
                'name' => 'Yeti Ruwaidayati, S.Pd',
                'email' => 'yetiruwaidayati@smart.sch.id',
            ],
            [
                'name' => 'Deni Saputra, S.Pd',
                'email' => 'denisaputra@smart.sch.id',
            ],
            [
                'name' => 'Khofifah, S.Hum',
                'email' => 'khofifah@smart.sch.id',
            ],
            [
                'name' => 'Laily Salma Hanum, S.Hum, M.Pd',
                'email' => 'lailysalmahanum@smart.sch.id',
            ],
            [
                'name' => 'Asep Gunawan, S.Pd',
                'email' => 'asepgunawan@smart.sch.id',
            ],
            [
                'name' => 'Trio Wahyu Afandi, S.Kom',
                    'email' => 'triowahyuafandi@smart.sch.id',
            ],
            [
                'name' => 'Ima Datul Ummah, S.Pd',
                'email' => 'imadatul@smart.sch.id',
            ],
            [
                'name' => 'Inna Layla Rahma Fauziah, S.Pd',
                'email' => 'innalaylarahmafauziah@smart.sch.id',
            ],
            [
                'name' => 'Rian Indra Gunawan, M.Pd',
                'email' => 'rianindragunawan@smart.sch.id',
            ],
            [
                'name' => 'Nurudin, S.Pd',
                'email' => 'nurudin@smart.sch.id',
            ],
            [
                'name' => 'Yuni Aswani, M.M, M.Pd',
                'email' => 'yuniaswani@smart.sch.id',
            ],
            [
                'name' => 'Dina Manja',
                'email' => 'dinamanja@smart.sch.id',
            ],
            [
                'name' => 'Endrik',
                'email' => 'endrik@smart.sch.id',
            ],
        ];

        foreach ($gurus as $index => $guru) {
            $user = User::updateOrCreate(
                ['email' => $guru['email']],
                [
                    'name' => $guru['name'],
                    'password' => Hash::make('12345678'),
                    'role' => 'guru',
                ]
            );

            Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $guru['name'],
                    'kode_guru' => 'GURU-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                ]
            );
        }
    }
}