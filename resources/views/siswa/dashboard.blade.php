<x-app-layout>
    <x-slot name="header"><div><h2 class="text-xl font-bold text-slate-900">Dashboard Siswa</h2><p class="mt-0.5 text-sm text-slate-500">Pantau jadwal dan hasil ujian Anda.</p></div></x-slot>
    <div class="py-6"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-sm sm:p-7"><p class="text-sm font-medium text-slate-600">Selamat datang kembali</p><h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $siswa->nama ?: auth()->user()->name }}</h1><p class="mt-2 text-sm text-slate-600">{{ $siswa->kelasData?->nama_kelas ?? $siswa->kelas ?? 'Kelas belum ditentukan' }} <span class="mx-1 text-slate-300">·</span> NISN {{ $siswa->nisn ?: $siswa->nis }}</p></section>
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach ([['Ujian Hari Ini', $todayExams->count(), 'bg-blue-50', 'text-blue-700'], ['Ujian Mendatang', $upcomingExams->count(), 'bg-amber-50', 'text-amber-700'], ['Ujian Selesai', $completedExams->count(), 'bg-emerald-50', 'text-emerald-700']] as [$label, $count, $background, $color])
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $count }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $background }} {{ $color }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.8" d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm3 5h8M8 13h8M8 17h5"/></svg></span></div></div>
            @endforeach
        </section>
        @php
            $activityExam = $todayExams->first(function ($exam) {
                $attempt = $exam->examAttempts->first();

                return $attempt?->status === 'in_progress'
                    || ($exam->tanggal_selesai && now()->lt($exam->tanggal_selesai));
            }) ?? $upcomingExams->first();
        @endphp

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div><h2 class="font-bold text-slate-900">Aktivitas Ujian</h2><p class="mt-0.5 text-xs text-slate-500">Ujian yang paling relevan untuk Anda saat ini.</p></div>
                <a href="{{ route('siswa.ujian.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Buka Ujian Saya</a>
            </div>
            @if ($activityExam)
                @php
                    $activityAttempt = $activityExam->examAttempts->first();
                    $activityAction = $activityAttempt?->status === 'in_progress' ? 'Lanjutkan' : 'Lihat Detail';
                @endphp
                <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-blue-600">{{ $activityExam->mataPelajaran?->nama ?? 'Mata Pelajaran' }}</p><h3 class="mt-1 text-lg font-bold text-slate-900">{{ $activityExam->nama }}</h3><p class="mt-1 text-sm text-slate-500">{{ $activityExam->kelasData?->nama_kelas ?? $activityExam->kelas ?? '-' }} <span class="mx-1 text-slate-300">·</span> {{ $activityExam->tanggal_mulai?->format('d M Y, H:i') ?? 'Jadwal belum diatur' }} <span class="mx-1 text-slate-300">·</span> {{ $activityExam->durasi_menit }} menit</p></div>
                    <a href="{{ route('siswa.ujian.show', $activityExam) }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700">{{ $activityAction }}</a>
                </div>
            @else
                <p class="px-5 py-4 text-sm text-slate-500">Belum ada ujian yang perlu dikerjakan.</p>
            @endif
        </section>
    </div></div>
</x-app-layout>