<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Master Data Mata Pelajaran') }}</h2>
            <a href="{{ route('mata-pelajaran.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">+ Tambah Mata Pelajaran</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="text-xs uppercase tracking-wider text-gray-500">
                            <tr><th class="px-4 py-3">Kode</th><th class="px-4 py-3">Nama Mata Pelajaran</th><th class="px-4 py-3">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($mataPelajarans as $mataPelajaran)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $mataPelajaran->kode }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $mataPelajaran->nama }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><div class="flex gap-3"><a class="text-blue-600 hover:text-blue-900" href="{{ route('mata-pelajaran.show', $mataPelajaran) }}">Detail</a><a class="text-gray-600 hover:text-gray-900" href="{{ route('mata-pelajaran.edit', $mataPelajaran) }}">Edit</a><form method="POST" action="{{ route('mata-pelajaran.destroy', $mataPelajaran) }}" onsubmit="return confirm('Hapus mata pelajaran ini?')">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-900" type="submit">Hapus</button></form></div></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada data mata pelajaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-6">{{ $mataPelajarans->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
