<?php
function replaceInFile($file, $search, $replace) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace($search, $replace, $content);
        file_put_contents($file, $content);
    }
}

// 1. guru/soal/index.blade.php
$file = "resources/views/guru/soal/index.blade.php";
replaceInFile($file, "{{ \$question->pertanyaan }}", "{!! \$question->pertanyaan !!}");
replaceInFile($file, "{{ \$question->{'opsi_'.\$option} }}", "{!! \$question->{'opsi_'.\$option} !!}");

// 2. guru/bank-soal/show.blade.php
$file = "resources/views/guru/bank-soal/show.blade.php";
replaceInFile($file, "{{ Str::limit(\$question->pertanyaan, 140) }}", "{!! Str::limit(strip_tags(\$question->pertanyaan), 140) !!}");
replaceInFile($file, "{{ \$question->{'opsi_'.\$option} }}", "{!! \$question->{'opsi_'.\$option} !!}");

// 3. siswa/exams/work_new.blade.php
$file = "resources/views/siswa/exams/work_new.blade.php";
replaceInFile($file, "{{ \$question->pertanyaan }}", "{!! \$question->pertanyaan !!}");
replaceInFile($file, "{{ \$question->{'opsi_'.\$option} }}", "{!! \$question->{'opsi_'.\$option} !!}");

// 4. siswa/exams/work.blade.php
$file = "resources/views/siswa/exams/work.blade.php";
replaceInFile($file, "{{ \$question->pertanyaan }}", "{!! \$question->pertanyaan !!}");
replaceInFile($file, "{{ \$question->{'opsi_'.\$option} }}", "{!! \$question->{'opsi_'.\$option} !!}");

// 5. hasil/show.blade.php (already uses strip_tags, but wait, the question text is not rendered properly?)
$file = "resources/views/hasil/show.blade.php";
replaceInFile($file, "strip_tags(\$ans->question->\$field)", "\$ans->question->\$field"); // Let's keep strip_tags for Excel, but for view we might want to strip tags but keep math? Wait, if we use {!! !!} in show.blade.php, we shouldn't strip_tags if we want to show math.

