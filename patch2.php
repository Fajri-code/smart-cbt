<?php
$file = 'resources/views/guru/monitoring/index.blade.php';
$content = file_get_contents($file);

$search1 = "@if (\$status === 'completed')<th class=\"px-6 py-3\">Nilai</th>@endif @endif</tr></thead>";
$replace1 = "@if (\$status === 'completed')<th class=\"px-6 py-3\">Nilai</th><th class=\"px-6 py-3 text-right\">Aksi</th>@elseif (\$status === 'in_progress')<th class=\"px-6 py-3 text-right\">Aksi</th>@endif @endif</tr></thead>";
$content = str_replace($search1, $replace1, $content);

$search2 = "@if (\$status === 'completed')<td class=\"px-6 py-4\">{{ \$attempt->nilai_akhir ?? 'Belum Dinilai' }}</td>@endif</tr>";
$replace2 = "@if (\$status === 'completed')<td class=\"px-6 py-4\">{{ \$attempt->nilai_akhir ?? 'Belum Dinilai' }}</td><td class=\"px-6 py-4 text-right\"><a href=\"{{ route('guru.hasil.show', \$attempt->id) }}\" class=\"inline-flex items-center gap-1 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50\">Rekap Jawaban</a></td>@elseif (\$status === 'in_progress')<td class=\"px-6 py-4 text-right\"><a href=\"{{ route('guru.hasil.show', \$attempt->id) }}\" class=\"inline-flex items-center gap-1 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50\">Intip Jawaban</a></td>@endif</tr>";
$content = str_replace($search2, $replace2, $content);

file_put_contents($file, $content);
