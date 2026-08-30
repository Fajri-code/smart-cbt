<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Master Data Siswa') }}</h2>
            <a href="{{ route('siswa.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">Tambah Siswa</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            <div class="mb-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Filter Data</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">Siswa berdasarkan kelas</h3>
                    </div>
                    <form class="flex flex-col gap-3 sm:flex-row" method="GET">
                        <select class="min-w-64 rounded-md border-slate-300" name="kelas" aria-label="Filter kelas">
                            <option value="">Semua kelas ({{ $siswas->total() }})</option>
                            @foreach ($kelasOptions as $kelas)
                                <option value="{{ $kelas->id }}" @selected((string) request('kelas') === (string) $kelas->id)>{{ $kelas->nama_kelas }} ({{ $kelasCounts[$kelas->id] ?? 0 }})</option>
                            @endforeach
                        </select>
                        <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" type="submit">Terapkan</button>
                        @if (request()->filled('kelas'))
                            <a class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ route('siswa.index') }}">Reset</a>
                        @endif
                    </form>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($kelasOptions as $kelas)
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ $kelas->nama_kelas }}: {{ $kelasCounts[$kelas->id] ?? 0 }}</span>
                    @endforeach
                </div>
            </div>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="text-xs uppercase tracking-wider text-gray-500">
                            <tr><th class="px-4 py-3">NISN</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Kelas</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($siswas as $siswa)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $siswa->nisn ?? $siswa->nis }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $siswa->nama }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $siswa->kelasData?->nama_kelas ?? $siswa->kelas ?? ($siswa->program_tahasus ? 'Tahasus' : '-') }} @if ($siswa->program_tahasus)<span class="ml-1 rounded-full bg-amber-100 px-2 py-1 text-xs text-amber-800">Program</span>@endif</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $siswa->user->email }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><div class="flex gap-3"><a class="text-blue-600 hover:text-blue-900" href="{{ route('siswa.show', $siswa) }}">Detail</a><a class="text-gray-600 hover:text-gray-900" href="{{ route('siswa.edit', $siswa) }}">Edit</a><form method="POST" action="{{ route('siswa.destroy', $siswa) }}" onsubmit="return confirm('Hapus data siswa ini?')">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-900" type="submit">Hapus</button></form></div></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data siswa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-6">{{ $siswas->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>