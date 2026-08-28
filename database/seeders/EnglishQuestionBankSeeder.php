<?php

namespace Database\Seeders;

use App\Models\BankQuestion;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\QuestionBank;
use Illuminate\Database\Seeder;

class EnglishQuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $guru = Guru::whereHas('user', fn ($query) => $query->where('email', 'rapimanurung@smart.sch.id'))->firstOrFail();
        $subject = MataPelajaran::where('nama', 'Bahasa Inggris')->firstOrFail();

        $bank = QuestionBank::firstOrCreate([
            'guru_id' => $guru->id,
            'mata_pelajaran_id' => $subject->id,
            'nama' => 'Bank Soal Bahasa Inggris',
        ]);

        $questions = [
            [
                'pertanyaan' => 'What is the correct response to "How are you?"',
                'opsi_a' => 'I am fine, thank you.',
                'opsi_b' => 'My name is Rapi.',
                'opsi_c' => 'I am twelve years old.',
                'opsi_d' => 'Good night.',
                'kunci' => 'A',
            ],
            [
                'pertanyaan' => 'She ___ to school every day.',
                'opsi_a' => 'go',
                'opsi_b' => 'goes',
                'opsi_c' => 'going',
                'opsi_d' => 'gone',
                'kunci' => 'B',
            ],
            [
                'pertanyaan' => 'The opposite of "expensive" is ___.',
                'opsi_a' => 'large',
                'opsi_b' => 'cheap',
                'opsi_c' => 'strong',
                'opsi_d' => 'difficult',
                'kunci' => 'B',
            ],
        ];

        foreach ($questions as $question) {
            BankQuestion::firstOrCreate(
                ['question_bank_id' => $bank->id, 'pertanyaan' => $question['pertanyaan']],
                [...$question, 'tipe' => 'pg', 'bobot' => 1]
            );
        }
    }
}