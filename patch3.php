<?php
$file = 'resources/views/hasil/show.blade.php';
$content = file_get_contents($file);

$search1 = '<th class="px-5 py-3">Tipe Soal</th>';
$replace1 = '<th class="px-5 py-3">Tipe Soal</th>'."\n".'                                <th class="px-5 py-3 w-1/3">Pertanyaan</th>';
$content = str_replace($search1, $replace1, $content);

$search2 = '<td class="px-5 py-4 whitespace-nowrap">{{ $ans->question->tipe === \'pilihan_ganda\' || $ans->question->tipe === \'pg\' ? \'Pilihan Ganda\' : \'Essay\' }}</td>';
$replace2 = '<td class="px-5 py-4 whitespace-nowrap">{{ $ans->question->tipe === \'pilihan_ganda\' || $ans->question->tipe === \'pg\' ? \'Pilihan Ganda\' : \'Essay\' }}</td>'."\n".'                                <td class="px-5 py-4"><div class="line-clamp-2 text-slate-600" title="{{ strip_tags($ans->question->pertanyaan) }}">{!! Str::limit(strip_tags($ans->question->pertanyaan), 100) !!}</div></td>';
$content = str_replace($search2, $replace2, $content);

file_put_contents($file, $content);
