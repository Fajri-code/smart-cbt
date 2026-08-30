<x-app-layout>

    {{-- HEADER HALAMAN --}}
    <x-slot name="header">
        <div class="flex items-center gap-3">

            <a href="{{ route('guru.ujian.index') }}"
               class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
               title="Kembali ke Ujian Saya">

                <svg class="h-4 w-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>

            </a>

            <div>
                <h2 class="text-lg font-bold leading-tight text-slate-900">
                    Kelola Soal: {{ $exam->nama }}
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    {{ $exam->mataPelajaran?->nama ?? '-' }}
                    &bull;
                    Kelas {{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}
                </p>
            </div>

        </div>
    </x-slot>


    <div class="py-6" x-data="{ showBank: false }">

        <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">

            {{-- FLASH MESSAGE --}}
            @if (session('success'))
                <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">

                    <span>
                        ✓ {{ session('success') }}
                    </span>

                    <button type="button"
                            @click="$el.parentElement.remove()"
                            class="text-emerald-500 hover:text-emerald-700">
                        &times;
                    </button>

                </div>
            @endif


            {{-- ERROR MESSAGE --}}
            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800">

                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif


            {{-- ========================================================= --}}
            {{-- RINGKASAN UJIAN --}}
            {{-- ========================================================= --}}

            @php
                $examStatus = $exam->status;
                if ($exam->status === 'aktif' && $exam->tanggal_selesai && $exam->tanggal_selesai->lte(now())) {
                    $examStatus = 'selesai';
                }
                $examLocked = $exam->status === 'selesai';
            @endphp

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-5 border-b border-slate-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl font-bold text-slate-900">{{ $exam->nama }}</h1>
                            @if ($examStatus === 'aktif' && $exam->token_aktif)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                                    Ujian Aktif
                                </span>
                            @elseif ($examStatus === 'selesai')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-700 ring-1 ring-inset ring-slate-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                                    Ujian Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-700 ring-1 ring-inset ring-amber-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Draft
                                </span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-sm text-slate-500">
                            {{ $exam->mataPelajaran?->nama ?? '-' }}
                            <span class="mx-1 text-slate-300">•</span>
                            Kelas {{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center">
                        @if ($examStatus === 'aktif' && $exam->token_aktif)
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-left">
                                <p class="text-[9px] font-semibold uppercase tracking-wider text-emerald-600">Token Siswa</p>
                                <p class="font-mono text-sm font-black tracking-[0.15em] text-emerald-700">{{ $exam->token }}</p>
                            </div>
                        @else
                            <a href="{{ route('guru.token.show', $exam) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2 text-[11px] font-bold text-amber-700 shadow-sm transition hover:bg-amber-100">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6V9m0-6a9 9 0 110 18 9 9 0 010-18z"/>
                                </svg>
                                Terbitkan Token
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid gap-3 p-4 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Soal</p>
                        <div class="mt-2 flex items-end gap-2">
                            <span class="text-2xl font-black text-slate-900">{{ $totalQuestions }}</span>
                            <span class="pb-1 text-xs text-slate-500">butir</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $pgCount }} PG • {{ $essayCount }} Essay</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Durasi</p>
                        <div class="mt-2 flex items-end gap-2">
                            <span class="text-2xl font-black text-slate-900">{{ $exam->durasi_menit }}</span>
                            <span class="pb-1 text-xs text-slate-500">menit</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Pengerjaan siswa</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Bobot</p>
                        <div class="mt-2 flex items-end gap-2">
                            <span class="text-2xl font-black text-slate-900">{{ number_format($totalBobot, 1) }}</span>
                            <span class="pb-1 text-xs text-slate-500">poin</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Skor akumulasi</p>
                    </div>
                </div>
            </section>

            {{-- ========================================================= --}}
            {{-- TOOLBAR AKSI — SEMUA DALAM SATU BARIS --}}
            {{-- ========================================================= --}}

            @php
                $examLocked = $exam->status === 'selesai';
            @endphp

            <div class="flex w-full flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm">

                {{-- ===================================================== --}}
                {{-- KIRI --}}
                {{-- ===================================================== --}}

                <div class="flex flex-wrap items-center gap-2.5">

                    @if (! $examLocked)
                        {{-- TAMBAH SOAL MANUAL --}}
                        <a href="{{ route('guru.soal.create', $exam) }}"
                           class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 4v16m8-8H4"/>

                            </svg>

                            Tambah Soal Manual

                        </a>


                        {{-- AMBIL DARI BANK SOAL --}}
                        <button type="button"
                                @click="showBank = !showBank"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">

                            <svg class="h-4 w-4 text-slate-500"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>

                            </svg>

                            <span x-text="showBank ? 'Tutup Bank Soal' : 'Ambil dari Bank Soal'"></span>

                            @if ($banks->isNotEmpty())

                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                                    {{ $banks->count() }}
                                </span>

                            @endif

                        </button>
                    @else
                        <span class="inline-flex items-center rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500">
                            Ujian selesai: perubahan soal dan import dinonaktifkan
                        </span>
                    @endif

                </div>


                {{-- ===================================================== --}}
                {{-- KANAN --}}
                {{-- ===================================================== --}}

                <div class="flex flex-wrap items-center gap-2">

                    {{-- TOKEN / TERBITKAN UJIAN --}}
                    @php
                        $canShowToken = $exam->status === 'aktif' && $exam->token_aktif;
                        if ($exam->status === 'aktif' && $exam->tanggal_selesai && $exam->tanggal_selesai->lte(now())) {
                            $canShowToken = false;
                        }
                    @endphp

                    @if ($examLocked)
                        <span class="inline-flex items-center rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500">
                            Token tidak bisa dibuat ulang setelah ujian selesai
                        </span>
                    @elseif ($canShowToken)

                        <a href="{{ route('guru.token.show', $exam) }}"
                           class="inline-flex h-10 items-center gap-2 rounded-xl
                                  bg-indigo-600 px-4 text-xs font-bold text-white
                                  shadow-sm transition hover:bg-indigo-700">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>

                            </svg>

                            Kelola Token

                        </a>

                    @else

                        <form method="POST"
                              action="{{ route('guru.soal.publish', $exam) }}"
                              onsubmit="return confirm('Terbitkan ujian ini sekarang? Token ujian akan langsung dibuat dan aktif untuk siswa.')">

                            @csrf

                            <button type="submit"
                                    @disabled($totalQuestions == 0)
                                    class="inline-flex h-10 items-center gap-2 rounded-xl
                                           bg-emerald-600 px-4 py-2 text-xs font-bold
                                           text-white shadow-sm transition
                                           hover:bg-emerald-700
                                           disabled:cursor-not-allowed
                                           disabled:opacity-50">

                                <svg class="h-4 w-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 13l4 4L19 7"/>

                                </svg>

                                Terbitkan Ujian

                            </button>

                        </form>

                    @endif

                    {{-- SIMPAN URUTAN --}}
                    @if ($questions->total() > 0 && ! $examLocked)

                        <button form="reorder-form"
                                type="submit"
                                class="inline-flex h-10 items-center gap-1.5 rounded-xl
                                       border border-slate-200 bg-white px-3.5 py-2
                                       text-xs font-semibold text-slate-700
                                       shadow-sm transition hover:bg-slate-50">

                            <svg class="h-4 w-4 text-slate-500"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>

                            </svg>

                            Simpan Urutan

                        </button>

                    @endif

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- PANEL BANK SOAL --}}
            {{-- ========================================================= --}}

            <div x-show="showBank"
                 x-collapse
                 class="space-y-3 rounded-xl border border-blue-200 bg-blue-50/50 p-4">

                <div class="flex items-center justify-between">

                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                        Pilih Bank Soal untuk Diimport
                    </h3>

                    <button type="button"
                            @click="showBank = false"
                            class="text-xs text-slate-400 hover:text-slate-600">

                        Tutup &times;

                    </button>

                </div>


                @if ($banks->isEmpty())

                    <p class="text-xs text-slate-500">

                        Anda belum memiliki bank soal.

                        <a href="{{ route('guru.bank.index') }}"
                           class="font-bold text-blue-600 hover:underline">

                            Buat bank soal di sini

                        </a>.

                    </p>

                @else

                    <div class="space-y-2">

                        @foreach ($banks as $bank)

                            @php

                                $allowedTypes = $exam->komponen_soal
                                    ?: match ($exam->jenis) {
                                        'pg' => ['pg'],
                                        'essay' => ['essay_1', 'essay_2'],
                                        default => ['pg', 'essay_1', 'essay_2']
                                    };

                                $compatible = $bank->questions->filter(
                                    fn ($q) => in_array($q->tipe, $allowedTypes, true)
                                );

                                $remaining = $compatible->reject(
                                    fn ($bq) => in_array($bq->pertanyaan, $importedQuestions, true)
                                );

                            @endphp


                            <div class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-white p-3 text-xs sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <p class="font-bold text-slate-800">
                                        {{ $bank->nama }}
                                    </p>

                                    <p class="text-slate-500">

                                        {{ $bank->questions->count() }}
                                        soal di bank

                                        &bull;

                                        {{ $remaining->count() }}
                                        belum dimasukkan

                                    </p>

                                </div>


                                <div>

                                    <form method="POST"
                                          action="{{ route('guru.soal.import-bank', [$exam, $bank]) }}">

                                        @csrf

                                        <button type="submit"
                                                @disabled($remaining->isEmpty())
                                                class="rounded-lg px-3 py-1.5 font-bold transition
                                                {{ $remaining->isEmpty()
                                                    ? 'cursor-not-allowed bg-slate-100 text-slate-400'
                                                    : 'bg-blue-600 text-white hover:bg-blue-700' }}">

                                            {{ $remaining->isEmpty()
                                                ? '✓ Semua Sudah Masuk'
                                                : 'Import ' . $remaining->count() . ' Soal' }}

                                        </button>

                                    </form>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- ========================================================= --}}
            {{-- FORM REORDER --}}
            {{-- ========================================================= --}}

            <form id="reorder-form"
                  method="POST"
                  action="{{ route('guru.soal.reorder', $exam) }}">

                @csrf
                @method('PATCH')

            </form>


            {{-- ========================================================= --}}
            {{-- DAFTAR SOAL --}}
            {{-- ========================================================= --}}

            <div class="space-y-3">

                @forelse ($questions as $question)

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300">

                        {{-- BARIS ATAS SOAL --}}
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 text-xs">

                            <div class="flex items-center gap-2">

                                {{-- NOMOR --}}
                                <span class="flex h-6 min-w-[24px] items-center justify-center rounded-md bg-slate-900 px-1.5 text-[11px] font-bold text-white">

                                    #{{ $question->urutan }}

                                </span>


                                {{-- INPUT URUTAN --}}
                                <input form="reorder-form"
                                       class="h-6 w-12 rounded border-slate-300 p-0 text-center text-xs font-bold"
                                       name="order[{{ $question->id }}]"
                                       type="number"
                                       min="1"
                                       value="{{ $question->urutan }}"
                                       title="Ganti nomor urut">


                                {{-- TIPE --}}
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">

                                    {{ $question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}

                                </span>


                                {{-- BOBOT --}}
                                <span class="text-slate-400">

                                    Bobot:

                                    <strong class="text-slate-700">
                                        {{ $question->bobot }}
                                    </strong>

                                </span>

                            </div>


                            {{-- AKSI --}}
                            <div class="flex items-center gap-2">
                                @if (! $examLocked)
                                    {{-- EDIT --}}
                                    <a href="{{ route('guru.soal.edit', [$exam, $question]) }}"
                                       class="font-semibold text-blue-600 hover:text-blue-800">

                                        Edit

                                    </a>


                                    <span class="text-slate-300">&bull;</span>


                                    {{-- DUPLIKAT --}}
                                    <form method="POST"
                                          action="{{ route('guru.soal.duplicate', [$exam, $question]) }}"
                                          class="inline">

                                        @csrf

                                        <button type="submit"
                                                class="font-semibold text-slate-600 hover:text-slate-900">

                                            Duplikat

                                        </button>

                                    </form>


                                    <span class="text-slate-300">&bull;</span>


                                    {{-- HAPUS --}}
                                    <form method="POST"
                                          action="{{ route('guru.soal.destroy', [$exam, $question]) }}"
                                          onsubmit="return confirm('Hapus soal #{{ $question->urutan }}?')"
                                          class="inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="font-semibold text-rose-600 hover:text-rose-800">

                                            Hapus

                                        </button>

                                    </form>
                                @else
                                    <span class="text-xs font-semibold text-slate-400">Read-only</span>
                                @endif
                            </div>

                        </div>


                        {{-- PERTANYAAN --}}
                        <div class="mt-3 whitespace-pre-line text-sm font-medium leading-relaxed text-slate-800">

                            {{ $question->pertanyaan }}

                        </div>


                        {{-- PILIHAN GANDA --}}
                        @if ($question->tipe === 'pg')

                            <div class="mt-3 space-y-1.5 text-xs">

                                @foreach (['a', 'b', 'c', 'd', 'e'] as $option)

                                    @if ($question->{'opsi_'.$option})

                                        @php
                                            $isKey = strtoupper($question->kunci) === strtoupper($option);
                                        @endphp


                                        <div class="flex items-center gap-2 rounded-lg px-3 py-2 transition
                                            {{ $isKey
                                                ? 'border border-emerald-200 bg-emerald-50 font-bold text-emerald-900'
                                                : 'bg-slate-50 text-slate-700' }}">

                                            {{-- HURUF --}}
                                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded text-[11px] font-bold
                                                {{ $isKey
                                                    ? 'bg-emerald-600 text-white'
                                                    : 'bg-slate-200 text-slate-700' }}">

                                                {{ strtoupper($option) }}

                                            </span>


                                            {{-- ISI OPSI --}}
                                            <span class="flex-1">

                                                {{ $question->{'opsi_'.$option} }}

                                            </span>


                                            {{-- KUNCI BENAR --}}
                                            @if ($isKey)

                                                <span class="rounded bg-emerald-100/80 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700">

                                                    ✓ Kunci Benar

                                                </span>

                                            @endif

                                        </div>

                                    @endif

                                @endforeach

                            </div>


                        {{-- ESSAY --}}
                        @elseif ($question->petunjuk_jawaban)

                            <div class="mt-3 rounded-lg bg-purple-50 p-2.5 text-xs text-purple-800">

                                <strong>Petunjuk Rubrik:</strong>

                                {{ $question->petunjuk_jawaban }}

                            </div>

                        @endif

                    </div>


                @empty

                    {{-- BELUM ADA SOAL --}}
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">

                        <p class="text-sm font-bold text-slate-800">
                            Belum ada soal pada ujian ini.
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Klik tombol di bawah untuk membuat soal baru atau import dari bank soal.
                        </p>


                        <div class="mt-4 flex justify-center gap-2">

                            <a href="{{ route('guru.soal.create', $exam) }}"
                               class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">

                                + Tambah Soal Manual

                            </a>


                            <button type="button"
                                    @click="showBank = true"
                                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">

                                Ambil dari Bank Soal

                            </button>

                        </div>

                    </div>

                @endforelse

            </div>


            {{-- ========================================================= --}}
            {{-- PAGINATION --}}
            {{-- ========================================================= --}}

            @if ($questions->total() > 0)

                <div class="flex justify-end pt-2">
                    <div>
                        {{ $questions->links() }}
                    </div>
                </div>

            @endif

        </div>

    </div>

</x-app-layout>