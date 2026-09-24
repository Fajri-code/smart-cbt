            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4 flex justify-between items-center flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-900">Rekap Jawaban: {{ ->exam->nama ?? '-' }}</h3>
                        <p class="mt-1 text-sm text-slate-500">Mata Pelajaran: {{ ->exam->mataPelajaran?->nama ?? '-' }} &bull; Nilai Akhir: <strong class="text-slate-800">{{ ->nilai_akhir !== null ? number_format(->nilai_akhir, 2) : '-' }}</strong></p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 w-16 text-center">No</th>
                                <th class="px-5 py-3">Tipe Soal</th>
                                <th class="px-5 py-3 text-center">Jawaban Siswa</th>
                                <th class="px-5 py-3 text-center">Kunci</th>
                                <th class="px-5 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ( as  => )
                                @php
                                     = ->is_correct;
                                     = !empty(->jawaban);
                                    
                                    if (!) {
                                         = 'Tidak Dijawab';
                                         = 'bg-slate-100 text-slate-600';
                                    } elseif (->question->tipe === 'pilihan_ganda' || ->question->tipe === 'pg') {
                                         =  ? 'Benar' : 'Salah';
                                         =  ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800';
                                    } else {
                                         = ->sudah_dinilai ? 'Sudah Dinilai' : 'Belum Dinilai';
                                         = ->sudah_dinilai ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4 text-center font-medium text-slate-900">{{  + 1 }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ ->question->tipe === 'pilihan_ganda' || ->question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}</td>
                                    <td class="px-5 py-4 text-center font-bold text-slate-900">{{ ->jawaban ?: '-' }}</td>
                                    <td class="px-5 py-4 text-center text-slate-600">{{ ->question->tipe === 'pilihan_ganda' || ->question->tipe === 'pg' ? (->question->kunci ?: '-') : '(Essay)' }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{  }}">
                                            {{  }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-500">Belum ada jawaban yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
