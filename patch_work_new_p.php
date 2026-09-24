<?php
$file = 'resources/views/siswa/exams/work_new.blade.php';
$content = file_get_contents($file);

$search = '<p class="whitespace-pre-line text-[15px] font-semibold leading-relaxed text-slate-900 sm:text-lg">';
$replace = '<div class="whitespace-pre-line text-[15px] font-semibold leading-relaxed text-slate-900 sm:text-lg">';
$content = str_replace($search, $replace, $content);

$content = str_replace('</p>
                            </div>

                            @if ($question->tipe === \'pg\')', '</div>
                            </div>

                            @if ($question->tipe === \'pg\')', $content);

file_put_contents($file, $content);
