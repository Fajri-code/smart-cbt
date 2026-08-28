<div x-data="{ open: false, cbtOpen: true }" @keydown.escape.window="open = false">

    {{-- Mobile Menu Button --}}
    <button
        type="button"
        class="fixed left-4 top-4 z-30 inline-flex items-center justify-center rounded-lg bg-slate-900 p-2.5 text-white shadow-lg transition hover:bg-slate-700 lg:hidden"
        @click="open = true"
        aria-label="Buka menu"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- Mobile Overlay --}}
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
        @click="open = false"
    ></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-slate-950 text-slate-300 shadow-2xl transition-transform duration-300 lg:translate-x-0"
        :class="{ 'translate-x-0': open }"
    >

        {{-- Logo --}}
        <div class="flex h-24 items-center justify-between border-b border-white/10 px-6">

            <a
                href="{{ Auth::user()->isGuru() ? route('guru.dashboard') : route('dashboard') }}"
                class="flex items-center gap-3"
                @click="open = false"
            >
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400 text-lg font-black text-slate-950">
                    S
                </span>

                <span>
                    <span class="block text-lg font-black tracking-[0.18em] text-white">
                        SMART CBT
                    </span>

                    <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-slate-400">
                        Computer Based Test
                    </span>
                </span>
            </a>

            {{-- Close Mobile --}}
            <button
                type="button"
                class="rounded-lg p-2 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden"
                @click="open = false"
                aria-label="Tutup menu"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

        </div>


        {{-- Navigation --}}
        <nav class="sidebar-scrollbar-hidden min-h-0 flex-1 overflow-y-auto px-4 py-6">

            {{-- Dashboard --}}
            @if (! Auth::user()->isSiswa())
            <a
                href="{{ Auth::user()->isGuru() ? route('guru.dashboard') : route('dashboard') }}"
                class="mb-2 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('dashboard', 'guru.dashboard') ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/10' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                @click="open = false"
            >
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 21v-6h6v6" />
                </svg>

                Dashboard
            </a>
            @endif


            {{-- ========================= --}}
            {{-- ADMIN --}}
            {{-- ========================= --}}

            @if (Auth::user()->isAdmin())

                {{-- Master Data --}}
                <div class="mt-5">

                    <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">
                        Master Data
                    </p>

                    <div class="space-y-1">

                        {{-- Siswa --}}
                        <a
                            href="{{ route('siswa.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('siswa.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm5-2h5m-2.5-2.5V11" />
                            </svg>

                            <span>Siswa</span>
                        </a>


                        {{-- Guru --}}
                        <a
                            href="{{ route('guru.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('guru.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm5.5-1.5a2.5 2.5 0 1 0 0-5" />
                            </svg>

                            <span>Guru</span>
                        </a>


                        {{-- Kelas --}}
                        <a
                            href="{{ route('kelas.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('kelas.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M3.5 5.5h17v13h-17zM7 9h4m-4 3h7m-7 3h5" />
                            </svg>

                            <span>Kelas</span>
                        </a>


                        {{-- Mata Pelajaran --}}
                        <a
                            href="{{ route('mata-pelajaran.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('mata-pelajaran.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 5.5v16M8 7h8m-8 4h8" />
                            </svg>

                            <span>Mata Pelajaran</span>
                        </a>

                    </div>
                </div>


                {{-- Akademik --}}
                <div class="mt-6">

                    <button
                        type="button"
                        class="mb-2 flex w-full items-center justify-between px-3 text-left text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500"
                        @click="cbtOpen = !cbtOpen"
                    >
                        <span>AKADEMIK</span>

                        <svg
                            class="h-4 w-4 transition"
                            :class="{ 'rotate-180': !cbtOpen }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m6 9 6 6 6-6" />
                        </svg>
                    </button>


                    <div x-show="cbtOpen" x-collapse class="space-y-1">

                        {{-- Ujian --}}
                        <a
                            href="{{ route('ujian.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('ujian.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M6 3.5h9l3 3V20.5H6a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M14 3.5v4h4M8 12h8m-8 3h6" />
                            </svg>

                            <span>Ujian</span>
                        </a>


                        {{-- Hasil Ujian --}}
                        <a
                            href="{{ route('hasil.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('hasil.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 19.5V5.8A1.8 1.8 0 0 1 5.8 4H20v15H5.8A1.8 1.8 0 0 0 4 20.8" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 15v-3m4 3V8m4 7v-5" />
                            </svg>

                            <span>Hasil Ujian</span>
                        </a>

                    </div>
                </div>

            @endif


            {{-- ========================= --}}
            {{-- GURU --}}
            {{-- ========================= --}}

            @if (Auth::user()->isGuru())

                <div class="mt-6">

                    <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">
                        
                    </p>

                    <div class="space-y-1">

                        {{-- Ujian Saya --}}
                        <a
                            href="{{ route('guru.ujian.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('guru.ujian.*', 'guru.soal.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M6 3.5h9l3 3V20.5H6a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M14 3.5v4h4M8 12h8m-8 3h6" />
                            </svg>

                            <span>Ujian Saya</span>
                        </a>


                        {{-- Token Ujian --}}
                        <a
                            href="{{ route('guru.token.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('guru.token.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 10V7a4 4 0 1 1 8 0v3m-9 0h10a2 2 0 0 1 2 2v7H5v-7a2 2 0 0 1 2-2Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 14v2" />
                            </svg>

                            <span>Token Ujian</span>
                        </a>


                        {{-- Bank Soal --}}
                        <a
                            href="{{ route('guru.bank.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('guru.bank.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M5 4.5h10l4 4v11H5v-15Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M15 4.5v4h4M8 12h8m-8 3h5" />
                            </svg>

                            <span>Bank Soal</span>
                        </a>


                        {{-- Rekap Nilai --}}
                        <a
                            href="{{ route('guru.hasil.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('guru.hasil.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M5 19V5a1 1 0 0 1 1-1h12v16H6a1 1 0 0 1-1-1Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 15v-3m4 3V8m4 7v-5" />
                            </svg>

                            <span>Rekap Nilai</span>
                        </a>


                        {{-- Monitoring Ujian --}}
                        <a
                            href="{{ route('guru.monitoring.index') }}"
                            class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition
                            {{ request()->routeIs('guru.monitoring.*')
                                ? 'bg-white/10 font-semibold text-white'
                                : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                            @click="open = false"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 12a8 8 0 1 1 16 0v6H4v-6Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 12h.01M12 12h.01M16 12h.01" />
                            </svg>

                            <span>Monitoring Ujian</span>
                        </a>

                    </div>
                </div>

            @endif

            {{-- ========================= --}}
            {{-- SISWA --}}
            {{-- ========================= --}}

            @if (Auth::user()->isSiswa())
                <div class="mt-2">
                    <div class="space-y-1">
                        <a href="{{ route('siswa.dashboard') }}" class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition {{ request()->routeIs('siswa.dashboard') ? 'bg-white/10 font-semibold text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" @click="open = false">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 21v-6h6v6"/></svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('siswa.ujian.index') }}" class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition {{ request()->routeIs('siswa.ujian.*') ? 'bg-white/10 font-semibold text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" @click="open = false">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3.5h9l3 3V20.5H6a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 3.5v4h4M8 12h8m-8 3h6"/></svg>
                            <span>Ujian Saya</span>
                        </a>
                        <a href="{{ route('siswa.riwayat') }}" class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition {{ request()->routeIs('siswa.riwayat') ? 'bg-white/10 font-semibold text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" @click="open = false">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h10M4 18h7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m17 15 3 3-3 3"/></svg>
                            <span>Riwayat Ujian</span>
                        </a>
                        <a href="{{ route('siswa.hasil') }}" class="flex h-11 items-center gap-3 rounded-xl px-3 text-sm transition {{ request()->routeIs('siswa.hasil') ? 'bg-white/10 font-semibold text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" @click="open = false">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19.5V5.8A1.8 1.8 0 0 1 5.8 4H20v15H5.8A1.8 1.8 0 0 0 4 20.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 15v-3m4 3V8m4 7v-5"/></svg>
                            <span>Hasil / Nilai</span>
                        </a>
                    </div>
                </div>
            @endif

        </nav>

    </aside>

</div>