<?php
$file = 'resources/views/guru/bank-soal/show.blade.php';
$content = file_get_contents($file);

$search = '<p><span class="font-semibold">{{ strtoupper($option) }}.</span> {!! $question->{\'opsi_\'.$option} !!}</p>';
$replace = '<div><span class="font-semibold">{{ strtoupper($option) }}.</span> {!! $question->{\'opsi_\'.$option} !!}</div>';
$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);
