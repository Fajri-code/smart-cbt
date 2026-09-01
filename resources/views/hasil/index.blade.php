<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Hasil Ujian</h2>
            <p class="mt-0.5 text-sm text-slate-500">Pantau hasil pengerjaan siswa dan status pengumpulan ujian.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['Total Peserta', $totalPeserta, 'Attempt tercatat'], ['Ujian Selesai', $totalSelesai, 'Status submitted'], ['Rata-rata Nilai', $rataRata !== null ? number_format($rataRata, 2) : '-', 'Dari nilai tersedia'], ['Nilai Tertinggi', $nilaiTertinggi !== null ? number_format($nilaiTertinggi, 2) : '-', 'Dari nilai tersedia']] as $stat)
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $stat[0] }}</p>
                        <p class="mt-2 text-2xl font-black text-slate-900">{{ $stat[1] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $stat[2] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ auth()->user()->isGuru() ? route('guru.hasil.index') : route('hasil.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="lg:col-span-2">
                        <label for="search_siswa" class="mb-1 block text-sm font-medium text-slate-700">Nama Siswa</label>
                        <input id="search_siswa" name="search_siswa" value="{{ request('search_siswa') }}" type="search" placeholder="Cari nama siswa..." class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="exam_id" class="mb-1 block text-sm font-medium text-slate-700">Ujian</label>
                        <select id="exam_id" name="exam_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Ujian</option>
                            @foreach ($examOptions as $exam)
                                <option value="{{ $exam->id }}" @selected((string) request('exam_id') === (string) $exam->id)>{{ $exam->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="kelas_id" class="mb-1 block text-sm font-medium text-slate-700">Kelas</label>
                        <select id="kelas_id" name="kelas_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasOptions as $kelas)
                                <option value="{{ $kelas->id }}" @selected((string) request('kelas_id') === (string) $kelas->id)>{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="mata_pelajaran_id" class="mb-1 block text-sm font-medium text-slate-700">Mata Pelajaran</label>
                        <select id="mata_pelajaran_id" name="mata_pelajaran_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($mataPelajaranOptions as $mataPelajaran)
                                <option value="{{ $mataPelajaran->id }}" @selected((string) request('mata_pelajaran_id') === (string) $mataPelajaran->id)>{{ $mataPelajaran->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tahun_ajaran" class="mb-1 block text-sm font-medium text-slate-700">Tahun Ajaran</label>
                        <select id="tahun_ajaran" name="tahun_ajaran" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach ($tahunAjaranOptions as $tahunAjaran)
                                <option value="{{ $tahunAjaran }}" @selected(request('tahun_ajaran') === $tahunAjaran)>{{ $tahunAjaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="semester" class="mb-1 block text-sm font-medium text-slate-700">Semester</label>
                        <select id="semester" name="semester" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Semester</option>
                            <option value="ganjil" @selected(request('semester') === 'ganjil')>Ganjil</option>
                            <option value="genap" @selected(request('semester') === 'genap')>Genap</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                        <select id="status" name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            @foreach (['in_progress' => 'Sedang Berlangsung', 'submitted' => 'Selesai', 'expired' => 'Waktu Habis'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="mb-1 block text-sm font-medium text-slate-700">Mulai Tanggal</label>
                        <input id="start_date" name="start_date" value="{{ request('start_date') }}" type="date" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="mb-1 block text-sm font-medium text-slate-700">Sampai Tanggal</label>
                        <input id="end_date" name="end_date" value="{{ request('end_date') }}" type="date" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Terapkan Filter</button>
                        <a href="{{ auth()->user()->isGuru() ? route('guru.hasil.index') : route('hasil.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="font-bold text-slate-900">Daftar Hasil</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $attempts->total() }} hasil ditemukan.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr><th class="px-5 py-3">Siswa</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Ujian</th><th class="px-5 py-3">Mata Pelajaran</th><th class="px-5 py-3">Periode</th><th class="px-5 py-3 text-center">Nilai</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Mulai</th><th class="px-5 py-3">Selesai</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($attempts as $attempt)
                                @php
                                    $statusLabel = ['in_progress' => 'Sedang Berlangsung', 'submitted' => 'Selesai', 'expired' => 'Waktu Habis'][$attempt->status] ?? ucfirst(str_replace('_', ' ', $attempt->status));
                                    $statusClass = ['submitted' => 'bg-emerald-50 text-emerald-700', 'expired' => 'bg-amber-50 text-amber-700', 'in_progress' => 'bg-blue-50 text-blue-700'][$attempt->status] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $attempt->siswa->nama }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $attempt->exam->kelasData?->nama_kelas ?? $attempt->exam->kelas ?? '-' }}</td>
                                    <td class="min-w-48 px-5 py-4 font-medium text-slate-800">{{ $attempt->exam->nama }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $attempt->exam->mataPelajaran?->nama ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                        <div>{{ $attempt->exam->tahun_ajaran ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">{{ $attempt->exam->semester ? ucfirst($attempt->exam->semester) : 'Semester belum diatur' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-center text-lg font-black text-slate-900">{{ $attempt->nilai_akhir !== null ? number_format($attempt->nilai_akhir, 2) : '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $attempt->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $attempt->submitted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="px-5 py-12 text-center text-slate-500">Belum ada hasil yang sesuai dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">{{ $attempts->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>