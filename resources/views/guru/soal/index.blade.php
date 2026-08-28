<x-app-layout>
    {{-- BARIS 1 — Header Halaman --}}
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.ujian.index') }}"
               class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
               title="Kembali ke Ujian Saya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-slate-900 leading-tight">Kelola Soal: {{ $exam->nama }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $exam->mataPelajaran?->nama ?? '-' }} &bull; Kelas {{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ showBank: false }">
        <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs font-medium text-emerald-800">
                    <span>✓ {{ session('success') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- BARIS 2 — Informasi Ujian (Card Ringkasan) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-3.5">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Informasi Paket Ujian</span>
                        <h1 class="text-xl font-bold text-slate-900">{{ $exam->nama }}</h1>
                    </div>
                    <div>
                        @if ($exam->status === 'aktif' && $exam->token_aktif)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-700">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Ujian Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold text-amber-700">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                Draft / Penyusunan
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Grid Rincian Informasi --}}
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block">Mata Pelajaran</span>
                        <span class="mt-1 font-bold text-slate-800 text-sm block">{{ $exam->mataPelajaran?->nama ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Kelas</span>
                        <span class="mt-1 font-bold text-slate-800 text-sm block">{{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Durasi Pengerjaan</span>
                        <span class="mt-1 font-bold text-slate-800 text-sm block">{{ $exam->durasi_menit }} Menit</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Total Butir Soal</span>
                        <span class="mt-1 font-bold text-slate-800 text-sm block">{{ $totalQuestions }} Butir <span class="text-xs font-normal text-slate-500">({{ $pgCount }} PG, {{ $essayCount }} Essay)</span></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Total Bobot Nilai</span>
                        <span class="mt-1 font-bold text-slate-800 text-sm block">{{ number_format($totalBobot, 1) }} Poin</span>
                    </div>
                </div>
            </div>

            {{-- BARIS 3 — Action Ujian (Card Terpisah untuk Status Token & Aksi Ujian) --}}
            <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500">Token Akses Siswa:</span>
                    @if ($exam->status === 'aktif' && $exam->token_aktif)
                        <div class="inline-flex items-center gap-2 rounded-xl bg-slate-50 border border-slate-200 px-3 py-1.5">
                            <span class="text-xs text-slate-500 font-medium">Token:</span>
                            <span class="font-mono text-sm font-black text-emerald-700 tracking-wider">{{ $exam->token }}</span>
                        </div>
                    @else
                        <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-xl">Belum Diterbitkan</span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if ($exam->status === 'aktif' && $exam->token_aktif)
                        <a href="{{ route('guru.token.show', $exam) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Kelola Token
                        </a>
                        <a href="{{ route('guru.monitoring.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-300 transition shadow-sm">
                            Monitoring Siswa &rarr;
                        </a>
                    @else
                        <form method="POST" action="{{ route('guru.soal.publish', $exam) }}" onsubmit="return confirm('Terbitkan ujian ini sekarang? Token ujian akan langsung dibuat dan aktif untuk siswa.')">
                            @csrf
                            <button type="submit"
                                    @disabled($totalQuestions === 0)
                                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50 transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Terbitkan Ujian Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- BARIS 4 — Action Soal (Toolbar Aksi Soal) --}}
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('guru.soal.create', $exam) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Tambah Soal Manual
                    </a>

                    <button type="button"
                            @click="showBank = !showBank"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span x-text="showBank ? 'Tutup Bank Soal' : 'Ambil dari Bank Soal'"></span>
                        @if ($banks->isNotEmpty())
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">{{ $banks->count() }}</span>
                        @endif
                    </button>
                </div>

                @if ($questions->total() > 0)
                    <div>
                        <button form="reorder-form"
                                type="submit"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            Simpan Urutan
                        </button>
                    </div>
                @endif
            </div>

            {{-- Panel Bank Soal (Hanya muncul jika tombol diklik) --}}
            <div x-show="showBank" x-collapse class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Pilih Bank Soal untuk Diimport</h3>
                    <button type="button" @click="showBank = false" class="text-xs text-slate-400 hover:text-slate-600">Tutup &times;</button>
                </div>

                @if ($banks->isEmpty())
                    <p class="text-xs text-slate-500">Anda belum memiliki bank soal. <a href="{{ route('guru.bank.index') }}" class="font-bold text-blue-600 hover:underline">Buat bank soal di sini</a>.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($banks as $bank)
                            @php
                                $allowedTypes = $exam->komponen_soal ?: match ($exam->jenis) { 'pg' => ['pg'], 'essay' => ['essay_1', 'essay_2'], default => ['pg', 'essay_1', 'essay_2'] };
                                $compatible = $bank->questions->filter(fn ($q) => in_array($q->tipe, $allowedTypes, true));
                                $remaining = $compatible->reject(fn ($bq) => in_array($bq->pertanyaan, $importedQuestions, true));
                            @endphp
                            <div class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-white p-3 sm:flex-row sm:items-center sm:justify-between text-xs">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $bank->nama }}</p>
                                    <p class="text-slate-500">{{ $bank->questions->count() }} soal di bank &bull; {{ $remaining->count() }} belum dimasukkan</p>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('guru.soal.import-bank', [$exam, $bank]) }}">
                                        @csrf
                                        <button type="submit"
                                                @disabled($remaining->isEmpty())
                                                class="rounded-lg px-3 py-1.5 font-bold transition {{ $remaining->isEmpty() ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                            {{ $remaining->isEmpty() ? '✓ Semua Sudah Masuk' : 'Import ' . $remaining->count() . ' Soal' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Form Reorder Urutan --}}
            <form id="reorder-form" method="POST" action="{{ route('guru.soal.reorder', $exam) }}">
                @csrf
                @method('PATCH')
            </form>

            {{-- Daftar Butir Soal --}}
            <div class="space-y-3">
                @forelse ($questions as $question)
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300">
                        {{-- Baris Atas Soal: Nomor, Tipe, Bobot, Tombol Aksi --}}
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 min-w-[24px] items-center justify-center rounded-md bg-slate-900 px-1.5 font-bold text-white text-[11px]">
                                    #{{ $question->urutan }}
                                </span>
                                <input form="reorder-form"
                                       class="h-6 w-12 rounded border-slate-300 text-center text-xs font-bold p-0"
                                       name="order[{{ $question->id }}]"
                                       type="number"
                                       min="1"
                                       value="{{ $question->urutan }}"
                                       title="Ganti nomor urut">

                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                    {{ $question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}
                                </span>

                                <span class="text-slate-400">
                                    Bobot: <strong class="text-slate-700">{{ $question->bobot }}</strong>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('guru.soal.edit', [$exam, $question]) }}" class="font-semibold text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>
                                <span class="text-slate-300">&bull;</span>
                                <form method="POST" action="{{ route('guru.soal.duplicate', [$exam, $question]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="font-semibold text-slate-600 hover:text-slate-900">
                                        Duplikat
                                    </button>
                                </form>
                                <span class="text-slate-300">&bull;</span>
                                <form method="POST" action="{{ route('guru.soal.destroy', [$exam, $question]) }}" onsubmit="return confirm('Hapus soal #{{ $question->urutan }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-rose-600 hover:text-rose-800">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Teks Pertanyaan --}}
                        <div class="mt-3 text-sm font-medium text-slate-800 leading-relaxed whitespace-pre-line">
                            {{ $question->pertanyaan }}
                        </div>

                        {{-- Pilihan Opsi (Jika PG) --}}
                        @if ($question->tipe === 'pg')
                            <div class="mt-3 space-y-1.5 text-xs">
                                @foreach (['a', 'b', 'c', 'd', 'e'] as $option)
                                    @if ($question->{'opsi_'.$option})
                                        @php
                                            $isKey = strtoupper($question->kunci) === strtoupper($option);
                                        @endphp
                                        <div class="flex items-center gap-2 rounded-lg px-3 py-2 transition {{ $isKey ? 'bg-emerald-50 font-bold text-emerald-900 border border-emerald-200' : 'bg-slate-50 text-slate-700' }}">
                                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded text-[11px] font-bold {{ $isKey ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                                {{ strtoupper($option) }}
                                            </span>
                                            <span class="flex-1">{{ $question->{'opsi_'.$option} }}</span>
                                            @if ($isKey)
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded">
                                                    ✓ Kunci Benar
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif ($question->petunjuk_jawaban)
                            <div class="mt-3 rounded-lg bg-purple-50 p-2.5 text-xs text-purple-800">
                                <strong>Petunjuk Rubrik:</strong> {{ $question->petunjuk_jawaban }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
                        <p class="font-bold text-slate-800 text-sm">Belum ada soal pada ujian ini.</p>
                        <p class="mt-1 text-xs text-slate-500">Klik tombol di bawah untuk membuat soal baru atau import dari bank soal.</p>
                        <div class="mt-4 flex justify-center gap-2">
                            <a href="{{ route('guru.soal.create', $exam) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">
                                + Tambah Soal Manual
                            </a>
                            <button type="button" @click="showBank = true" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Ambil dari Bank Soal
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination & Simpan Urutan Bawah --}}
            @if ($questions->total() > 0)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                    <button form="reorder-form"
                            type="submit"
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Simpan Urutan Soal
                    </button>
                    <div>
                        {{ $questions->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
