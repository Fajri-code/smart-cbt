<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SMART CBT') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            aside.bg-slate-950 {
                background: #fff !important;
                border-right: 1px solid #dbe4ec;
                box-shadow: 10px 0 28px rgba(15, 35, 55, .04);
            }

            aside.bg-slate-950 > div:first-child {
                background: #fff;
                border-bottom-color: #dbe4ec;
            }

            aside.bg-slate-950 .sidebar-scrollbar-hidden {
                background: transparent;
            }

            aside.bg-slate-950 .text-slate-300 {
                color: #526273 !important;
            }

            aside.bg-slate-950 .text-slate-400 {
                color: #64748b !important;
            }

            aside.bg-slate-950 .text-slate-500 {
                color: #8290a0 !important;
            }

            aside.bg-slate-950 .text-white {
                color: #102033 !important;
            }

            aside.bg-slate-950 .bg-white\/10 {
                background: #f1f5f9 !important;
            }

            aside.bg-slate-950 .bg-white\/5 {
                background: #f8fafc !important;
            }

            aside.bg-slate-950 .bg-amber-400 {
                background: #eaf1ff !important;
                color: #4169ff !important;
                box-shadow: none !important;
            }

            aside.bg-slate-950 .bg-amber-400 svg {
                color: #4169ff;
            }

            aside.bg-slate-950 .hover\:bg-white\/10:hover {
                background: #f1f5f9 !important;
                color: #102033 !important;
            }

            aside.bg-slate-950 nav a {
                color: #526273 !important;
            }

            aside.bg-slate-950 nav a.bg-white\/10,
            aside.bg-slate-950 nav a.bg-amber-400 {
                color: #102033 !important;
            }

            aside.bg-slate-950 nav a:not(.bg-amber-400) svg {
                color: #718096;
            }

            .app-header {
                background: #fff;
            }
        </style>
    </head>

    <body class="font-sans antialiased text-slate-800">
        <div id="page-loading-indicator" aria-hidden="true"></div>

        <div class="min-h-screen bg-white">

            {{-- SIDEBAR --}}
            @include('layouts.navigation')

            <div class="min-h-screen lg:pl-64">

                {{-- TOP HEADER --}}
                <header
                    class="app-header relative z-40 flex min-h-[5rem] py-3 items-center justify-between border-b border-slate-200/80 bg-white/95 px-4 shadow-sm backdrop-blur sm:px-6 lg:px-8"
                >

                    {{-- KIRI --}}
                    <div class="flex items-center gap-4">

                        {{-- Tombol menu mobile --}}
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 lg:hidden"
                            onclick="window.dispatchEvent(new CustomEvent('open-sidebar'))"
                            aria-label="Buka menu"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>

                        {{-- Judul halaman --}}
                        @isset($header)
                            <div>
                                {{ $header }}
                            </div>
                        @endisset

                    </div>


                    {{-- KANAN: PROFIL --}}
                    <div
                        x-data="{ profileOpen: false }"
                        class="relative"
                    >

                        {{-- Tombol Profil --}}
                        <button
                            type="button"
                            @click="profileOpen = !profileOpen"
                            @click.outside="profileOpen = false"
                            class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm transition hover:bg-slate-50"
                        >

                            {{-- Avatar --}}
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#eaf1ff] font-bold text-[#4169ff]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>

                            {{-- Nama --}}
                            <div class="text-left">
                                <p class="max-w-[180px] truncate text-sm font-bold text-slate-800">
                                    {{ Auth::user()->name }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ Auth::user()->isAdmin() ? 'Super Admin' : (Auth::user()->isGuru() ? 'Guru' : 'Siswa') }}
                                </p>
                            </div>

                            {{-- Chevron --}}
                            <svg
                                class="hidden h-4 w-4 text-slate-500 transition sm:block"
                                :class="{ 'rotate-180': profileOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m6 9 6 6 6-6"
                                />
                            </svg>

                        </button>


                        {{-- DROPDOWN PROFIL --}}
                        <div
                            x-show="profileOpen"
                            x-transition
                            style="display: none;"
                            class="absolute right-0 top-14 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
                        >

                            {{-- Info user --}}
                            <div class="border-b border-slate-200 px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#eaf1ff] font-bold text-[#4169ff]">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">
                                            {{ Auth::user()->name }}
                                        </p>

                                        <p class="truncate text-xs text-slate-500">
                                            {{ Auth::user()->email }}
                                        </p>

                                        <span class="mt-1 inline-flex rounded-full bg-[#eaf1ff] px-2.5 py-1 text-[10px] font-semibold text-[#4169ff]">
                                            {{ Auth::user()->isAdmin() ? 'Super Admin' : (Auth::user()->isGuru() ? 'Guru' : 'Siswa') }}
                                        </span>
                                    </div>

                                </div>

                            </div>


                            {{-- Menu --}}
                            <div class="p-2">

                                {{-- Profil --}}
                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                >
                                    <svg
                                        class="h-5 w-5 text-slate-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M4 21a8 8 0 0 1 16 0"
                                        />
                                    </svg>

                                    <span>Profil Saya</span>
                                </a>


                                {{-- Ubah Password --}}
                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                >
                                    <svg
                                        class="h-5 w-5 text-slate-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M16 8a4 4 0 1 0-7.5 2H3v4h3v3h3v-3h3.5A4 4 0 0 0 16 8Z"
                                        />
                                    </svg>

                                    <span>Ubah Password</span>
                                </a>


                                {{-- Garis --}}
                                <div class="my-1 border-t border-slate-100"></div>


                                {{-- Logout --}}
                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.8"
                                                d="M14 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-3M9 12h11m0 0-3-3m3 3-3 3"
                                            />
                                        </svg>

                                        <span>Keluar</span>
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </header>


                {{-- CONTENT --}}
                <main>
                    {{ $slot }}
                </main>

            </div>

        </div>
    </body>
</html>
