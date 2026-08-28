<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Master Data Guru') }}</h2>
            <a href="{{ route('guru.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">Tambah Guru</a>
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
                            <tr><th class="px-4 py-3">NIP</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($gurus as $guru)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $guru->nip ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $guru->nama }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $guru->user->email }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><div class="flex gap-3"><a class="text-blue-600 hover:text-blue-900" href="{{ route('guru.show', $guru) }}">Detail</a><a class="text-gray-600 hover:text-gray-900" href="{{ route('guru.edit', $guru) }}">Edit</a><form method="POST" action="{{ route('guru.destroy', $guru) }}" onsubmit="return confirm('Hapus data guru ini?')">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-900" type="submit">Hapus</button></form></div></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada data guru.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-6">{{ $gurus->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
