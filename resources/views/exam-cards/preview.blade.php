<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Preview Kartu Ujian</h2>
                <p class="mt-0.5 text-sm text-slate-500">{{ $kelas->nama_kelas }} · {{ $students->count() }} siswa</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('kartu-ujian.index', ['nama_ujian' => $namaUjian, 'ruangan' => $ruangan, 'kelas_id' => $kelas->id]) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
                <form action="{{ route('kartu-ujian.pdf') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="nama_ujian" value="{{ $namaUjian }}">
                    <input type="hidden" name="ruangan" value="{{ $ruangan }}">
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="mode_duduk" value="{{ request('mode_duduk', 'otomatis') }}">
                    @if(request()->has('tempat_duduk'))
                        @foreach(request('tempat_duduk') as $id => $bangku)
                            <input type="hidden" name="tempat_duduk[{{ $id }}]" value="{{ $bangku }}">
                        @endforeach
                    @endif
                    <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Cetak PDF</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-100 py-8">
        <div class="mx-auto max-w-4xl space-y-5 px-4 sm:px-6 lg:px-8">
            @foreach ($students as $student)
                <article class="mx-auto max-w-2xl border-2 border-slate-800 bg-white p-5 shadow-sm sm:p-7">
                    @include('exam-cards.partials.card', ['student' => $student, 'number' => $student->nomor_bangku, 'namaUjian' => $namaUjian, 'ruangan' => $ruangan, 'kelas' => $kelas])
                </article>
            @endforeach
        </div>
    </div>
</x-app-layout>
