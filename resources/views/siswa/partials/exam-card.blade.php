@php
    $attempt = $exam->examAttempts->first();
    $status = $exam->status !== 'aktif' ? 'Tidak Tersedia' : ($attempt && $attempt->status !== 'in_progress' ? 'Sudah Dikerjakan' : ($exam->tanggal_mulai?->isFuture() ? 'Belum Dimulai' : (($exam->tanggal_selesai && $exam->tanggal_selesai->isPast()) ? 'Sudah Selesai' : 'Sedang Berlangsung')));
    $statusClass = ['Tidak Tersedia' => 'bg-gray-100 text-gray-600', 'Sudah Dikerjakan' => 'bg-slate-100 text-slate-600', 'Belum Dimulai' => 'bg-blue-50 text-blue-700', 'Sudah Selesai' => 'bg-red-50 text-red-700', 'Sedang Berlangsung' => 'bg-emerald-50 text-emerald-700'][$status];
@endphp
<article class="flex h-full flex-col rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="flex items-start justify-between gap-3">
        <div><p class="text-xs font-semibold uppercase tracking-wide text-blue-600">{{ $exam->mataPelajaran?->nama ?? 'Mata Pelajaran' }}</p><h3 class="mt-1 text-lg font-bold text-slate-900">{{ $exam->nama }}</h3></div>
        <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">{{ $status }}</span>
    </div>
    <div class="mt-5 grid grid-cols-2 gap-3 text-sm text-slate-600">
        <div><span class="block text-xs text-slate-400">Jadwal</span>{{ $exam->tanggal_mulai?->format('d M Y') ?? '-' }}</div>
        <div><span class="block text-xs text-slate-400">Waktu</span>{{ $exam->tanggal_mulai?->format('H:i') ?? '-' }} - {{ $exam->tanggal_selesai?->format('H:i') ?? '-' }}</div>
        <div><span class="block text-xs text-slate-400">Durasi</span>{{ $exam->durasi_menit }} menit</div>
        <div><span class="block text-xs text-slate-400">Soal</span>{{ $exam->questions_count }} soal</div>
        @if ($attempt && $attempt->status !== 'in_progress')
            <div><span class="block text-xs text-slate-400">Nilai</span>{{ $attempt->nilai_akhir !== null ? number_format($attempt->nilai_akhir, 2) : '-' }}</div>
            <div><span class="block text-xs text-slate-400">Status</span>{{ $attempt->status === 'expired' ? 'Waktu Habis' : 'Selesai' }}</div>
        @endif
    </div>
    <div class="mt-auto pt-5"><a href="{{ route('siswa.ujian.show', $exam) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700">{{ $status === 'Sedang Berlangsung' && $attempt?->status === 'in_progress' ? 'Lanjutkan Ujian' : 'Lihat Detail' }}</a></div>
</article>