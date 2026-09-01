<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Dashboard Guru</h2>
            <p class="mt-0.5 text-sm text-slate-500">Ringkasan ujian yang menjadi tanggung jawab Anda.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-sm sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div><p class="text-sm font-medium text-slate-600">Selamat datang kembali</p><h1 class="mt-1 text-2xl font-bold text-slate-900">Kelola Ujian Anda</h1><p class="mt-2 max-w-xl text-sm text-slate-600">Pantau jadwal, status, dan kesiapan paket ujian dari satu tempat.</p></div>
                    <a href="{{ route('guru.ujian.index') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Lihat Ujian Saya</a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['Total Ujian Saya', 'total', 'text-blue-600', 'bg-blue-50'], ['Ujian Mendatang', 'mendatang', 'text-amber-600', 'bg-amber-50'], ['Ujian Aktif', 'aktif', 'text-emerald-600', 'bg-emerald-50'], ['Ujian Selesai', 'selesai', 'text-slate-600', 'bg-slate-100']] as [$label, $key, $color, $iconBackground])
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p><p class="mt-2 text-3xl font-black {{ $color }}">{{ number_format($stats[$key]) }}</p><p class="mt-1 text-xs text-slate-500">Ringkasan ujian</p></div><div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $iconBackground }} {{ $color }}"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.8" d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm3 5h8M8 13h8M8 17h5"/></svg></div></div></div>
                @endforeach
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><div><h3 class="font-bold text-slate-900">Ujian yang Menjadi Tanggung Jawab Anda</h3><p class="mt-0.5 text-xs text-slate-500">Daftar paket ujian yang perlu dikelola.</p></div><a href="{{ route('guru.ujian.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Semua</a></div><div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Ujian</th><th class="px-5 py-3">Mata Pelajaran</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Jadwal</th><th class="px-5 py-3">Ruangan</th><th class="px-5 py-3">Soal</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100 text-slate-700">@forelse ($exams as $exam)<tr class="hover:bg-slate-50"><td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $exam->nama }}</td><td class="whitespace-nowrap px-5 py-4">{{ $exam->mataPelajaran?->nama ?? '-' }}</td><td class="whitespace-nowrap px-5 py-4">{{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</td><td class="whitespace-nowrap px-5 py-4">{{ $exam->tanggal_mulai?->format('d/m/Y H:i') ?? '-' }}<span class="block text-xs text-slate-500">s.d. {{ $exam->tanggal_selesai?->format('H:i') ?? '-' }}</span></td><td class="whitespace-nowrap px-5 py-4">{{ $exam->ruangan ?? '-' }}</td><td class="whitespace-nowrap px-5 py-4">{{ $exam->questions_count }} soal</td><td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($exam->status) }}</span></td><td class="whitespace-nowrap px-5 py-4"><a class="font-semibold text-blue-600 hover:text-blue-800" href="{{ route('guru.soal.index', $exam) }}">Kelola Soal</a></td></tr>@empty<tr><td colspan="8" class="px-5 py-12 text-center text-slate-500">Belum ada ujian yang ditetapkan kepada Anda.</td></tr>@endforelse</tbody></table></div></div>
        </div>
    </div>
</x-app-layout>