<div class="grid gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="nama_kelas" :value="__('Nama Kelas')" />
        <x-text-input id="nama_kelas" name="nama_kelas" type="text" class="mt-1 block w-full" :value="old('nama_kelas', $kelas->nama_kelas ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('nama_kelas')" />
    </div>

    <div>
        <x-input-label for="tingkat" :value="__('Tingkat')" />
        <x-text-input id="tingkat" name="tingkat" type="text" class="mt-1 block w-full" placeholder="Contoh: X, XI, XII" :value="old('tingkat', $kelas->tingkat ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('tingkat')" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $kelas->status ?? 'aktif') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('kelas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>
