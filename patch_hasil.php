<?php
$file = 'resources/views/hasil/show.blade.php';
$content = file_get_contents($file);

$search1 = "\$jawabanDisplay = strtoupper(\$ans->jawaban) . '. ' . Str::limit(\$ans->question->\$field, 50);";
$replace1 = "\$jawabanDisplay = '<strong>' . strtoupper(\$ans->jawaban) . '.</strong> ' . \$ans->question->\$field;";
$content = str_replace($search1, $replace1, $content);

$search2 = "\$kunciDisplay = strtoupper(\$ans->question->kunci) . '. ' . Str::limit(\$ans->question->\$field, 50);";
$replace2 = "\$kunciDisplay = '<strong>' . strtoupper(\$ans->question->kunci) . '.</strong> ' . \$ans->question->\$field;";
$content = str_replace($search2, $replace2, $content);

$search3 = "<td class=\"px-5 py-4\"><div class=\"line-clamp-2 text-slate-600\" title=\"{{ strip_tags(\$ans->question->pertanyaan) }}\">{!! Str::limit(strip_tags(\$ans->question->pertanyaan), 100) !!}</div></td>
                                    <td class=\"px-5 py-4\"><div class=\"font-bold text-slate-900\">{{ \$jawabanDisplay }}</div></td>
                                    <td class=\"px-5 py-4 text-slate-600\">{{ \$isPG ? \$kunciDisplay : '(Essay)' }}</td>";
$replace3 = "<td class=\"px-5 py-4\"><div class=\"max-h-20 overflow-hidden text-slate-600 text-sm\">{!! \$ans->question->pertanyaan !!}</div></td>
                                    <td class=\"px-5 py-4\"><div class=\"text-slate-900 text-sm\">{!! \$jawabanDisplay !!}</div></td>
                                    <td class=\"px-5 py-4 text-slate-600 text-sm\">{!! \$isPG ? \$kunciDisplay : '(Essay)' !!}</td>";
$content = str_replace($search3, $replace3, $content);

file_put_contents($file, $content);
