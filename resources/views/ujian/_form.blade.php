@php
    $exam = $exam ?? null;
@endphp

@csrf

<div class="grid gap-6 md:grid-cols-2">

    {{-- Nama Ujian --}}
    <div class="md:col-span-2">
        <label for="nama" class="mb-2 block text-sm font-medium text-slate-700">
            Nama Ujian
        </label>

        <input
            id="nama"
            name="nama"
            type="text"
            value="{{ old('nama', $exam->nama ?? '') }}"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            placeholder="Penilaian Tengah Semester Matematika"
            required
        >

        @error('nama')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Tahun Ajaran --}}
    <div>
        <label for="tahun_ajaran" class="mb-2 block text-sm font-medium text-slate-700">
            Tahun Ajaran
        </label>

        <input
            id="tahun_ajaran"
            name="tahun_ajaran"
            type="text"
            value="{{ old('tahun_ajaran', $exam->tahun_ajaran ?? '') }}"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            placeholder="2026/2027"
            maxlength="20"
        >

        @error('tahun_ajaran')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Semester --}}
    <div>
        <label for="semester" class="mb-2 block text-sm font-medium text-slate-700">
            Semester
        </label>

        <select
            id="semester"
            name="semester"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
        >
            <option value="">Pilih semester</option>
            <option value="ganjil" @selected(old('semester', $exam->semester ?? '') === 'ganjil')>Ganjil</option>
            <option value="genap" @selected(old('semester', $exam->semester ?? '') === 'genap')>Genap</option>
        </select>

        @error('semester')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Mata Pelajaran --}}
    <div>
        <label for="mata_pelajaran_id" class="mb-2 block text-sm font-medium text-slate-700">
            Mata Pelajaran
        </label>

        <select
            id="mata_pelajaran_id"
            name="mata_pelajaran_id"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            required
        >
            <option value="">Pilih mata pelajaran</option>

            @foreach ($mataPelajarans as $mataPelajaran)
                <option
                    value="{{ $mataPelajaran->id }}"
                    @selected(old('mata_pelajaran_id', $exam->mata_pelajaran_id ?? '') == $mataPelajaran->id)
                >
                    {{ $mataPelajaran->nama }}
                </option>
            @endforeach
        </select>

        @error('mata_pelajaran_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Guru Penanggung Jawab --}}
    <div>
        <label for="guru_id" class="mb-2 block text-sm font-medium text-slate-700">
            Guru Penanggung Jawab
        </label>

        <select
            id="guru_id"
            name="guru_id"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            required
        >
            <option value="">Pilih guru</option>

            @foreach ($gurus as $guru)
                <option
                    value="{{ $guru->id }}"
                    @selected(old('guru_id', $exam->guru_id ?? '') == $guru->id)
                >
                    {{ $guru->nama }}
                </option>
            @endforeach
        </select>

        @error('guru_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Guru Pengawas --}}
    <div>
        <label for="guru_pengawas_id" class="mb-2 block text-sm font-medium text-slate-700">
            Guru Pengawas <span class="font-normal text-slate-400">(otomatis mengikuti PIC)</span>
        </label>

        <select
            id="guru_pengawas_id"
            name="guru_pengawas_id"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            required
        >
            <option value="">Pilih guru pengawas</option>

            @foreach ($gurus as $guru)
                <option
                    value="{{ $guru->id }}"
                    @selected(old('guru_pengawas_id', $exam->guru_pengawas_id ?? '') == $guru->id)
                >
                    {{ $guru->nama }}
                </option>
            @endforeach
        </select>

        @error('guru_pengawas_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Kelas --}}
    <div>
        <label for="kelas_id" class="mb-2 block text-sm font-medium text-slate-700">
            Kelas
        </label>

        <select
            id="kelas_id"
            name="kelas_id"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            required
        >
            <option value="">Pilih kelas</option>

            @foreach ($kelasData as $kelas)
                <option
                    value="{{ $kelas->id }}"
                    @selected(old('kelas_id', $exam->kelas_id ?? '') == $kelas->id)
                >
                    {{ $kelas->nama_kelas }}
                </option>
            @endforeach
        </select>

        @error('kelas_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Ruangan --}}
    <div>
        <label for="ruangan" class="mb-2 block text-sm font-medium text-slate-700">
            Ruangan
        </label>

        <input
            id="ruangan"
            name="ruangan"
            type="text"
            value="{{ old('ruangan', $exam->ruangan ?? '') }}"
            class="w-full rounded-md border-slate-300"
            placeholder="Lab 1"
            required
        >

        @error('ruangan')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Tanggal Mulai --}}
    <div>
        <label for="tanggal_mulai_tanggal" class="mb-2 block text-sm font-medium text-slate-700">
            Tanggal Mulai
        </label>

        <input
            id="tanggal_mulai_tanggal"
            name="tanggal_mulai_tanggal"
            type="date"
            value="{{ old('tanggal_mulai_tanggal', $exam?->tanggal_mulai?->format('Y-m-d')) }}"
            class="w-full rounded-md border-slate-300"
            required
        >

        @error('tanggal_mulai_tanggal')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Jam Mulai --}}
    <div>
        <label for="tanggal_mulai_jam" class="mb-2 block text-sm font-medium text-slate-700">
            Jam Mulai (24 jam)
        </label>

       <input
    id="tanggal_mulai_jam"
    name="tanggal_mulai_jam"
    type="text"
    value="{{ old('tanggal_mulai_jam', $exam?->tanggal_mulai?->format('H:i')) }}"
    class="w-full rounded-md border-slate-300"
    placeholder="07:00"
    inputmode="numeric"
    pattern="([01][0-9]|2[0-3]):[0-5][0-9]"
    maxlength="5"
    required
>

        @error('tanggal_mulai_jam')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Tanggal Selesai --}}
    <div>
        <label for="tanggal_selesai_tanggal" class="mb-2 block text-sm font-medium text-slate-700">
            Tanggal Selesai
        </label>

        <input
            id="tanggal_selesai_tanggal"
            name="tanggal_selesai_tanggal"
            type="date"
            value="{{ old('tanggal_selesai_tanggal', $exam?->tanggal_selesai?->format('Y-m-d')) }}"
            class="w-full rounded-md border-slate-300"
            required
        >

        @error('tanggal_selesai_tanggal')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Jam Selesai --}}
    <div>
        <label for="tanggal_selesai_jam" class="mb-2 block text-sm font-medium text-slate-700">
            Jam Selesai (24 jam)
        </label>

      <input
    id="tanggal_selesai_jam"
    name="tanggal_selesai_jam"
    type="text"
    value="{{ old('tanggal_selesai_jam', $exam?->tanggal_selesai?->format('H:i')) }}"
    class="w-full rounded-md border-slate-300"
    placeholder="15:00"
    inputmode="numeric"
    pattern="([01][0-9]|2[0-3]):[0-5][0-9]"
    maxlength="5"
    required
>

        @error('tanggal_selesai_jam')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- Durasi --}}
    <div>

        <label for="durasi_menit" class="mb-2 block text-sm font-medium text-slate-700">
            Durasi
        </label>

        <div class="flex items-center gap-3">

            <input
                id="durasi_menit"
                name="durasi_menit"
                type="number"
                min="1"
                value="{{ old('durasi_menit', $exam->durasi_menit ?? '') }}"
                class="w-full rounded-md border-slate-300 bg-slate-50 focus:border-blue-500 focus:ring-blue-500"
                readonly
                required
            >

            <span class="text-sm text-slate-500">
                menit
            </span>

        </div>

        <p id="durasi_info" class="mt-1 text-xs text-slate-500">
            Durasi dihitung otomatis dari jam mulai dan jam selesai.
        </p>

        @error('durasi_menit')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>


    {{-- Status --}}
    <div>

        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
            Status
        </label>

        <select
            id="status"
            name="status"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            required
        >

            @foreach ([
                'draft' => 'Draft',
                'aktif' => 'Aktif'
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(old('status', $exam->status ?? 'draft') === $value)
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>


    {{-- Deskripsi --}}
    <div class="md:col-span-2">

        <label for="deskripsi" class="mb-2 block text-sm font-medium text-slate-700">
            Deskripsi / Petunjuk
        </label>

        <textarea
            id="deskripsi"
            name="deskripsi"
            rows="4"
            class="w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            placeholder="Bacalah setiap soal dengan teliti sebelum menjawab."
        >{{ old('deskripsi', $exam->deskripsi ?? '') }}</textarea>

        @error('deskripsi')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

</div>


{{-- Tombol --}}
<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">

    <a
        href="{{ route('ujian.index') }}"
        class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
    >
        Batal
    </a>

    <button
        type="submit"
        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
    >
        Simpan Ujian
    </button>

</div>


{{-- JavaScript Durasi Otomatis --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const startDate = document.getElementById('tanggal_mulai_tanggal');
    const startTime = document.getElementById('tanggal_mulai_jam');

    const endDate = document.getElementById('tanggal_selesai_tanggal');
    const endTime = document.getElementById('tanggal_selesai_jam');

    const duration = document.getElementById('durasi_menit');
    const durationInfo = document.getElementById('durasi_info');
    const responsibleTeacher = document.getElementById('guru_id');
    const supervisor = document.getElementById('guru_pengawas_id');

    if (! supervisor.value && responsibleTeacher.value) {
        supervisor.value = responsibleTeacher.value;
    }

    responsibleTeacher.addEventListener('change', function () {
        if (! supervisor.dataset.manuallyChanged || ! supervisor.value) {
            supervisor.value = responsibleTeacher.value;
        }
    });

    supervisor.addEventListener('change', function () {
        supervisor.dataset.manuallyChanged = 'true';
    });


    function updateDuration() {

        // Kalau data belum lengkap, kosongkan durasi
        if (
            !startDate.value ||
            !startTime.value ||
            !endDate.value ||
            !endTime.value
        ) {
            duration.value = '';
            durationInfo.textContent =
                'Durasi dihitung otomatis dari jam mulai dan jam selesai.';
            return;
        }


        // Buat object tanggal
        const start = new Date(
            startDate.value + 'T' + startTime.value
        );

        const end = new Date(
            endDate.value + 'T' + endTime.value
        );


        // Hitung selisih waktu dalam milidetik
        const difference = end.getTime() - start.getTime();


        // Kalau jam selesai lebih kecil dari jam mulai
        if (difference <= 0) {

            duration.value = '';

            durationInfo.textContent =
                'Tanggal/jam selesai harus lebih besar dari tanggal/jam mulai.';

            durationInfo.classList.remove('text-slate-500');
            durationInfo.classList.add('text-red-600');

            return;
        }


        // Konversi milidetik ke menit
        const minutes = Math.round(
            difference / (1000 * 60)
        );


        // Masukkan hasil ke field durasi
        duration.value = minutes;


        // Keterangan
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;


        let text = 'Durasi otomatis: ';

        if (hours > 0) {
            text += hours + ' jam';

            if (remainingMinutes > 0) {
                text += ' ' + remainingMinutes + ' menit';
            }
        } else {
            text += minutes + ' menit';
        }


        durationInfo.textContent = text;

        durationInfo.classList.remove('text-red-600');
        durationInfo.classList.add('text-slate-500');
    }


    // Jalankan setiap kali field berubah
    startDate.addEventListener('change', updateDuration);
    startTime.addEventListener('change', updateDuration);

    endDate.addEventListener('change', updateDuration);
    endTime.addEventListener('change', updateDuration);


    // Jalankan saat halaman pertama kali dibuka
    updateDuration();

});
</script>