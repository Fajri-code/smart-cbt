@php
    $typeLabels = ['pg' => 'Pilihan Ganda', 'essay' => 'Essay', 'pg_essay' => 'Pilihan Ganda + Essay'];
    $statusLabels = ['draft' => 'Draft', 'aktif' => 'Aktif', 'selesai' => 'Selesai'];
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                {{-- Breadcrumb --}}
                <nav class="mb-1 flex items-center gap-2 text-xs text-slate-500">
                    <a href="{{ ($isGuruView ?? false) ? route('guru.dashboard') : route('dashboard') }}" class="hover:text-slate-800">Dashboard</a>
                    <span>/</span>
                    <a href="{{ ($isGuruView ?? false) ? route('guru.ujian.index') : route('ujian.index') }}" class="hover:text-slate-800">Ujian</a>
                    <span>/</span>
                    <span class="font-medium text-slate-800">Detail Ujian</span>
                </nav>
                <h2 class="text-xl font-bold leading-tight text-slate-900">
                    Detail Ujian: {{ $exam->nama }}
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Informasi jadwal, konfigurasi paket ujian, penugasan pengawas, dan statistik butir soal.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($isGuruView ?? false)
                    <a href="{{ route('guru.soal.index', $exam) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Kelola Soal
                    </a>
                    <a href="{{ route('guru.token.show', $exam) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Token
                    </a>
                @else
                    <a href="{{ route('ujian.edit', $exam) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                        Edit Ujian
                    </a>
                @endif
                <a href="{{ ($isGuruView ?? false) ? route('guru.ujian.index') : route('ujian.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Stat Cards --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Soal</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ $totalQuestions }} <span class="text-xs font-normal text-slate-500">butir</span></p>
                    <p class="mt-1 text-xs text-slate-500">{{ $pgCount }} PG &bull; {{ $essayCount }} Essay</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Durasi Pengerjaan</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ $exam->durasi_menit }} <span class="text-xs font-normal text-slate-500">menit</span></p>
                    <p class="mt-1 text-xs text-slate-500">Tipe: {{ $typeLabels[$exam->jenis] ?? $exam->jenis }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Ujian</p>
                    <p class="mt-2 text-2xl font-black {{ $exam->status === 'aktif' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $statusLabels[$exam->status] ?? ucfirst($exam->status) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Token: {{ $exam->token_aktif ? 'Aktif (' . $exam->token . ')' : 'Nonaktif' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Peserta Selesai/Mulai</p>
                    <p class="mt-2 text-2xl font-black text-blue-600">{{ $totalParticipants }} <span class="text-xs font-normal text-slate-500">siswa</span></p>
                    <p class="mt-1 text-xs text-slate-500">Riwayat submit & pengerjaan</p>
                </div>
            </div>

            {{-- Informasi Ujian --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="font-bold text-slate-900">Informasi Utama Paket Ujian</h3>
                </div>
                <div class="p-6">
                    <dl class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Nama Ujian</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $exam->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Mata Pelajaran</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $exam->mataPelajaran?->nama ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Guru Pembuat (PIC)</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $exam->guru?->nama ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Target Kelas</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Jadwal Pelaksanaan</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $exam->tanggal_mulai?->format('d/m/Y H:i') ?? '-' }} s/d {{ $exam->tanggal_selesai?->format('d/m/Y H:i') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Ruangan & Pengawas</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $exam->ruangan ?: '-' }} (Pengawas: {{ $exam->guruPengawas?->nama ?? 'Belum ada' }})</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi / Petunjuk Pengerjaan</dt>
                            <dd class="mt-1 text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $exam->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>