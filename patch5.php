<?php
$file = 'resources/views/hasil/show.blade.php';
$content = file_get_contents($file);

$search1 = "@php
                                    \$isCorrect = \$ans->is_correct;
                                    \$hasAnswered = !empty(\$ans->jawaban);";
$replace1 = "@php
                                    \$isCorrect = \$ans->is_correct;
                                    \$hasAnswered = !empty(\$ans->jawaban);
                                    \$isPG = \$ans->question->tipe === 'pilihan_ganda' || \$ans->question->tipe === 'pg';
                                    
                                    \$jawabanDisplay = \$ans->jawaban ?: '-';
                                    if (\$hasAnswered && \$isPG && in_array(strtolower(\$ans->jawaban), ['a','b','c','d','e'])) {
                                        \$field = 'opsi_' . strtolower(\$ans->jawaban);
                                        \$jawabanDisplay = strtoupper(\$ans->jawaban) . '. ' . Str::limit(strip_tags(\$ans->question->\$field), 50);
                                    }

                                    \$kunciDisplay = \$ans->question->kunci ?: '-';
                                    if (\$isPG && !empty(\$ans->question->kunci) && in_array(strtolower(\$ans->question->kunci), ['a','b','c','d','e'])) {
                                        \$field = 'opsi_' . strtolower(\$ans->question->kunci);
                                        \$kunciDisplay = strtoupper(\$ans->question->kunci) . '. ' . Str::limit(strip_tags(\$ans->question->\$field), 50);
                                    }";
$content = str_replace($search1, $replace1, $content);

$search2 = "<td class=\"px-5 py-4 text-center font-bold text-slate-900\">{{ \$ans->jawaban ?: '-' }}</td>";
$replace2 = "<td class=\"px-5 py-4\"><div class=\"font-bold text-slate-900\">{{ \$jawabanDisplay }}</div></td>";
$content = str_replace($search2, $replace2, $content);

$search3 = "<td class=\"px-5 py-4 text-center text-slate-600\">{{ \$ans->question->tipe === 'pilihan_ganda' || \$ans->question->tipe === 'pg' ? (\$ans->question->kunci ?: '-') : '(Essay)' }}</td>";
$replace3 = "<td class=\"px-5 py-4 text-slate-600\">{{ \$isPG ? \$kunciDisplay : '(Essay)' }}</td>";
$content = str_replace($search3, $replace3, $content);

file_put_contents($file, $content);
