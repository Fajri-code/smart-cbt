<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                {{-- Breadcrumb --}}
                <nav class="mb-1 flex items-center gap-2 text-xs text-slate-500">
                    <a href="{{ route('guru.dashboard') }}" class="hover:text-slate-800">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('guru.ujian.index') }}" class="hover:text-slate-800">Ujian Saya</a>
                    <span>/</span>
                    <span class="font-medium text-slate-800">Token Ujian: {{ $exam->nama }}</span>
                </nav>
                <h2 class="text-xl font-bold leading-tight text-slate-900">
                    Token Akses Ujian
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Gunakan token ini untuk dibagikan kepada peserta ujian agar dapat memulai pengerjaan.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('guru.soal.index', $exam) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Kelola Soal
                </a>

                <a href="{{ route('guru.monitoring.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-amber-400 px-3.5 py-2 text-xs font-bold text-slate-950 shadow-sm transition hover:bg-amber-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Monitoring Peserta &rarr;
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ copied: false }">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">

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

            {{-- Token Display Card (Hero for Classroom Projection) --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-lg sm:p-10">
                <div class="inline-flex items-center gap-2 rounded-full px-3.5 py-1 text-xs font-bold {{ $exam->token_aktif ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                    <span class="h-2 w-2 rounded-full {{ $exam->token_aktif ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                    {{ $exam->token_aktif ? 'TOKEN AKTIF' : 'TOKEN NONAKTIF' }}
                </div>

                <h3 class="mt-3 text-lg font-bold text-slate-900">{{ $exam->nama }}</h3>
                <p class="text-xs text-slate-500">
                    {{ $exam->mataPelajaran?->nama ?? '-' }} &bull; Kelas: {{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}
                </p>

                {{-- Big Token Box --}}
                <div class="my-6 flex flex-col items-center justify-center">
                    <div class="relative inline-block rounded-2xl border-2 {{ $exam->token_aktif ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-300 bg-slate-50' }} px-8 py-5 shadow-inner">
                        <span id="exam-token" class="font-mono text-4xl font-black tracking-[0.3em] sm:text-5xl {{ $exam->token_aktif ? 'text-emerald-700' : 'text-slate-400' }}">
                            {{ $exam->token ?? 'BELUM ADA' }}
                        </span>
                    </div>

                    @if ($exam->token)
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $exam->token }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span x-text="copied ? '✓ Berhasil Disalin!' : 'Salin Kode Token'"></span>
                        </button>
                    @endif
                </div>

                {{-- Expiry / Schedule Info --}}
                <div class="mx-auto grid max-w-md gap-3 rounded-xl border border-slate-100 bg-slate-50/70 p-4 text-xs text-slate-600 sm:grid-cols-2">
                    <div>
                        <span class="text-slate-400">Durasi Pengerjaan:</span>
                        <p class="font-bold text-slate-800">{{ $exam->durasi_menit }} Menit</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Jadwal Ujian:</span>
                        <p class="font-bold text-slate-800">{{ $exam->tanggal_mulai?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Waktu Dibuat:</span>
                        <p class="font-medium text-slate-700">{{ $exam->token_dibuat_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Masa Berlaku Token:</span>
                        <p class="font-medium text-slate-700">{{ $exam->token_aktif && $exam->token_kedaluwarsa_at ? $exam->token_kedaluwarsa_at->format('d/m/Y H:i:s') : '-' }}</p>
                    </div>
                </div>

                {{-- Action Controls --}}
                <div class="mt-8 flex flex-wrap justify-center gap-3 border-t border-slate-100 pt-6">
                    <form method="POST" action="{{ route('guru.token.generate', $exam) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-600/20 transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ $exam->token ? 'Regenerate / Ganti Token Baru' : 'Generate Token Baru' }}
                        </button>
                    </form>

                    @if ($exam->token)
                        <form method="POST" action="{{ route('guru.token.toggle', $exam) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                {{ $exam->token_aktif ? 'Nonaktifkan Token Sementara' : 'Aktifkan Token Kembali' }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('guru.monitoring.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-slate-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Buka Live Monitoring Siswa
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

