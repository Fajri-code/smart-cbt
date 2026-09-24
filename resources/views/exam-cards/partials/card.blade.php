<div class="relative overflow-hidden rounded-2xl border-2 border-slate-800 bg-white p-6 pb-4 shadow-sm">
    <!-- Header -->
    <div class="flex items-center gap-4 border-b-2 border-slate-800 pb-4">
        <!-- Logo -->
        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 overflow-hidden">
            @if(isset($setting) && $setting->logo_kiri)
                <!-- Karena diload via web (preview) / dompdf (pdf), kita cek base64 atau public_path kalau di PDF -->
                @php
                    $isPdf = isset($isPdf) ? $isPdf : false;
                    $logoPath = $isPdf ? public_path('storage/'.$setting->logo_kiri) : asset('storage/'.$setting->logo_kiri);
                @endphp
                <img src="{{ $logoPath }}" class="h-full w-full object-contain">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            @endif
        </div>
        <div class="text-left tracking-wide text-slate-900 sm:text-lg">
            <div class="text-xs font-bold leading-tight">{{ $setting->header_1 ?? 'PEMERINTAH PROVINSI' }}</div>
            <div class="text-xs font-bold leading-tight">{{ $setting->header_2 ?? 'DINAS PENDIDIKAN' }}</div>
            <div class="text-lg font-black leading-tight">{{ $setting->header_3 ?? 'NAMA SEKOLAH' }}</div>
            <div class="text-[10px] font-normal leading-tight">{{ $setting->header_4 ?? 'Alamat Lengkap' }}</div>
        </div>
    </div>

    <!-- Title -->
    <div class="mt-4 text-center">
        <h3 class="text-2xl font-black uppercase tracking-wider text-slate-900">{{ $setting->judul_kartu ?? 'KARTU PESERTA UJIAN' }}</h3>
    </div>

    <!-- Exam Info Box -->
    <div class="mx-auto mt-3 max-w-sm rounded-xl bg-slate-100 py-3 text-center text-sm font-bold text-slate-900">
        <div class="text-base uppercase">{{ $namaUjian }}</div>
    </div>

    <!-- Student Info -->
    <div class="mx-auto mt-6 max-w-sm">
        <table class="w-full text-left text-[14px] font-bold text-slate-900">
            <tbody>
                <tr>
                    <td class="w-32 py-1 align-top">Nama</td>
                    <td class="w-4 py-1 align-top">:</td>
                    <td class="py-1 uppercase">{{ $student->nama }}</td>
                </tr>
                <tr>
                    <td class="py-1 align-top">NISN</td>
                    <td class="py-1 align-top">:</td>
                    <td class="py-1">{{ $student->nisn ?? '-' }}</td>
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
                <tr>
                    <td class="py-1 align-top text-blue-700">Password CBT</td>
                    <td class="py-1 align-top text-blue-700">:</td>
                    <td class="py-1 text-blue-700">12345678</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- History Pembuatan / Titimangsa -->
    <div class="mt-6 flex justify-end text-sm text-slate-900">
        <div class="w-48 text-center">
            <p>{{ $setting->tempat_tanggal ?? 'Kota, Tanggal' }}</p>
            <p class="font-bold">{{ $setting->jabatan_penandatangan ?? 'Kepala Sekolah' }}</p>
            <div class="my-1 h-14 w-full flex items-center justify-center">
                @if(isset($setting) && $setting->ttd_image)
                    @php
                        $isPdf = isset($isPdf) ? $isPdf : false;
                        $ttdPath = $isPdf ? public_path('storage/'.$setting->ttd_image) : asset('storage/'.$setting->ttd_image);
                    @endphp
                    <img src="{{ $ttdPath }}" class="max-h-full object-contain">
                @endif
            </div>
            <p class="font-bold underline">{{ $setting->nama_penandatangan ?? 'Nama Penandatangan' }}</p>
            <p>{{ $setting->nip_penandatangan ?? 'NIP. -' }}</p>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="mt-4 border-t border-slate-800 pt-3 text-center text-xs font-medium italic text-slate-600">
        Kartu ini harap dibawa saat pelaksanaan ujian.
    </div>
</div>
