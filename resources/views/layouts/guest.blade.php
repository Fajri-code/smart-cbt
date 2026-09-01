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
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-blue-50">
            <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="w-full max-w-xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.12)]">
                    <div class="flex items-center justify-center bg-gradient-to-r from-blue-700 to-indigo-600 px-6 py-6 text-white sm:px-8">
                        <div class="flex items-center gap-3">
                            <a href="/" class="inline-flex items-center justify-center rounded-full bg-white/10 p-2 ring-1 ring-white/20">
                                <x-application-logo class="h-9 w-9 fill-current text-white" />
                            </a>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">SMART CBT</p>
                                <h1 class="text-lg font-bold">Portal Login</h1>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-6 sm:px-8 sm:py-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
