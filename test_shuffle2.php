<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$exams = App\Models\Exam::where('acak_soal', true)->get();
foreach ($exams as $exam) {
    if ($exam->questions()->count() > 3) {
        echo "Exam ID: " . $exam->id . "\n";
        $attempts = App\Models\ExamAttempt::where('exam_id', $exam->id)->take(3)->get();
        if ($attempts->isEmpty()) {
            $attempts = [
                (object)['id' => 1], (object)['id' => 2], (object)['id' => 3]
            ];
        }
        foreach ($attempts as $attempt) {
            $questions = $exam->questions->all();
            mt_srand(crc32($attempt->id));
            shuffle($questions);
            mt_srand();
            echo "Attempt ID: " . $attempt->id . " -> Order: ";
            foreach($questions as $q) { echo $q->id . " "; }
            echo "\n";
        }
    }
}
