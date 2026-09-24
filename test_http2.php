<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$siswa1 = App\Models\Siswa::first();
$siswa2 = App\Models\Siswa::skip(1)->first();
$exam = App\Models\Exam::where('acak_soal', true)->where('status', 'aktif')->first();

if (!$exam) {
    $exam = App\Models\Exam::where('acak_soal', true)->first();
    $exam->update(['status' => 'aktif', 'token_aktif' => true, 'token' => '12345']);
}

// Ensure attempts
$attempt1 = App\Models\ExamAttempt::firstOrCreate(['exam_id' => $exam->id, 'siswa_id' => $siswa1->id], ['started_at' => now(), 'status' => 'in_progress', 'token_used' => '12345']);
$attempt2 = App\Models\ExamAttempt::firstOrCreate(['exam_id' => $exam->id, 'siswa_id' => $siswa2->id], ['started_at' => now(), 'status' => 'in_progress', 'token_used' => '12345']);

// Just test the core logic from SiswaExamController
$questions = $exam->questions->all();
mt_srand(crc32($attempt1->id));
shuffle($questions);
mt_srand();
$exam->setRelation('questions', collect($questions));
echo "Siswa 1 Order: " . $exam->questions->pluck('id')->implode(',') . "\n";

$exam2 = clone $exam;
$exam2->unsetRelation('questions');
$questions2 = $exam2->questions->all();
mt_srand(crc32($attempt2->id));
shuffle($questions2);
mt_srand();
$exam2->setRelation('questions', collect($questions2));
echo "Siswa 2 Order: " . $exam2->questions->pluck('id')->implode(',') . "\n";
