@php($isCreate = $isCreate ?? false)

<div class="grid gap-6 md:grid-cols-2">
	<div>
		<x-input-label for="nisn" :value="__('NISN')" />
		<x-text-input id="nisn" name="nisn" type="text" class="mt-1 block w-full" :value="old('nisn', $siswa->nisn ?? $siswa->nis ?? '')" required autofocus />
		<x-input-error class="mt-2" :messages="$errors->get('nisn')" />
	</div>

	<div>
		<x-input-label for="nama" :value="__('Nama')" />
		<x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $siswa->nama ?? '')" required />
		<x-input-error class="mt-2" :messages="$errors->get('nama')" />
	</div>

	<div>
		<x-input-label for="kelas_id" :value="__('Kelas sekolah (kosongkan untuk Tahasus)')" />
		<select id="kelas_id" name="kelas_id" class="mt-1 block w-full rounded-md border-gray-300">
			<option value="">Pilih kelas sekolah</option>
			@foreach ($kelass as $kelas)
				<option value="{{ $kelas->id }}" @selected(old('kelas_id', $siswa->kelas_id ?? '') == $kelas->id)>{{ $kelas->nama_kelas }}</option>
			@endforeach
		</select>
		<x-input-error class="mt-2" :messages="$errors->get('kelas_id')" />
	</div>

	<div>
		<x-input-label for="tahun_ajaran" :value="__('Tahun ajaran')" />
		<x-text-input id="tahun_ajaran" name="tahun_ajaran" type="text" class="mt-1 block w-full" :value="old('tahun_ajaran', $siswa->tahun_ajaran ?? '2026/2027')" required />
		<x-input-error class="mt-2" :messages="$errors->get('tahun_ajaran')" />
	</div>

	<div>
		<x-input-label for="email" :value="__('Email')" />
		<x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $siswa->user->email ?? '')" required />
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

	<div class="flex items-center gap-3">
		<input id="program_tahasus" name="program_tahasus" type="checkbox" value="1" class="rounded border-gray-300" @checked(old('program_tahasus', $siswa->program_tahasus ?? false))>
		<x-input-label for="program_tahasus" :value="__('Mengikuti program Tahasus (tanpa kelas sekolah)')" />
	</div>

	<div class="flex items-center gap-3">
		<input id="status_aktif" name="status_aktif" type="checkbox" value="1" class="rounded border-gray-300" @checked(old('status_aktif', $siswa->status_aktif ?? true))>
		<x-input-label for="status_aktif" :value="__('Siswa aktif')" />
	</div>
</div>

<div class="mt-6 flex items-center gap-3">
	<x-primary-button>{{ $submitLabel }}</x-primary-button>
	<a href="{{ route('siswa.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>