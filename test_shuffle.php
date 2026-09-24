<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$exam = App\Models\Exam::where('acak_soal', true)->first();
$attempts = App\Models\ExamAttempt::where('exam_id', $exam->id)->take(2)->get();
foreach ($attempts as $attempt) {
    $questions = $exam->questions->all();
    mt_srand(crc32($attempt->id));
    shuffle($questions);
    mt_srand();
    echo "Attempt ID: " . $attempt->id . " -> Order: ";
    foreach($questions as $q) { echo $q->id . " "; }
    echo "\n";
}
