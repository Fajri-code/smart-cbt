<div class="relative overflow-hidden rounded-2xl border-2 border-slate-800 bg-white p-6 pb-4 shadow-sm">
    <!-- Header -->
    <div class="flex items-center gap-4 border-b-2 border-slate-800 pb-4">
        <!-- Logo placeholder (simulating the green logo) -->
        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-green-700 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="text-left font-black tracking-wide text-slate-900 sm:text-lg">
            <div class="leading-tight">SMP AL-MADINAH</div>
            <div class="leading-tight">ISLAMIC CENTER KKMB</div>
        </div>
    </div>

    <!-- Title -->
    <div class="mt-4 text-center">
        <h3 class="text-2xl font-black uppercase tracking-wider text-slate-900">KARTU UJIAN</h3>
    </div>

    <!-- Exam Info Box -->
    <div class="mx-auto mt-3 max-w-sm rounded-xl bg-slate-100 py-3 text-center text-sm font-bold text-slate-900">
        <div class="text-base uppercase">{{ $namaUjian }}</div>
    </div>

    <!-- Student Info -->
    <div class="mx-auto mt-6 max-w-sm">
        <table class="w-full text-left text-[15px] font-bold text-slate-900">
            <tbody>
                <tr>
                    <td class="w-32 py-1 align-top">Nama</td>
                    <td class="w-4 py-1 align-top">:</td>
                    <td class="py-1 uppercase">{{ $student->nama }}</td>
                </tr>
                <tr>
                    <td class="py-1 align-top">Kelas</td>
                    <td class="py-1 align-top">:</td>
                    <td class="py-1">{{ $kelas->nama_kelas }}</td>
                </tr>
                <tr>
                    <td class="py-1 align-top">Kode Ruangan</td>
                    <td class="py-1 align-top">:</td>
                    <td class="py-1">{{ $ruangan }}</td>
                </tr>
                <tr>
                    <td class="py-1 align-top">Tempat Duduk</td>
                    <td class="py-1 align-top">:</td>
                    <td class="py-1">{{ str_pad($number ?? $loop->iteration ?? 1, 2, '0', STR_PAD_LEFT) }}</td>
                </tr>
                @if($student->user && !empty($student->user->email))
                <tr>
                    <td class="py-1 align-top text-blue-700">Username CBT</td>
                    <td class="py-1 align-top text-blue-700">:</td>
                    <td class="py-1 text-blue-700">{{ $student->user->email }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Footer Note -->
    <div class="mt-8 border-t border-slate-800 pt-3 text-center text-xs font-medium italic text-slate-600">
        Kartu ini harap dibawa saat pelaksanaan ujian.
    </div>
</div>
