<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                {{-- Breadcrumb --}}
                <nav class="mb-1 flex items-center gap-2 text-xs text-slate-500">
                    <a href="{{ route('guru.dashboard') }}" class="hover:text-slate-800">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('guru.ujian.index') }}" class="hover:text-slate-800">Ujian Saya</a>
                    <span>/</span>
                    <a href="{{ route('guru.soal.index', $exam) }}" class="hover:text-slate-800">Kelola Soal: {{ $exam->nama }}</a>
                    <span>/</span>
                    <span class="font-medium text-slate-800">{{ $question->exists ? 'Edit Soal' : 'Tambah Soal Baru' }}</span>
                </nav>
                <h2 class="text-xl font-bold leading-tight text-slate-900">
                    {{ $question->exists ? 'Edit Butir Soal' : 'Tambah Butir Soal Baru' }}
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Paket Ujian: <span class="font-semibold text-slate-700">{{ $exam->nama }}</span>
                    &bull; Mapel: <span class="font-semibold text-slate-700">{{ $exam->mataPelajaran?->nama ?? '-' }}</span>
                    &bull; Kelas: <span class="font-semibold text-slate-700">{{ $exam->kelasData?->nama_kelas ?? $exam->kelas ?? '-' }}</span>
                </p>
            </div>

            <div>
                <a href="{{ route('guru.soal.index', $exam) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Soal
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        tipe: '{{ old('tipe', $question->tipe ?: ($allowedTypes[0] ?? 'pg')) }}',
        kunci: '{{ old('kunci', $question->kunci ?? 'A') }}'
    }">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold">Mohon periksa data berikut:</p>
                                <ul class="mt-1 list-disc pl-5 text-xs">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ $question->exists ? route('guru.soal.update', [$exam, $question]) : route('guru.soal.store', $exam) }}" class="space-y-6">
                    @csrf
                    @if ($question->exists)
                        @method('PUT')
                    @endif

                    {{-- 1. PILIH TIPE SOAL --}}
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">
                            1. Tipe Soal
                        </label>
                        <div class="grid gap-3 sm:grid-cols-3">
                            @foreach (['pg' => 'Pilihan Ganda', 'essay_1' => 'Essay Bagian 1', 'essay_2' => 'Essay Bagian 2'] as $value => $label)
                                @if (in_array($value, $allowedTypes, true))
                                    <label class="relative flex cursor-pointer items-center justify-between rounded-xl border p-4 transition"
                                           :class="tipe === '{{ $value }}' ? 'border-blue-500 bg-blue-50/60 ring-2 ring-blue-100 font-bold text-blue-900' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="tipe" value="{{ $value }}" x-model="tipe" class="text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm font-semibold">{{ $label }}</span>
                                        </div>
                                        <span class="text-xs text-slate-400" x-show="tipe === '{{ $value }}'">✓ Dipilih</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. TEKS PERTANYAAN --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500" for="pertanyaan">
                                2. Teks Pertanyaan <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Gunakan bahasa yang jelas dan mudah dipahami siswa</span>
                        </div>
                        <textarea id="pertanyaan"
                                  name="pertanyaan"
                                  rows="4"
                                  required
                                  placeholder="Tuliskan butir soal pertanyaan di sini..."
                                  class="w-full rounded-xl border-slate-300 text-sm leading-relaxed text-slate-900 shadow-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400">{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
                    </div>

                    {{-- 3. PILIHAN GANDA (A, B, C, D, E) & KUNCI JAWABAN --}}
                    <div x-show="tipe === 'pg'" class="space-y-4 rounded-2xl border border-blue-100 bg-blue-50/30 p-5">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                                    3. Pilihan Jawaban & Kunci Jawaban
                                </h3>
                                <p class="text-xs text-slate-500">Isi pilihan opsi dan klik radio untuk menentukan <strong class="text-emerald-700">Kunci Jawaban Benar</strong>.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-600">Kunci Terpilih:</span>
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-xs font-bold text-white shadow-sm" x-text="kunci"></span>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $option)
                                @php
                                    $upperOpt = strtoupper($option);
                                @endphp
                                <div class="rounded-xl border p-3.5 transition"
                                     :class="kunci === '{{ $upperOpt }}' ? 'border-emerald-400 bg-emerald-50/80 ring-1 ring-emerald-400' : 'border-slate-200 bg-white'">
                                    <div class="flex items-start gap-3">
                                        {{-- Radio Kunci Jawaban --}}
                                        <label class="mt-2 flex cursor-pointer items-center gap-1.5" title="Jadikan opsi {{ $upperOpt }} sebagai kunci jawaban">
                                            <input type="radio"
                                                   name="kunci"
                                                   id="kunci_{{ $option }}"
                                                   value="{{ $upperOpt }}"
                                                   x-model="kunci"
                                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500"
                                                   :required="tipe === 'pg'">
                                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold shadow-sm"
                                                  :class="kunci === '{{ $upperOpt }}' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                                                {{ $upperOpt }}
                                            </span>
                                        </label>

                                        {{-- Text Input Opsi --}}
                                        <div class="flex-1">
                                            <textarea id="opsi_{{ $option }}"
                                                      name="opsi_{{ $option }}"
                                                      rows="2"
                                                      :required="tipe === 'pg' && '{{ $option }}' !== 'e'"
                                                      placeholder="Tuliskan teks jawaban untuk opsi {{ $upperOpt }}...{{ $option === 'e' ? ' (Opsional)' : '' }}"
                                                      class="w-full rounded-lg border-slate-300 text-xs text-slate-800 shadow-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400">{{ old('opsi_'.$option, $question->{'opsi_'.$option}) }}</textarea>
                                        </div>

                                        {{-- Badge Kunci --}}
                                        <div class="hidden sm:block pt-2">
                                            <span x-show="kunci === '{{ $upperOpt }}'"
                                                  class="inline-flex items-center gap-1 rounded-md bg-emerald-200/80 px-2 py-1 text-[11px] font-bold text-emerald-800">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                Kunci Benar
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. PETUNJUK JAWABAN (JIKA ESSAY) --}}
                    <div x-show="tipe !== 'pg'" class="rounded-2xl border border-purple-100 bg-purple-50/30 p-5 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-purple-900" for="petunjuk_jawaban">
                            Petunjuk Jawaban / Rubrik Penilaian (Essay)
                        </label>
                        <p class="text-xs text-slate-500">Tuliskan kata kunci atau panduan penilaian jawaban essay untuk mempermudah saat koreksi nilai.</p>
                        <textarea id="petunjuk_jawaban"
                                  name="petunjuk_jawaban"
                                  rows="3"
                                  placeholder="Contoh: Jawaban harus memuat poin-poin utama A, B, dan C..."
                                  class="w-full rounded-xl border-slate-300 text-xs text-slate-800 shadow-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400">{{ old('petunjuk_jawaban', $question->petunjuk_jawaban) }}</textarea>
                    </div>

                    {{-- 5. BOBOT NILAI --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="bobot">
                                    Bobot Nilai Soal Ini <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500">Nilai yang didapatkan siswa jika menjawab benar butir soal ini.</p>
                            </div>
                            <div class="w-full sm:w-48">
                                <div class="relative">
                                    <input id="bobot"
                                           name="bobot"
                                           type="number"
                                           min="0"
                                           step="0.01"
                                           required
                                           value="{{ old('bobot', $question->bobot ?? 1) }}"
                                           class="w-full rounded-xl border-slate-300 pr-12 text-sm font-bold text-slate-900 shadow-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                                    <span class="absolute inset-y-0 right-3 flex items-center text-xs font-bold text-slate-400">Poin</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI SIMPAN --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                        <a href="{{ route('guru.soal.index', $exam) }}"
                           class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-600/20 transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $question->exists ? 'Simpan Perubahan Soal' : 'Simpan Butir Soal Baru' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
