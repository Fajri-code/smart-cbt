<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Riwayat Ujian</h2>
            <p class="mt-0.5 text-sm text-slate-500">Seluruh ujian yang pernah Anda ikuti.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end sm:justify-between">
                <div><span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $siswa->kelasData?->nama_kelas ?? $siswa->kelas ?? 'Kelas belum ditentukan' }}</span><h1 class="mt-2 text-2xl font-black text-slate-900">Semua Riwayat Pengerjaan</h1><p class="mt-1 text-sm text-slate-500">Catatan seluruh ujian yang pernah Anda ikuti.</p></div>
                <p class="text-sm text-slate-500">{{ $exams->count() }} ujian tercatat</p>
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="hidden overflow-x-auto md:block"><table class="min-w-full divide-y divide-slate-200 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Ujian</th><th class="px-5 py-3">Mata Pelajaran</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Periode</th><th class="px-5 py-3">Mulai</th><th class="px-5 py-3">Selesai</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">
                    @forelse ($exams as $exam)
                        @php($attempt = $exam->examAttempts->first())
                        <tr class="hover:bg-slate-50"><td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $exam->nama }}</td><td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $exam->mataPelajaran?->nama ?? '-' }}</td><td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</td><td class="whitespace-nowrap px-5 py-4 text-slate-600"><div>{{ $exam->tahun_ajaran ?? '-' }}</div><div class="text-xs text-slate-400">{{ $exam->semester ? ucfirst($exam->semester) : 'Semester belum diatur' }}</div></td><td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $attempt?->started_at?->format('d/m/Y H:i') ?? '-' }}</td><td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $attempt?->submitted_at?->format('d/m/Y H:i') ?? '-' }}</td><td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $attempt?->status === 'expired' ? 'Waktu Habis' : ($attempt?->status === 'submitted' ? 'Selesai' : ucfirst(str_replace('_', ' ', $attempt?->status ?? '-'))) }}</span></td><td class="whitespace-nowrap px-5 py-4"><a href="{{ route('siswa.ujian.show', $exam) }}" class="font-semibold text-blue-600 hover:text-blue-800">Lihat Detail</a></td></tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-12 text-center text-slate-500">Belum ada riwayat ujian.</td></tr>
                    @endforelse
                </tbody></table></div>
                @if ($exams->isNotEmpty())
                    <div class="space-y-3 p-4 md:hidden">
                        @foreach ($exams as $exam)
                            @php($attempt = $exam->examAttempts->first())
                            <article class="rounded-xl border border-slate-200 p-4"><div class="flex items-start justify-between gap-3"><div><p class="font-bold text-slate-900">{{ $exam->nama }}</p><p class="mt-1 text-xs text-blue-600">{{ $exam->mataPelajaran?->nama ?? '-' }}</p></div><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">{{ $attempt?->status === 'expired' ? 'Waktu Habis' : ($attempt?->status === 'submitted' ? 'Selesai' : ucfirst(str_replace('_', ' ', $attempt?->status ?? '-'))) }}</span></div><div class="mt-3 grid grid-cols-2 gap-3 text-xs text-slate-500"><div><span class="block text-slate-400">Kelas</span>{{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</div><div><span class="block text-slate-400">Periode</span>{{ $exam->tahun_ajaran ?? '-' }} · {{ $exam->semester ? ucfirst($exam->semester) : 'Belum diatur' }}</div><div><span class="block text-slate-400">Mulai</span>{{ $attempt?->started_at?->format('d/m/Y H:i') ?? '-' }}</div><div><span class="block text-slate-400">Selesai</span>{{ $attempt?->submitted_at?->format('d/m/Y H:i') ?? '-' }}</div></div><a href="{{ route('siswa.ujian.show', $exam) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-blue-200 px-3 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-50">Lihat Detail</a></article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
