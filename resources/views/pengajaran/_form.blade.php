<div class="grid gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="guru_id" :value="__('Guru')" />
        <select id="guru_id" name="guru_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih guru</option>
            @foreach ($gurus as $guru)
                <option value="{{ $guru->id }}" @selected((string) old('guru_id', $pengajaran->guru_id ?? '') === (string) $guru->id)>{{ $guru->nama }}{{ $guru->nip ? ' - '.$guru->nip : '' }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('guru_id')" />
    </div>

    <div>
        <x-input-label for="mata_pelajaran_id" :value="__('Mata Pelajaran')" />
        <select id="mata_pelajaran_id" name="mata_pelajaran_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih mata pelajaran</option>
            @foreach ($mataPelajarans as $mataPelajaran)
                <option value="{{ $mataPelajaran->id }}" @selected((string) old('mata_pelajaran_id', $pengajaran->mata_pelajaran_id ?? '') === (string) $mataPelajaran->id)>{{ $mataPelajaran->kode }} - {{ $mataPelajaran->nama }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('mata_pelajaran_id')" />
    </div>

    <div>
        <x-input-label for="kelas_id" :value="__('Kelas')" />
        <select id="kelas_id" name="kelas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih kelas</option>
            @foreach ($kelass as $kelas)
                <option value="{{ $kelas->id }}" @selected((string) old('kelas_id', $pengajaran->kelas_id ?? '') === (string) $kelas->id)>{{ $kelas->nama_kelas }} - Tingkat {{ $kelas->tingkat }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('kelas_id')" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $pengajaran->status ?? 'aktif') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('pengajaran.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>
