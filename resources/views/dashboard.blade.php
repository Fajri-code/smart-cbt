<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Dashboard
            </h2>
            <p class="mt-0.5 text-sm text-slate-500">
                Ringkasan dan monitoring sistem SMART CBT
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- =========================
                WELCOME
            ========================== --}}
            <div class="overflow-hidden rounded-2xl bg-white p-6 text-slate-900 shadow-sm border border-slate-200">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-sm font-medium text-slate-600">
                            Selamat datang kembali 👋
                        </p>

                        <h1 class="mt-1 text-2xl font-bold text-slate-900">
                            Administrator SMART CBT
                        </h1>

                        <p class="mt-2 max-w-xl text-sm text-slate-600">
                            Kelola data siswa, guru, kelas, mata pelajaran,
                            ujian, dan hasil ujian dari satu tempat.
                        </p>
                    </div>

                    <div class="hidden sm:flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 ring-1 ring-slate-200">
                        <svg class="h-8 w-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.291 9 11.622C17.176 22.291 21 17.591 21 12c0-1.39-.236-2.725-.67-3.968"/>
                        </svg>
                    </div>

                </div>
            </div>


            {{-- =========================
                STATISTIK
            ========================== --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- SISWA --}}
                <a href="{{ url('/siswa') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Total Siswa
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ number_format($totalSiswa) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Peserta terdaftar
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M17 20h5v-2a4 4 0 00-4-4h-1m-3 6H6a2 2 0 01-2-2v-1a4 4 0 014-4h6a4 4 0 014 4v1a2 2 0 01-2 2zm-3-10a4 4 0 100-8 4 4 0 000 8zm8 2a3 3 0 100-6 3 3 0 000 6z"/>
                            </svg>
                        </div>

                    </div>

                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-blue-600">
                        Kelola Siswa
                        <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                </a>


                {{-- GURU --}}
                <a href="{{ url('/guru') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Total Guru
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ number_format($totalGuru) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Pengajar terdaftar
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M12 14a5 5 0 100-10 5 5 0 000 10zm-7 7a7 7 0 0114 0M19 8h2m-1-1v2"/>
                            </svg>
                        </div>

                    </div>

                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-emerald-600">
                        Kelola Guru
                        <svg class="h-3.5 w-3.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                </a>


                {{-- KELAS --}}
                <a href="{{ url('/kelas') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-200 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Total Kelas
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ number_format($totalKelas) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Kelas terdaftar
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 17A2.5 2.5 0 004 19.5V5a2 2 0 012-2h14v14H6.5z"/>
                            </svg>
                        </div>

                    </div>

                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-violet-600">
                        Kelola Kelas
                        <svg class="h-3.5 w-3.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                </a>


                {{-- UJIAN --}}
                <a href="{{ url('/ujian') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Paket Ujian
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ number_format($totalUjian) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Total paket ujian
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M9 5h6m-8 4h10M7 13h10m-8 4h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                        </div>

                    </div>

                    <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-amber-600">
                        Kelola Ujian
                        <svg class="h-3.5 w-3.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                </a>

            </div>


            {{-- =========================
                BAGIAN BAWAH
            ========================== --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- UJIAN TERBARU --}}
                <div class="lg:col-span-2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">

                        <div>
                            <h3 class="font-bold text-slate-900">
                                Paket Ujian Terbaru
                            </h3>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Ujian yang baru dibuat di sistem
                            </p>
                        </div>

                        <a href="{{ url('/ujian') }}"
                           class="text-xs font-bold text-blue-600 hover:text-blue-800">
                            Lihat Semua
                        </a>

                    </div>


                    <div class="divide-y divide-slate-100">

                        @forelse ($ujianTerbaru as $ujian)

                            <div class="flex items-center justify-between gap-4 px-5 py-4">

                                <div class="flex min-w-0 items-center gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">

                                        <svg class="h-5 w-5"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="1.8"
                                                  d="M9 5h6m-8 4h10M7 13h10m-8 4h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                        </svg>

                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-bold text-slate-800">
                                            {{ $ujian->nama }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ $ujian->mataPelajaran?->nama ?? 'Mata pelajaran belum diatur' }}
                                            <span class="mx-1 text-slate-300">•</span>
                                            Kelas {{ $ujian->kelasData?->nama_kelas ?? $ujian->kelas ?? '-' }}
                                        </p>

                                    </div>

                                </div>


                                <div class="shrink-0 text-right">

                                    @if ($ujian->status === 'aktif')

                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>

                                    @elseif ($ujian->status === 'selesai')

                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">
                                            Selesai
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">
                                            Draft
                                        </span>

                                    @endif

                                    <p class="mt-1 text-[10px] text-slate-400">
                                        {{ $ujian->created_at?->format('d/m/Y H:i') }}
                                    </p>

                                </div>

                            </div>

                        @empty

                            <div class="px-5 py-10 text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <svg class="h-6 w-6"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="1.8"
                                              d="M9 5h6m-8 4h10M7 13h10m-8 4h6"/>
                                    </svg>
                                </div>

                                <p class="mt-3 text-sm font-bold text-slate-700">
                                    Belum ada paket ujian
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Paket ujian yang dibuat akan muncul di sini.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>



                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>