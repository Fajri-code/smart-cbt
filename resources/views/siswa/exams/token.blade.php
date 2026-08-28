<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-bold text-slate-900">Masukkan Token Ujian</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8"><div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-sm font-bold text-blue-600">{{ $exam->mataPelajaran?->nama }}</p>
        <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $exam->nama }}</h1>
        <p class="mt-3 text-sm text-slate-600">Masukkan token ujian dari guru atau pengawas untuk melanjutkan.</p>
        @if ($errors->any()) <p class="mt-5 rounded-lg bg-red-50 p-3 text-sm font-semibold text-red-700">{{ $errors->first('token') }}</p> @endif
        <form method="POST" action="{{ route('siswa.ujian.start', $exam) }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="token" class="block text-sm font-semibold text-slate-700">Token Ujian</label>
                <input id="token" name="token" required autofocus autocomplete="off" maxlength="20" value="{{ old('token') }}" class="mt-1 block w-full rounded-lg border-slate-300 uppercase" placeholder="Masukkan token">
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('siswa.ujian.show', $exam) }}" class="inline-flex rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700">Kembali</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">Masuk Ujian</button>
            </div>
        </form>
    </div></div></div>
</x-app-layout>