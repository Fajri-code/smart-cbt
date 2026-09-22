<x-app-layout>
    <x-slot name="header"><div><h2 class="text-xl font-bold text-slate-900">Dashboard Siswa</h2><p class="mt-0.5 text-sm text-slate-500">Pantau jadwal dan hasil ujian Anda.</p></div></x-slot>
    <div class="py-6"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-sm sm:p-7"><p class="text-sm font-medium text-slate-600">Selamat datang kembali</p><h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $siswa->nama ?: auth()->user()->name }}</h1><p class="mt-2 text-sm text-slate-600">{{ $siswa->kelasData?->nama_kelas ?? $siswa->kelas ?? 'Kelas belum ditentukan' }} <span class="mx-1 text-slate-300">·</span> NISN {{ $siswa->nisn ?: $siswa->nis }}</p></section>
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach ([['Ujian Hari Ini', $todayExams->count(), 'bg-blue-50', 'text-blue-700'], ['Ujian Mendatang', $upcomingExams->count(), 'bg-amber-50', 'text-amber-700'], ['Ujian Selesai', $completedExams->count(), 'bg-emerald-50', 'text-emerald-700']] as [$label, $count, $background, $color])
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $count }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $background }} {{ $color }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.8" d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm3 5h8M8 13h8M8 17h5"/></svg></span></div></div>
            @endforeach
        </section>
        @php
            $activityExam = $todayExams->first(function ($exam) {
                $attempt = $exam->examAttempts->first();

                return $attempt?->status === 'in_progress'
                    || ($exam->tanggal_selesai && now()->lt($exam->tanggal_selesai));
            }) ?? $upcomingExams->first();
        @endphp

        <section x-data="{
            isScanning: false,
            html5QrcodeScanner: null,
            scanMessage: 'Arahkan kamera HP ke QR Code ujian di depan kelas.',
            startScan() {
                this.isScanning = true;
                this.scanMessage = 'Meminta izin kamera...';
                this.$nextTick(() => {
                    if (typeof Html5Qrcode === 'undefined') {
                        this.scanMessage = 'Script kamera belum dimuat. Coba refresh halaman.';
                        return;
                    }
                    this.html5QrcodeScanner = new Html5Qrcode('reader');
                    this.html5QrcodeScanner.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            this.html5QrcodeScanner.stop();
                            this.isScanning = false;
                            this.scanMessage = 'QR ditemukan! Mengarahkan...';
                            try {
                                const qrUrl = new URL(decodedText);
                                window.location.href = qrUrl.pathname + qrUrl.search;
                            } catch (e) {
                                window.location.href = decodedText;
                            }
                        },
                        () => { this.scanMessage = 'Sedang memindai QR Code... Arahkan kamera ke layar.'; }
                    ).catch((err) => {
                        this.scanMessage = 'Gagal mengakses kamera. Pastikan browser diizinkan menggunakan kamera.';
                        console.error(err);
                    });
                });
            },
            stopScan() {
                if (this.html5QrcodeScanner) {
                    this.html5QrcodeScanner.stop().then(() => { this.isScanning = false; });
                } else {
                    this.isScanning = false;
                }
            }
        }" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 px-5 py-4 bg-slate-50">
                <div class="flex-1">
                    <h2 class="font-bold text-slate-900">Scan QR Ujian</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Scan dari layar guru untuk langsung masuk kelas ujian.</p>
                </div>
                <div class="shrink-0">
                    <button @click="startScan()" x-show="!isScanning" type="button" class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Buka Kamera
                    </button>
                    <button @click="stopScan()" x-show="isScanning" type="button" style="display: none;" class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-red-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tutup Kamera
                    </button>
                </div>
            </div>
            
            <div x-show="isScanning" style="display: none;" class="p-5 border-b border-slate-100">
                <div id="reader" class="mx-auto w-full max-w-sm rounded-lg overflow-hidden border-2 border-dashed border-emerald-500"></div>
                <p class="text-center mt-3 text-xs text-slate-500" x-text="scanMessage"></p>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div><h2 class="font-bold text-slate-900">Aktivitas Ujian</h2><p class="mt-0.5 text-xs text-slate-500">Ujian yang paling relevan untuk Anda saat ini.</p></div>
                <a href="{{ route('siswa.ujian.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Buka Ujian Saya</a>
            </div>
            @if ($activityExam)
                @php
                    $activityAttempt = $activityExam->examAttempts->first();
                    $activityAction = $activityAttempt?->status === 'in_progress' ? 'Lanjutkan' : 'Lihat Detail';
                @endphp
                <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-blue-600">{{ $activityExam->mataPelajaran?->nama ?? 'Mata Pelajaran' }}</p><h3 class="mt-1 text-lg font-bold text-slate-900">{{ $activityExam->nama }}</h3><p class="mt-1 text-sm text-slate-500">{{ $activityExam->kelasData?->nama_kelas ?? $activityExam->kelas ?? '-' }} <span class="mx-1 text-slate-300">·</span> {{ $activityExam->tanggal_mulai?->format('d M Y, H:i') ?? 'Jadwal belum diatur' }} <span class="mx-1 text-slate-300">·</span> {{ $activityExam->durasi_menit }} menit</p></div>
                    <a href="{{ route('siswa.ujian.show', $activityExam) }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700">{{ $activityAction }}</a>
                </div>
            @else
                <p class="px-5 py-4 text-sm text-slate-500">Belum ada ujian yang perlu dikerjakan.</p>
            @endif
        </section>
    </div></div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    @endpush
</x-app-layout>