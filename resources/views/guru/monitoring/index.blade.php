<x-app-layout>
    @php
        $labels = ['online' => 'Peserta Online', 'in_progress' => 'Sedang Mengerjakan', 'completed' => 'Selesai', 'not_started' => 'Belum Mulai'];
    @endphp
    <x-slot name="header"><div><p class="text-sm font-medium text-amber-600"></p><h2 class="text-xl font-semibold leading-tight text-slate-800">Monitoring Ujian</h2><p class="mt-1 text-sm text-slate-500">Pantau progres peserta pada ujian yang menjadi tanggung jawab Anda.</p></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        
        {{-- Filter Ujian --}}
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('guru.monitoring.index') }}" class="flex flex-col sm:flex-row items-center gap-4">
                @if($status !== 'online')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <label for="exam_id" class="text-sm font-medium text-slate-700 whitespace-nowrap">Filter Ujian:</label>
                <select name="exam_id" id="exam_id" class="block w-full sm:w-auto rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" onchange="this.form.submit()">
                    <option value="">-- Tampilkan Semua Ujian --</option>
                    @foreach($ownedExams as $ex)
                        <option value="{{ $ex->id }}" {{ $queryExamId == $ex->id ? 'selected' : '' }}>
                            {{ $ex->nama }} ({{ $ex->kelas_nama }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($labels as $key => $label)
                <a href="{{ route('guru.monitoring.index', ['status' => $key, 'exam_id' => $queryExamId]) }}" class="rounded-lg border bg-white p-5 shadow-sm transition hover:border-blue-300 {{ $status === $key ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200' }}"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $counts[$key] }}</p></a>
            @endforeach
        </div>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-200 px-6 py-4"><h3 class="font-semibold text-slate-900">{{ $labels[$status] }}</h3></div><div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-6 py-3">Peserta</th><th class="px-6 py-3">Ujian</th><th class="px-6 py-3">Kelas</th><th class="px-6 py-3">Status</th>@if ($status === 'not_started')<th class="px-6 py-3">Jadwal Mulai</th><th class="px-6 py-3">Jadwal Selesai</th>@else<th class="px-6 py-3">Mulai</th><th class="px-6 py-3">{{ $status === 'completed' ? 'Selesai' : 'Deadline' }}</th>@if ($status === 'in_progress')<th class="px-6 py-3">Progress</th>@endif @if ($status === 'completed')<th class="px-6 py-3">Nilai</th><th class="px-6 py-3 text-right">Aksi</th>@elseif ($status === 'in_progress')<th class="px-6 py-3 text-right">Aksi</th>@endif @endif</tr></thead><tbody class="divide-y divide-slate-100">
            @forelse ($attempts as $attempt)
                @if ($status === 'not_started')
                    <tr><td class="px-6 py-4 font-semibold text-slate-900">{{ $attempt->nama }}</td><td class="px-6 py-4">{{ $attempt->exam_nama }}</td><td class="px-6 py-4">{{ $attempt->exam_kelas ?: '-' }}</td><td class="px-6 py-4">Belum Mulai</td><td class="px-6 py-4">{{ $attempt->tanggal_mulai ? \Illuminate\Support\Carbon::parse($attempt->tanggal_mulai)->format('d/m/Y H:i') : '-' }}</td><td class="px-6 py-4">{{ $attempt->tanggal_selesai ? \Illuminate\Support\Carbon::parse($attempt->tanggal_selesai)->format('d/m/Y H:i') : '-' }}</td></tr>
                @else
                    <tr><td class="px-6 py-4 font-semibold text-slate-900">{{ $attempt->siswa?->nama ?? '-' }}</td><td class="px-6 py-4">{{ $attempt->exam?->nama ?? '-' }}</td><td class="px-6 py-4">{{ $attempt->exam?->kelasData?->nama_kelas ?? $attempt->exam?->kelas ?? '-' }}</td><td class="px-6 py-4">{{ $status === 'in_progress' ? 'Sedang Mengerjakan' : ($status === 'online' ? 'Online' : 'Selesai') }}</td><td class="px-6 py-4">{{ $attempt->started_at?->format('d/m/Y H:i') ?? '-' }}</td><td class="px-6 py-4">{{ $status === 'completed' ? ($attempt->submitted_at?->format('d/m/Y H:i') ?? '-') : ($attempt->exam?->tanggal_selesai?->format('d/m/Y H:i') ?? '-') }}</td>@if ($status === 'in_progress')<td class="px-6 py-4">{{ $attempt->answers_count ?? 0 }} / {{ $attempt->exam?->questions_count ?? 0 }}</td>@endif @if ($status === 'completed')<td class="px-6 py-4">{{ $attempt->nilai_akhir ?? 'Belum Dinilai' }}</td><td class="px-6 py-4 text-right"><a href="{{ route('guru.hasil.show', $attempt->id) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Rekap Jawaban</a></td>@elseif ($status === 'in_progress')<td class="px-6 py-4 text-right"><a href="{{ route('guru.hasil.show', $attempt->id) }}" class="inline-flex items-center gap-1 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Intip Jawaban</a></td>@endif</tr>
                @endif
            @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada data peserta pada kategori ini.</td></tr>
            @endforelse
            </tbody></table></div><div class="p-6">{{ $attempts->links() }}</div></div>
    </div></div>
</x-app-layout>
