<div class="space-y-6">
    <div>
        <x-input-label for="kode" :value="__('Kode')" />
        <x-text-input id="kode" name="kode" type="text" class="mt-1 block w-full" :value="old('kode', $mataPelajaran->kode ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('kode')" />
    </div>

    <div>
        <x-input-label for="nama" :value="__('Nama Mata Pelajaran')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $mataPelajaran->nama ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
    </div>

    <div>
        <x-input-label for="deskripsi" :value="__('Deskripsi')" />
        <textarea id="deskripsi" name="deskripsi" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $mataPelajaran->deskripsi ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('deskripsi')" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('mata-pelajaran.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>
