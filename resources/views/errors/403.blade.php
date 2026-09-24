<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - SMART CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-6 text-slate-800">
    <div class="w-full max-w-md text-center bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-6">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Akses Ditolak / Sesi Habis</h1>
        <p class="text-slate-600 mb-8">{{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}</p>

        @php
            $redirectUrl = route('dashboard');
            if (request()->is('siswa/ujian/*')) {
                $segments = request()->segments();
                if (isset($segments[2])) {
                    $redirectUrl = url('/siswa/ujian/' . $segments[2] . '/token');
                }
            }
        @endphp

        @if($exception->getMessage() === 'Token ujian tidak valid.' || str_contains($exception->getMessage(), 'Token'))
            <div x-data="{ timeLeft: 5 }" x-init="setInterval(() => { if (timeLeft > 0) timeLeft--; else window.location.href = '{{ $redirectUrl }}' }, 1000)">
                <p class="text-sm text-slate-500 mb-4">Anda akan dialihkan ke halaman input token dalam <strong x-text="timeLeft" class="text-red-600"></strong> detik...</p>
                <a href="{{ $redirectUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                    Masukkan Token Kembali
                </a>
            </div>
        @else
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 transition">
                Kembali
            </a>
        @endif
    </div>
</body>
</html>
