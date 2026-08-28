<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-slate-800">Edit Ujian</h2></x-slot>
    <div class="py-10"><div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        @if ($exam->questions()->exists())<div class="mb-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Ujian ini sudah memiliki soal. Jenis ujian tidak dapat diubah.</div>@endif
        <div class="rounded-lg bg-white p-6 shadow-sm sm:p-8"><form method="POST" action="{{ route('ujian.update', $exam) }}">@method('PUT') @include('ujian._form')</form></div>
    </div></div>
</x-app-layout>