<?php
$file = 'resources/views/exam-cards/index.blade.php';
$content = file_get_contents($file);

$search = "<x-slot name=\"header\">
        <div>
            <div class=\"flex items-center justify-between w-full\"><div><h2 class=\"text-xl font-bold text-slate-900\">Kartu Ujian</h2>
            <p class=\"mt-0.5 text-sm text-slate-500\">Pilih ujian dan kelas untuk mencetak kartu peserta.</p></div><a href=\"{{ route('kartu-ujian.settings') }}\" class=\"rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50\">Pengaturan Kartu Ujian</a></div>
    </x-slot>";
$replace = "<x-slot name=\"header\">
        <div>
            <h2 class=\"text-xl font-bold text-slate-900\">Kartu Ujian</h2>
            <p class=\"mt-0.5 text-sm text-slate-500\">Pilih ujian dan kelas untuk mencetak kartu peserta.</p>
        </div>
    </x-slot>";
$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);
