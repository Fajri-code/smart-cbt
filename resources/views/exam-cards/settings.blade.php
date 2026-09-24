<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Pengaturan Kartu Ujian</h2>
                <p class="mt-0.5 text-sm text-slate-500">Atur kop surat, logo, dan riwayat/titimangsa pembuatan kartu.</p>
            </div>
            <a href="{{ route('kartu-ujian.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
            @endif

            <form action="{{ route('kartu-ujian.settings') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-bold text-slate-800">1. Kop Surat & Judul</h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Logo Instansi</label>
                            @if($setting->logo_kiri)
                                <img src="{{ asset('storage/'.$setting->logo_kiri) }}" class="mb-3 h-16 object-contain" alt="Logo">
                            @endif
                            <input type="file" name="logo_kiri" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Header 1 (Pemerintah/Yayasan)</label>
                                <input type="text" name="header_1" value="{{ old('header_1', $setting->header_1) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Header 2 (Dinas/Lembaga)</label>
                                <input type="text" name="header_2" value="{{ old('header_2', $setting->header_2) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Header 3 (Nama Sekolah)</label>
                                <input type="text" name="header_3" value="{{ old('header_3', $setting->header_3) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Header 4 (Alamat/Kontak)</label>
                                <input type="text" name="header_4" value="{{ old('header_4', $setting->header_4) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Judul Kartu</label>
                                <input type="text" name="judul_kartu" value="{{ old('judul_kartu', $setting->judul_kartu) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-bold text-slate-800">2. History Pembuatan & Tanda Tangan</h3>
                    <p class="mb-4 text-sm text-slate-500">Bagian ini akan tampil di sudut kanan bawah kartu.</p>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Tempat & Tanggal Pembuatan</label>
                                <input type="text" name="tempat_tanggal" value="{{ old('tempat_tanggal', $setting->tempat_tanggal) }}" placeholder="Contoh: Cibinong, 24 September 2026" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Jabatan Penandatangan</label>
                                <input type="text" name="jabatan_penandatangan" value="{{ old('jabatan_penandatangan', $setting->jabatan_penandatangan) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Nama Penandatangan</label>
                                <input type="text" name="nama_penandatangan" value="{{ old('nama_penandatangan', $setting->nama_penandatangan) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">NIP / NIK</label>
                                <input type="text" name="nip_penandatangan" value="{{ old('nip_penandatangan', $setting->nip_penandatangan) }}" class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar Tanda Tangan / Stempel</label>
                            @if($setting->ttd_image)
                                <img src="{{ asset('storage/'.$setting->ttd_image) }}" class="mb-3 h-20 object-contain" alt="TTD">
                            @endif
                            <input type="file" name="ttd_image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                            <p class="mt-2 text-xs text-slate-400">Gunakan gambar transparan (PNG) untuk hasil terbaik.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-sm hover:bg-blue-700">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
