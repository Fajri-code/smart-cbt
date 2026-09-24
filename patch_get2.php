<?php
$file2 = "resources/views/exam-cards/preview.blade.php";
$content2 = file_get_contents($file2);
$content2 = str_replace('@csrf', '', $content2);
file_put_contents($file2, $content2);
