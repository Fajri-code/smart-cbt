<x-app-layout>
    <x-slot name="header"><div class="flex items-center justify-between gap-4"><div><p class="text-sm font-medium text-amber-600">Bank Soal</p><h2 class="text-xl font-semibold leading-tight text-slate-800">{{ $bank->nama }}</h2></div><a class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" href="{{ route('guru.ujian.index') }}">Ke Ujian Saya</a></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        @if (session('success'))<div class="rounded-md bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</div>@endif
        
        @if ($errors->any())<div class="rounded-md bg-rose-50 p-4 text-sm text-rose-700"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        
        <!-- Action Bar -->
        <div x-data="{ showImportModal: false }" class="flex flex-wrap items-center justify-end gap-3 rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
            <button @click="showImportModal = true" class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Soal
            </button>
            <a href="{{ route('guru.bank.export-soal', $bank) }}" class="inline-flex items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Soal
            </a>

            <!-- Import Modal -->
            <div x-cloak x-show="showImportModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div x-show="showImportModal" x-transition.opacity x-transition:enter.duration.300ms x-transition:leave.duration.200ms @click.away="showImportModal = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <form action="{{ route('guru.bank.import', $bank) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                            <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title">Import Soal dari Excel</h3>
                                            <div class="mt-4 space-y-4">
                                                <div class="rounded-lg bg-blue-50 p-4 border border-blue-100">
                                                    <h4 class="text-sm font-semibold text-blue-800">Langkah 1: Download Template</h4>
                                                    <p class="text-xs text-blue-600 mt-1">Gunakan template ini untuk memastikan format kolom sesuai (Pertanyaan, Opsi, dll).</p>
                                                    <a href="{{ route('guru.bank.import-template', $bank) }}" class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-blue-700 hover:text-blue-800 hover:underline">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Download Template.xlsx
                                                    </a>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-slate-800">Langkah 2: Upload File</h4>
                                                    <p class="text-xs text-slate-500 mt-1 mb-2">Pilih file Excel yang sudah Anda isi.</p>
                                                    <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto">Mulai Import</button>
                                    <button type="button" @click="showImportModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm"><h3 class="font-semibold text-slate-900">Tambah Soal ke Bank</h3><form class="mt-4 grid gap-4 md:grid-cols-2" method="POST" action="{{ route('guru.bank.question.store', $bank) }}">@csrf<select class="rounded-md border-slate-300" name="tipe" required><option value="pg">Pilihan Ganda</option><option value="essay_1">Essay Bagian 1</option><option value="essay_2">Essay Bagian 2</option></select><input class="rounded-md border-slate-300" name="bobot" type="number" min="0" step="0.01" value="1" required><textarea class="md:col-span-2 rounded-md border-slate-300" name="pertanyaan" rows="3" placeholder="Pertanyaan" required></textarea><textarea class="md:col-span-2 rounded-md border-slate-300" name="petunjuk_jawaban" rows="2" placeholder="Petunjuk jawaban essay (opsional)"></textarea><div class="grid gap-3 sm:grid-cols-5 md:col-span-2">@foreach (['a','b','c','d','e'] as $option)<input class="rounded-md border-slate-300" name="opsi_{{ $option }}" placeholder="Opsi {{ strtoupper($option) }}">@endforeach</div><select class="rounded-md border-slate-300" name="kunci"><option value="">Tanpa kunci</option>@foreach (['A','B','C','D','E'] as $key)<option>{{ $key }}</option>@endforeach</select><div><button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white" type="submit">Simpan ke Bank</button></div></form></div>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200 text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-6 py-3">Tipe</th><th class="px-6 py-3">Pertanyaan</th><th class="px-6 py-3">Pilihan Jawaban</th><th class="px-6 py-3">Kunci</th><th class="px-6 py-3">Bobot</th><th class="px-6 py-3">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse ($questions as $question)<tr><td class="px-6 py-4">{{ strtoupper(str_replace('_', ' ', $question->tipe)) }}</td><td class="max-w-2xl px-6 py-4">{{ Str::limit($question->pertanyaan, 140) }}</td><td class="px-6 py-4"><div class="space-y-1 text-xs">@foreach (['a', 'b', 'c', 'd', 'e'] as $option)@if ($question->{'opsi_'.$option})<p><span class="font-semibold">{{ strtoupper($option) }}.</span> {{ $question->{'opsi_'.$option} }}</p>@endif @endforeach</div></td><td class="px-6 py-4 font-semibold">{{ $question->kunci ?: '-' }}</td><td class="px-6 py-4">{{ $question->bobot }}</td><td class="px-6 py-4"><form method="POST" action="{{ route('guru.bank.question.destroy', [$bank, $question]) }}">@csrf @method('DELETE')<button class="text-red-600" type="submit">Hapus</button></form></td></tr>@empty<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada soal dalam bank ini.</td></tr>@endforelse</tbody></table></div><div class="p-6">{{ $questions->links() }}</div></div>
    </div></div>
</x-app-layout>
