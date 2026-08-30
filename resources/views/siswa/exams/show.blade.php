<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-bold text-slate-900">Detail Ujian</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8"><div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-sm font-bold text-blue-600">{{ $exam->mataPelajaran?->nama }}</p><h1 class="mt-2 text-3xl font-black text-slate-900">{{ $exam->nama }}</h1><p class="mt-3 text-slate-600">{{ $exam->deskripsi ?: 'Pastikan koneksi internet stabil sebelum memulai ujian.' }}</p>
        <div class="mt-8 grid gap-5 border-y border-slate-100 py-6 sm:grid-cols-2"><div><p class="text-xs text-slate-400">Guru</p><p class="font-semibold">{{ $exam->guru?->nama ?? '-' }}</p></div><div><p class="text-xs text-slate-400">Kelas</p><p class="font-semibold">{{ $exam->kelasData?->nama_kelas ?? $exam->kelas }}</p></div><div><p class="text-xs text-slate-400">Jadwal</p><p class="font-semibold">{{ $exam->tanggal_mulai?->format('d M Y, H:i') }} - {{ $exam->tanggal_selesai?->format('H:i') }}</p></div><div><p class="text-xs text-slate-400">Soal / Durasi</p><p class="font-semibold">{{ $exam->questions_count }} soal · {{ $exam->durasi_menit }} menit</p></div></div>
        @if (session('error')) <p class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p> @endif
        @if ($errors->any()) <p class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</p> @endif
        @if ($exam->status !== 'aktif')
            <p class="text-sm font-semibold text-slate-500">Ujian tidak tersedia. Ujian hanya dapat diakses sesuai jadwal dan status.</p>
        @elseif ($attempt && $attempt->status !== 'in_progress') <a href="{{ route('siswa.ujian.result', $exam) }}" class="inline-flex rounded-lg bg-slate-900 px-5 py-3 text-sm font-bold text-white">Lihat Hasil</a>
        @elseif ($status === 'Sedang Berlangsung' && $attempt && session()->get('siswa.exam_token_verified.' . $exam->id, false)) <a href="{{ route('siswa.ujian.work', $exam) }}" class="inline-flex rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white">Lanjutkan Ujian</a>
        @elseif ($status === 'Sedang Berlangsung' && $exam->questions_count > 0 && $exam->token_aktif && $exam->token) <a href="{{ route('siswa.ujian.token', $exam) }}" class="inline-flex rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white">Mulai Ujian</a>
        @else <p class="text-sm font-semibold text-slate-500">{{ $status }}. Ujian hanya dapat dikerjakan sesuai jadwal.</p> @endif
    </div></div></div>
</x-app-layout>