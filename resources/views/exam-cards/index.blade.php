<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kartu Ujian</h2>
            <p class="mt-0.5 text-sm text-slate-500">Pilih ujian dan kelas untuk mencetak kartu peserta.</p>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ modeDuduk: '{{ request('mode_duduk', 'otomatis') }}' }">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <form method="GET" action="{{ route('kartu-ujian.index') }}">
                @csrf
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-6 text-lg font-bold text-slate-800">Form Pembuatan Kartu Ujian</h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Left Column -->
                        <div class="space-y-5">
                            <div>
                                <label for="nama_ujian" class="mb-2 block text-sm font-semibold text-slate-700">Nama Ujian</label>
                                <input type="text" id="nama_ujian" name="nama_ujian" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: PAS PENILAIAN AKHIR SEMESTER 2026/2027" required value="{{ request('nama_ujian') }}">
                            </div>
                            
                            <div>
                                <label for="ruangan" class="mb-2 block text-sm font-semibold text-slate-700">Kode Ruangan</label>
                                <input type="text" id="ruangan" name="ruangan" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: R-01" required value="{{ request('ruangan') }}">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Tempat Duduk</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="mode_duduk" value="otomatis" x-model="modeDuduk" class="text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-slate-700">Otomatis (01, 02...)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="mode_duduk" value="manual" x-model="modeDuduk" class="text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-slate-700">Manual / Custom</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-5">
                            <div>
                                <label for="kelas_id" class="mb-2 block text-sm font-semibold text-slate-700">Pilih Kelas</label>
                                <select id="kelas_id" name="kelas_id" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="">Pilih kelas</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" @selected($selectedClass === $class->id)>{{ $class->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($namaUjian && $selectedClass && $ruangan)
                                <div class="flex items-start gap-3 rounded-xl bg-blue-50 p-4 text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm">Jumlah siswa dalam kelas ini:</p>
                                        <p class="text-base font-bold">{{ $participantCount }} siswa</p>
                                    </div>
                                </div>
                                
                                @if($printStatus)
                                <div class="flex items-start gap-3 rounded-xl bg-emerald-50 p-4 text-emerald-800 border border-emerald-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-bold">✓ Kartu ujian kelas {{ $printStatus->kelas->nama_kelas ?? '' }} sudah dicetak</p>
                                        <p class="text-xs mt-1">{{ $printStatus->jumlah_kartu }} kartu &bull; {{ $printStatus->updated_at->format('j F Y') }}</p>
                                    </div>
                                </div>
                                @else
                                <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-4 text-slate-600 border border-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold">Belum dicetak</p>
                                        <p class="text-xs mt-1">Kartu ujian untuk kombinasi ini belum pernah dicetak.</p>
                                    </div>
                                </div>
                                @endif
                                
                            @else
                                <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-4 text-slate-500 border border-slate-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm">Silakan isi formulir untuk melihat jumlah peserta.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('kartu-ujian.index') }}" class="inline-flex h-11 w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-6 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 sm:w-auto">Reset</a>
                        @if ($namaUjian && $selectedClass && $ruangan && $participantCount > 0)
                            <button type="submit" formmethod="POST" formaction="{{ route('kartu-ujian.preview') }}" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                {{ $printStatus ? 'Cetak Ulang' : 'Buat & Preview Kartu' }}
                            </button>
                        @else
                            <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Tampilkan
                            </button>
                        @endif
                    </div>
                </div>

                @if ($participantCount > 0)
                    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-semibold text-slate-900">Tempat Duduk</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-slate-900">NISN</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-slate-900">Nama Lengkap</th>
                                    <th scope="col" class="px-6 py-4 font-semibold text-slate-900">Username</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($students as $index => $student)
                                    <tr class="hover:bg-slate-50">
                                        <td class="whitespace-nowrap px-6 py-4 text-slate-500">
                                            <span x-show="modeDuduk === 'otomatis'">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                            <input x-cloak x-show="modeDuduk === 'manual'" type="text" name="tempat_duduk[{{ $student->id }}]" class="w-20 rounded-md border-slate-300 px-2 py-1 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('tempat_duduk.'.$student->id, str_pad($index + 1, 2, '0', STR_PAD_LEFT)) }}" placeholder="01">
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-slate-500">{{ $student->nisn ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">{{ $student->nama }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-slate-500">{{ $student->user->email ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-app-layout>
