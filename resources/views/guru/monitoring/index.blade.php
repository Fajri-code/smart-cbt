<x-app-layout>
    @php
        $labels = ['online' => 'Peserta Online', 'in_progress' => 'Sedang Mengerjakan', 'completed' => 'Selesai', 'not_started' => 'Belum Mulai'];
    @endphp
    <x-slot name="header"><div><p class="text-sm font-medium text-amber-600"></p><h2 class="text-xl font-semibold leading-tight text-slate-800">Monitoring Ujian</h2><p class="mt-1 text-sm text-slate-500">Pantau progres peserta pada ujian yang menjadi tanggung jawab Anda.</p></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($labels as $key => $label)
                <a href="{{ route('guru.monitoring.index', ['status' => $key]) }}" class="rounded-lg border bg-white p-5 shadow-sm transition hover:border-blue-300 {{ $status === $key ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200' }}"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $counts[$key] }}</p></a>
            @endforeach
        </div>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-200 px-6 py-4"><h3 class="font-semibold text-slate-900">{{ $labels[$status] }}</h3></div><div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-6 py-3">Peserta</th><th class="px-6 py-3">Ujian</th><th class="px-6 py-3">Kelas</th><th class="px-6 py-3">Status</th>@if ($status === 'not_started')<th class="px-6 py-3">Jadwal Mulai</th><th class="px-6 py-3">Jadwal Selesai</th>@else<th class="px-6 py-3">Mulai</th><th class="px-6 py-3">{{ $status === 'completed' ? 'Selesai' : 'Deadline' }}</th>@if ($status === 'in_progress')<th class="px-6 py-3">Progress</th>@endif @if ($status === 'completed')<th class="px-6 py-3">Nilai</th>@endif @endif</tr></thead><tbody class="divide-y divide-slate-100">
            @forelse ($attempts as $attempt)
                @if ($status === 'not_started')
                    <tr><td class="px-6 py-4 font-semibold text-slate-900">{{ $attempt->nama }}</td><td class="px-6 py-4">{{ $attempt->exam_nama }}</td><td class="px-6 py-4">{{ $attempt->exam_kelas ?: '-' }}</td><td class="px-6 py-4">Belum Mulai</td><td class="px-6 py-4">{{ $attempt->tanggal_mulai ? \Illuminate\Support\Carbon::parse($attempt->tanggal_mulai)->format('d/m/Y H:i') : '-' }}</td><td class="px-6 py-4">{{ $attempt->tanggal_selesai ? \Illuminate\Support\Carbon::parse($attempt->tanggal_selesai)->format('d/m/Y H:i') : '-' }}</td></tr>
                @else
                    <tr><td class="px-6 py-4 font-semibold text-slate-900">{{ $attempt->siswa?->nama ?? '-' }}</td><td class="px-6 py-4">{{ $attempt->exam?->nama ?? '-' }}</td><td class="px-6 py-4">{{ $attempt->exam?->kelasData?->nama_kelas ?? $attempt->exam?->kelas ?? '-' }}</td><td class="px-6 py-4">{{ $status === 'in_progress' ? 'Sedang Mengerjakan' : ($status === 'online' ? 'Online' : 'Selesai') }}</td><td class="px-6 py-4">{{ $attempt->started_at?->format('d/m/Y H:i') ?? '-' }}</td><td class="px-6 py-4">{{ $status === 'completed' ? ($attempt->submitted_at?->format('d/m/Y H:i') ?? '-') : ($attempt->exam?->tanggal_selesai?->format('d/m/Y H:i') ?? '-') }}</td>@if ($status === 'in_progress')<td class="px-6 py-4">{{ $attempt->answers_count ?? 0 }} / {{ $attempt->exam?->questions_count ?? 0 }}</td>@endif @if ($status === 'completed')<td class="px-6 py-4">{{ $attempt->nilai_akhir ?? 'Belum Dinilai' }}</td>@endif</tr>
                @endif
            @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada data peserta pada kategori ini.</td></tr>
            @endforelse
            </tbody></table></div><div class="p-6">{{ $attempts->links() }}</div></div>
    </div></div>
</x-app-layout>