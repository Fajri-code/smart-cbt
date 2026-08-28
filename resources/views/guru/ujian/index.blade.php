<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                {{-- Breadcrumb --}}
                <nav class="mb-1 flex items-center gap-2 text-xs text-slate-500">
                    <a href="{{ route('guru.dashboard') }}" class="hover:text-slate-800">Dashboard</a>
                    <span>/</span>
                    <span class="font-medium text-slate-800">Ujian Saya</span>
                </nav>
                <h2 class="text-xl font-bold leading-tight text-slate-900">
                    Daftar Ujian Saya
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Kelola soal ujian, penerbitan token, dan pantau status pelaksanaan ujian yang ditugaskan kepada Anda.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('guru.bank.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Bank Soal
                </a>

                <a href="{{ route('guru.monitoring.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-amber-400 px-3.5 py-2 text-xs font-bold text-slate-950 shadow-sm transition hover:bg-amber-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Monitoring Siswa
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Ujian Saya</p>
                        <p class="text-2xl font-black text-slate-900">{{ $totalExams ?? $exams->total() }} <span class="text-xs font-normal text-slate-500">paket ujian</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ujian Aktif / Siap</p>
                        <p class="text-2xl font-black text-emerald-600">{{ $activeExams ?? 0 }} <span class="text-xs font-normal text-slate-500">diterbitkan</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Draft / Penyusunan</p>
                        <p class="text-2xl font-black text-amber-600">{{ $draftExams ?? 0 }} <span class="text-xs font-normal text-slate-500">perlu dilengkapi</span></p>
                    </div>
                </div>
            </div>

            {{-- Table List Ujian --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="font-bold text-slate-900">Daftar Paket Ujian</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Informasi Ujian</th>
                                <th class="px-6 py-4">Mata Pelajaran & Kelas</th>
                                <th class="px-6 py-4 text-center">Soal</th>
                                <th class="px-6 py-4 text-center">Token</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($exams as $exam)
                                @php
                                    $isPublished = $exam->status === 'aktif' && $exam->token_aktif;
                                @endphp
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('guru.soal.index', $exam) }}" class="font-bold text-slate-900 hover:text-blue-600">
                                            {{ $exam->nama }}
                                        </a>
                                        <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                            <span>{{ $exam->durasi_menit }} Menit</span>
                                            <span>&bull;</span>
                                            <span>Jadwal: {{ $exam->tanggal_mulai?->format('d/m/Y H:i') ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-800">{{ $exam->mataPelajaran?->nama ?? '-' }}</p>
                                        <p class="text-xs text-slate-500">Kelas: {{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($exam->questions_count > 0)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                                                {{ $exam->questions_count }} Soal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-600">
                                                0 Soal (Kosong)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($isPublished)
                                            <span class="font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs">
                                                {{ $exam->token }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">Belum Terbit</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($isPublished)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Tombol Kelola Soal (Alur Utama) --}}
                                            <a href="{{ route('guru.soal.index', $exam) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Kelola Soal
                                            </a>

                                            {{-- Tombol Token --}}
                                            <a href="{{ route('guru.token.show', $exam) }}"
                                               class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                               title="Atur Token Ujian">
                                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                </svg>
                                                Token
                                            </a>

                                            {{-- Tombol Detail --}}
                                            <a href="{{ route('guru.ujian.show', $exam) }}"
                                               class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                               title="Lihat Detail & Penugasan">
                                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        <p class="font-medium text-slate-700">Belum ada paket ujian yang ditugaskan kepada Anda.</p>
                                        <p class="mt-1 text-xs text-slate-400">Hubungi Administrator untuk penetapan jadwal & paket ujian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 p-6">
                    {{ $exams->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

