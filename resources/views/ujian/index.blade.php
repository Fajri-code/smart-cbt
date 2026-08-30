@php
    $componentLabels = ['pg' => 'PG', 'essay_1' => 'Essay 1', 'essay_2' => 'Essay 2'];
    $statusLabels = ['draft' => 'Draft', 'aktif' => 'Aktif', 'selesai' => 'Selesai'];
    $statusStyles = ['draft' => 'bg-slate-100 text-slate-700', 'aktif' => 'bg-emerald-100 text-emerald-700', 'selesai' => 'bg-blue-100 text-blue-700'];
@endphp
<x-app-layout>
    <x-slot name="header"><div class="flex items-center justify-between gap-4"><h2 class="text-xl font-semibold leading-tight text-slate-800">Data Ujian</h2>@if (Auth::user()->isAdmin())<a href="{{ route('ujian.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Tambah Ujian</a>@endif</div></x-slot>
    <div class="py-10"><div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @foreach (['success' => 'green', 'error' => 'red'] as $key => $color)
            @if (session($key))<div class="mb-4 rounded-md bg-{{ $color }}-50 p-4 text-sm text-{{ $color }}-700">{{ session($key) }}</div>@endif
        @endforeach
        <div class="overflow-hidden rounded-lg bg-white shadow-sm"><div class="overflow-x-auto p-6">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm"><thead class="text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">No</th><th class="px-4 py-3">Nama Ujian</th><th class="px-4 py-3">Mata Pelajaran</th><th class="px-4 py-3">Guru Penanggung Jawab</th><th class="px-4 py-3">Guru Pengawas</th><th class="px-4 py-3">Komponen Soal</th><th class="px-4 py-3">Durasi</th><th class="px-4 py-3">Jumlah Soal</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">@forelse ($exams as $exam)@php
    $computedStatus = $exam->status;
    // If status is 'aktif', check if deadline has passed
    if ($exam->status === 'aktif' && $exam->tanggal_selesai && $exam->tanggal_selesai->lte(now())) {
        $computedStatus = 'selesai';
    }
    // If status is 'aktif', check if it hasn't started yet
    if ($exam->status === 'aktif' && $exam->tanggal_mulai && now()->lessThan($exam->tanggal_mulai)) {
        $computedStatus = 'draft';
    }
@endphp<tr><td class="px-4 py-3">{{ $exams->firstItem() + $loop->index }}</td><td class="min-w-56 px-4 py-3 font-medium text-slate-900">{{ $exam->nama }}</td><td class="whitespace-nowrap px-4 py-3">{{ $exam->mataPelajaran->nama }}</td><td class="whitespace-nowrap px-4 py-3">{{ $exam->guru->nama }}</td><td class="min-w-52 px-4 py-3">{{ $exam->guruPengawas?->nama ?? '-' }}</td><td class="min-w-48 px-4 py-3"><div class="flex flex-wrap gap-1">@foreach (($exam->komponen_soal ?: match ($exam->jenis) { 'pg' => ['pg'], 'essay' => ['essay_1', 'essay_2'], default => ['pg', 'essay_1', 'essay_2'] }) as $component)<span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ $componentLabels[$component] ?? $component }}</span>@endforeach</div></td><td class="whitespace-nowrap px-4 py-3">{{ $exam->durasi_menit }} menit</td><td class="px-4 py-3">{{ $exam->questions_count }}</td><td class="whitespace-nowrap px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyles[$computedStatus] ?? 'bg-slate-100 text-slate-700' }}">{{ $statusLabels[$computedStatus] ?? ucfirst($computedStatus) }}</span></td><td class="whitespace-nowrap px-4 py-3"><div class="flex gap-3"><a class="text-blue-600 hover:text-blue-900" href="{{ route('ujian.show', $exam) }}">Detail</a><a class="text-slate-600 hover:text-slate-900" href="{{ route('ujian.edit', $exam) }}">Edit</a><form method="POST" action="{{ route('ujian.destroy', $exam) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini?')">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-900" type="submit">Hapus</button></form></div></td></tr>@empty<tr><td colspan="10" class="px-4 py-10 text-center text-slate-500">Belum ada data ujian.</td></tr>@endforelse</tbody>
            </table><div class="mt-6">{{ $exams->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>