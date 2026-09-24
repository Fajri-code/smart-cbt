<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Habis - SMART CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-6 text-slate-800">
    <div class="w-full max-w-md text-center bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 mb-6">
            <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Sesi Telah Habis</h1>
        <p class="text-slate-600 mb-8">Sesi halaman Anda telah kadaluarsa karena terlalu lama tidak ada aktivitas atau tab direfresh. Silakan kembali untuk melanjutkan.</p>

        @php
            $redirectUrl = route('dashboard');
            if (request()->is('siswa/ujian/*')) {
                $segments = request()->segments();
                if (isset($segments[2])) {
                    $redirectUrl = url('/siswa/ujian/' . $segments[2] . '/token');
                }
            }
        @endphp

        <div x-data="{ timeLeft: 5 }" x-init="setInterval(() => { if (timeLeft > 0) timeLeft--; else window.location.href = '{{ $redirectUrl }}' }, 1000)">
            <p class="text-sm text-slate-500 mb-4">Anda akan dialihkan secara otomatis dalam <strong x-text="timeLeft" class="text-amber-600"></strong> detik...</p>
            <a href="{{ $redirectUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition">
                Muat Ulang Halaman
            </a>
        </div>
    </div>
</body>
</html>
