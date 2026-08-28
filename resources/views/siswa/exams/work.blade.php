<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>{{ $exam->nama }} - SMART CBT</title>@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <header class="sticky top-0 z-20 border-b border-slate-200 bg-white shadow-sm"><div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6"><div class="min-w-0"><p class="truncate text-xs font-bold uppercase tracking-wide text-blue-600">{{ $exam->mataPelajaran?->nama }}</p><h1 class="truncate text-lg font-black text-slate-900">{{ $exam->nama }}</h1></div><div class="shrink-0 rounded-xl bg-blue-50 px-4 py-2 text-center"><span class="block text-[10px] font-bold uppercase tracking-wider text-blue-600">Sisa waktu</span><strong id="countdown" class="text-xl font-black tabular-nums text-blue-800">--:--</strong></div></div></header>
    <main class="mx-auto grid max-w-6xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[1fr_260px]">
        <form id="exam-form" method="POST" action="{{ route('siswa.ujian.submit', $exam) }}" class="space-y-5">@csrf
            @foreach ($exam->questions as $question)
                <section id="question-{{ $loop->iteration }}" class="question-panel rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" data-question="{{ $loop->iteration }}" style="display: {{ $loop->first ? 'block' : 'none' }}"><div class="flex items-center justify-between border-b border-slate-100 pb-4"><span class="text-sm font-bold text-slate-500">Soal {{ $loop->iteration }} dari {{ $exam->questions->count() }}</span><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ strtoupper($question->tipe) }}</span></div><div class="prose prose-slate mt-6 max-w-none"><p class="whitespace-pre-line text-lg font-semibold leading-relaxed text-slate-900">{{ $question->pertanyaan }}</p></div>@if ($question->tipe === 'pg')<div class="mt-7 space-y-3">@foreach (['a','b','c','d','e'] as $option) @if ($question->{'opsi_'.$option})<label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 hover:border-blue-400 hover:bg-blue-50/40"><input type="radio" name="answers[{{ $question->id }}]" value="{{ strtoupper($option) }}" class="mt-1 text-blue-600" @checked(($answers[$question->id] ?? '') === strtoupper($option))><span><strong class="mr-2 text-blue-700">{{ strtoupper($option) }}.</strong>{{ $question->{'opsi_'.$option} }}</span></label>@endif @endforeach</div>@else<label class="mt-6 block"><span class="mb-2 block text-sm font-semibold text-slate-700">Jawaban Anda</span><textarea name="answers[{{ $question->id }}]" rows="5" class="w-full rounded-xl border-slate-300" placeholder="Tulis jawaban Anda...">{{ $answers[$question->id] ?? '' }}</textarea></label>@endif</section>
            @endforeach
            <div class="flex items-center justify-between gap-3"><button type="button" id="previous" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 disabled:cursor-not-allowed disabled:opacity-40">Sebelumnya</button><button type="button" id="next" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700">Berikutnya</button><button type="button" id="finish" class="hidden rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Kumpulkan Ujian</button></div>
        </form>
        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="font-bold text-slate-900">Navigasi Soal</h2><div class="mt-4 grid grid-cols-5 gap-2">@foreach ($exam->questions as $question)<button type="button" class="question-number h-9 rounded-lg border border-slate-200 text-sm font-bold text-slate-600 hover:border-blue-400" data-target="{{ $loop->iteration }}">{{ $loop->iteration }}</button>@endforeach</div><p class="mt-5 text-xs leading-relaxed text-slate-500">Jawaban tersimpan di formulir ini selama Anda berpindah soal. Saat waktu habis, ujian akan dikumpulkan otomatis oleh sistem.</p></aside>
    </main>
    <script>
        const panels = [...document.querySelectorAll('.question-panel')];
        const numbers = [...document.querySelectorAll('.question-number')];
        const previous = document.getElementById('previous');
        const next = document.getElementById('next');
        const finish = document.getElementById('finish');
        const form = document.getElementById('exam-form');
        let current = 1;
        const deadline = new Date('{{ $attempt->started_at->copy()->addMinutes($exam->durasi_menit)->min($exam->tanggal_selesai ?? $attempt->started_at->copy()->addMinutes($exam->durasi_menit))->toIso8601String() }}').getTime();
        function showQuestion(number) { current = number; panels.forEach((panel, index) => panel.style.display = index + 1 === current ? 'block' : 'none'); numbers.forEach((button, index) => button.classList.toggle('border-blue-600', index + 1 === current)); previous.disabled = current === 1; next.classList.toggle('hidden', current === panels.length); finish.classList.toggle('hidden', current !== panels.length); }
        let saveTimer;
        let isSubmitting = false;
        function saveAnswers() {
            const answers = Object.fromEntries(new FormData(form).entries());
            const groupedAnswers = {};
            Object.entries(answers).forEach(([key, value]) => { const match = key.match(/^answers\[(\d+)\]$/); if (match) groupedAnswers[match[1]] = value; });
            return fetch('{{ route('siswa.ujian.answers', $exam) }}', { method: 'POST', keepalive: true, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ answers: groupedAnswers }) });
        }
        function queueSave() { clearTimeout(saveTimer); saveTimer = setTimeout(saveAnswers, 500); }
        function submitExam() { if (confirm('Kumpulkan ujian sekarang? Jawaban yang sudah dikirim tidak dapat diubah.')) { isSubmitting = true; saveAnswers().finally(() => form.submit()); } }
        previous.addEventListener('click', () => showQuestion(current - 1)); next.addEventListener('click', () => showQuestion(current + 1)); finish.addEventListener('click', submitExam); numbers.forEach(button => button.addEventListener('click', () => showQuestion(Number(button.dataset.target))));
        form.addEventListener('input', queueSave); form.addEventListener('change', queueSave); setInterval(saveAnswers, 20000);
        window.addEventListener('pagehide', () => { if (isSubmitting) return; saveAnswers().finally(() => fetch('{{ route('siswa.ujian.leave', $exam) }}', { method: 'POST', keepalive: true, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })); });
        const timer = setInterval(() => { const remaining = Math.max(0, deadline - Date.now()); const minutes = Math.floor(remaining / 60000); const seconds = Math.floor(remaining % 60000 / 1000); document.getElementById('countdown').textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`; if (remaining === 0) { clearInterval(timer); form.submit(); } }, 1000); showQuestion(1);
    </script>
</body>
</html>