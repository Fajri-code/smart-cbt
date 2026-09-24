<?php
$file = 'app/Http/Controllers/ExportController.php';
$content = file_get_contents($file);

$search4 = "        foreach (\$answers as \$ans) {
            \$isCorrect = \$ans->is_correct;
            \$hasAnswered = !empty(\$ans->jawaban);";
$replace4 = "        foreach (\$answers as \$ans) {
            \$isCorrect = \$ans->is_correct;
            \$hasAnswered = !empty(\$ans->jawaban);
            \$isPG = \$ans->question->tipe === 'pilihan_ganda' || \$ans->question->tipe === 'pg';

            \$jawabanTextExp = \$ans->jawaban ?: '-';
            if (\$hasAnswered && \$isPG && in_array(strtolower(\$ans->jawaban), ['a', 'b', 'c', 'd', 'e'])) {
                \$opsiField = 'opsi_' . strtolower(\$ans->jawaban);
                \$jawabanTextExp = strtoupper(\$ans->jawaban) . '. ' . strip_tags(\$ans->question->\$opsiField);
            }

            \$kunciTextExp = \$ans->question->kunci ?: '-';
            if (\$isPG && !empty(\$ans->question->kunci) && in_array(strtolower(\$ans->question->kunci), ['a', 'b', 'c', 'd', 'e'])) {
                \$kunciField = 'opsi_' . strtolower(\$ans->question->kunci);
                \$kunciTextExp = strtoupper(\$ans->question->kunci) . '. ' . strip_tags(\$ans->question->\$kunciField);
            }";
$content = str_replace($search4, $replace4, $content);

$search5 = "                'Jawaban Siswa' => \$ans->jawaban ?: '-',
                'Kunci' => \$ans->question->tipe === 'pilihan_ganda' || \$ans->question->tipe === 'pg' ? (\$ans->question->kunci ?: '-') : '(Essay)',";
$replace5 = "                'Jawaban Siswa' => \$jawabanTextExp,
                'Kunci' => \$isPG ? \$kunciTextExp : '(Essay)',";
$content = str_replace($search5, $replace5, $content);

file_put_contents($file, $content);
