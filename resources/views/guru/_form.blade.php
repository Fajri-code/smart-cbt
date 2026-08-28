@php($isCreate = $isCreate ?? false)

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="nip" :value="__('NIP')" />
        <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip', $guru->nip ?? '')" autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('nip')" />
    </div>

    <div>
        <x-input-label for="kode_guru" :value="__('Kode Guru')" />
        <x-text-input id="kode_guru" name="kode_guru" type="text" class="mt-1 block w-full" :value="old('kode_guru', $guru->kode_guru ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('kode_guru')" />
    </div>

    <div>
        <x-input-label for="nama" :value="__('Nama')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $guru->nama ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $guru->user->email ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Password')" />
        <input id="password" name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password" @if ($isCreate) required @endif>
        @if (!$isCreate)<p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>@endif
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
        <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password" @if ($isCreate) required @endif>
        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('guru.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>
