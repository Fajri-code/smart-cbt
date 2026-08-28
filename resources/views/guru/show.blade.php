<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Guru') }}</h2>
            <a href="{{ route('guru.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="divide-y divide-gray-100">
                    <div class="grid gap-2 py-4 sm:grid-cols-3"><dt class="font-medium text-gray-500">NIP</dt><dd class="sm:col-span-2">{{ $guru->nip ?: '-' }}</dd></div>
                    <div class="grid gap-2 py-4 sm:grid-cols-3"><dt class="font-medium text-gray-500">Kode Guru</dt><dd class="sm:col-span-2">{{ $guru->kode_guru }}</dd></div>
                    <div class="grid gap-2 py-4 sm:grid-cols-3"><dt class="font-medium text-gray-500">Nama</dt><dd class="sm:col-span-2">{{ $guru->nama }}</dd></div>
                    <div class="grid gap-2 py-4 sm:grid-cols-3"><dt class="font-medium text-gray-500">Email</dt><dd class="sm:col-span-2">{{ $guru->user->email }}</dd></div>
                </dl>
                <div class="mt-6"><a href="{{ route('guru.edit', $guru) }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">Edit Guru</a></div>
            </div>
        </div>
    </div>
</x-app-layout>
