<?php
$file = "resources/views/exam-cards/index.blade.php";
$content = file_get_contents($file);
$content = str_replace('formmethod="POST"', 'formmethod="GET"', $content);
file_put_contents($file, $content);

$file2 = "resources/views/exam-cards/preview.blade.php";
$content2 = file_get_contents($file2);
$content2 = str_replace('method="POST"', 'method="GET"', $content2);
file_put_contents($file2, $content2);
