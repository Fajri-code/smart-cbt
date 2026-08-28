<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-slate-800">Tambah Ujian</h2></x-slot>
    <div class="py-10"><div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white p-6 shadow-sm sm:p-8"><form method="POST" action="{{ route('ujian.store') }}">@include('ujian._form')</form></div>
    </div></div>
</x-app-layout>