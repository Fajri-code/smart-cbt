<?php
$file = 'resources/views/exam-cards/index.blade.php';
$content = file_get_contents($file);

$search = "<p class=\"mt-0.5 text-sm text-slate-500\">Pilih ujian dan kelas untuk mencetak kartu peserta.</p>
        </div>
    </x-slot>";
$replace = "<p class=\"mt-0.5 text-sm text-slate-500\">Pilih ujian dan kelas untuk mencetak kartu peserta.</p>
        </div>
        <a href=\"{{ route('kartu-ujian.settings') }}\" class=\"rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800\">Pengaturan Template Kartu</a>
    </x-slot>";
$content = str_replace($search, $replace, $content);

$search2 = "<div>
            <h2 class=\"text-xl font-bold text-slate-900\">Kartu Ujian</h2>";
$replace2 = "<div class=\"flex w-full items-center justify-between\">\n            <div>\n            <h2 class=\"text-xl font-bold text-slate-900\">Kartu Ujian</h2>";
$content = str_replace($search2, $replace2, $content);

$search3 = "Pengaturan Template Kartu</a>
    </x-slot>";
$replace3 = "Pengaturan Template Kartu</a>\n        </div>\n    </x-slot>";
$content = str_replace($search3, $replace3, $content);

file_put_contents($file, $content);
