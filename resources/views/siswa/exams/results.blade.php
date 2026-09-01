<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Hasil / Nilai</h2>
            <p class="mt-0.5 text-sm text-slate-500">Ringkasan nilai dari ujian yang telah Anda selesaikan.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Ujian Selesai</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $totalResults }}</p><p class="mt-1 text-xs text-slate-500">Hasil pengerjaan</p></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Nilai</p><p class="mt-2 text-3xl font-black text-blue-700">{{ $averageScore !== null ? number_format($averageScore, 2) : '-' }}</p><p class="mt-1 text-xs text-slate-500">Dari nilai yang tersedia</p></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Nilai Tertinggi</p><p class="mt-2 text-3xl font-black text-emerald-600">{{ $highestScore !== null ? number_format($highestScore, 2) : '-' }}</p><p class="mt-1 text-xs text-slate-500">Dari nilai yang tersedia</p></div>
            </div>

            <div class="flex items-end justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div><span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $siswa->nama }}</span><h1 class="mt-2 text-2xl font-black text-slate-900">Ringkasan Nilai</h1><p class="mt-1 text-sm text-slate-500">Performa dari ujian yang telah Anda selesaikan.</p></div><a href="{{ route('siswa.riwayat') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">Lihat Riwayat</a></div>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($exams as $exam)
                    @php($attempt = $exam->examAttempts->first())
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-wider text-blue-600">{{ $exam->mataPelajaran?->nama ?? 'Mata Pelajaran' }}</p><h2 class="mt-1 text-lg font-bold text-slate-900">{{ $exam->nama }}</h2><p class="mt-1 text-xs text-slate-500">{{ $exam->tahun_ajaran ?? 'Periode belum diatur' }} <span class="mx-1 text-slate-300">·</span> {{ $exam->semester ? ucfirst($exam->semester) : 'Semester belum diatur' }}</p></div><span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ $attempt?->status === 'expired' ? 'Waktu Habis' : 'Selesai' }}</span></div><div class="mt-5 flex items-end justify-between border-t border-slate-100 pt-4"><div><p class="text-xs text-slate-400">Nilai</p><p class="mt-1 text-4xl font-black text-slate-900">{{ $attempt?->nilai_akhir !== null ? number_format($attempt->nilai_akhir, 2) : '-' }}</p></div><div class="text-right"><p class="text-xs text-slate-400">Tanggal</p><p class="mt-1 text-sm font-semibold text-slate-700">{{ $attempt?->submitted_at?->format('d/m/Y') ?? '-' }}</p><a href="{{ route('siswa.ujian.result', $exam) }}" class="mt-2 inline-block text-sm font-bold text-blue-600 hover:text-blue-800">Lihat Detail</a></div></div></article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500 md:col-span-2">Belum ada nilai ujian yang tersedia.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
