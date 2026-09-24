<?php
$file = 'app/Http/Controllers/ExportController.php';
$content = file_get_contents($file);

$search3 = "                'Tipe Soal' => \$ans->question->tipe === 'pilihan_ganda' || \$ans->question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay',";
$replace3 = "                'Tipe Soal' => \$ans->question->tipe === 'pilihan_ganda' || \$ans->question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay',\n                'Pertanyaan' => strip_tags(\$ans->question->pertanyaan),";
$content = str_replace($search3, $replace3, $content);

file_put_contents($file, $content);
