<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="mb-1 flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ auth()->user()->isGuru() ? route('guru.hasil.index') : route('hasil.index') }}" class="hover:text-slate-800">Daftar Hasil</a>
                <span>/</span>
                <span class="font-medium text-slate-800">Detail Siswa</span>
            </nav>
            <h2 class="text-xl font-bold text-slate-900">Riwayat Hasil: {{ $siswa->nama }}</h2>
            <p class="mt-0.5 text-sm text-slate-500">
                NIS: {{ $siswa->nis }} &bull; Kelas: {{ $siswa->kelasData?->nama_kelas ?? $siswa->kelas ?? '-' }}
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            
            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <a href="{{ auth()->user()->isGuru() ? route('guru.hasil.export.siswa', $siswa) : route('hasil.export.siswa', $siswa) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Nilai Siswa
                </a>
                
                <a href="{{ auth()->user()->isGuru() ? route('guru.hasil.index') : route('hasil.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    &larr; Kembali
                </a>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm mb-6">
                <div class="border-b border-slate-200 px-5 py-4 flex justify-between items-center flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-900">Rekap Jawaban: {{ $attempt->exam->nama ?? '-' }}</h3>
                        <p class="mt-1 text-sm text-slate-500">Mata Pelajaran: {{ $attempt->exam->mataPelajaran?->nama ?? '-' }} &bull; Nilai Akhir: <strong class="text-slate-800">{{ $attempt->nilai_akhir !== null ? number_format($attempt->nilai_akhir, 2) : '-' }}</strong></p>
                    </div>
                    <div>
                        <a href="{{ auth()->user()->isGuru() ? route('guru.hasil.export.rekap', $attempt->id) : route('hasil.export.rekap', $attempt->id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 shadow-sm transition hover:bg-blue-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Rekap Jawaban
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 w-16 text-center">No</th>
                                <th class="px-5 py-3">Tipe Soal</th>
                                <th class="px-5 py-3 w-1/3">Pertanyaan</th>
                                <th class="px-5 py-3 text-center">Jawaban Siswa</th>
                                <th class="px-5 py-3 text-center">Kunci</th>
                                <th class="px-5 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($answers as $index => $ans)
                                @php
                                    $isCorrect = $ans->is_correct;
                                    $hasAnswered = !empty($ans->jawaban);
                                    
                                    if (!$hasAnswered) {
                                        $statusText = 'Tidak Dijawab';
                                        $statusClass = 'bg-slate-100 text-slate-600';
                                    } elseif ($ans->question->tipe === 'pilihan_ganda' || $ans->question->tipe === 'pg') {
                                        $statusText = $isCorrect ? 'Benar' : 'Salah';
                                        $statusClass = $isCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800';
                                    } else {
                                        $statusText = $ans->sudah_dinilai ? 'Sudah Dinilai' : 'Belum Dinilai';
                                        $statusClass = $ans->sudah_dinilai ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4 text-center font-medium text-slate-900">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $ans->question->tipe === 'pilihan_ganda' || $ans->question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}</td>
                                <td class="px-5 py-4"><div class="line-clamp-2 text-slate-600" title="{{ strip_tags($ans->question->pertanyaan) }}">{!! Str::limit(strip_tags($ans->question->pertanyaan), 100) !!}</div></td>
                                    <td class="px-5 py-4 text-center font-bold text-slate-900">{{ $ans->jawaban ?: '-' }}</td>
                                    <td class="px-5 py-4 text-center text-slate-600">{{ $ans->question->tipe === 'pilihan_ganda' || $ans->question->tipe === 'pg' ? ($ans->question->kunci ?: '-') : '(Essay)' }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-500">Belum ada jawaban yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="font-bold text-slate-900">Riwayat Ujian</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $riwayatUjian->count() }} ujian tercatat.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Ujian</th>
                                <th class="px-5 py-3">Mata Pelajaran</th>
                                <th class="px-5 py-3">Tahun/Semester</th>
                                <th class="px-5 py-3 text-center">Nilai</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Mulai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($riwayatUjian as $ru)
                                @php
                                    $statusLabel = ['in_progress' => 'Sedang Berlangsung', 'submitted' => 'Selesai', 'expired' => 'Waktu Habis'][$ru->status] ?? ucfirst($ru->status);
                                    $statusClass = ['submitted' => 'bg-emerald-50 text-emerald-700', 'expired' => 'bg-amber-50 text-amber-700', 'in_progress' => 'bg-blue-50 text-blue-700'][$ru->status] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $ru->exam->nama ?? '-' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $ru->exam->mataPelajaran?->nama ?? '-' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $ru->exam->tahun_ajaran ?? '-' }} / {{ $ru->exam->semester ?? '-' }}</td>
                                    <td class="px-5 py-4 text-center text-lg font-black text-slate-900">{{ $ru->nilai_akhir !== null ? number_format($ru->nilai_akhir, 2) : '-' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">{{ $ru->started_at ? $ru->started_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-slate-500">Belum ada riwayat ujian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

