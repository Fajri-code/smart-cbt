<?php

namespace App\Http\Controllers;

use App\Models\BankQuestion;
use App\Models\QuestionBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $guruId = $request->user()->guru?->id;
        abort_unless($guruId, 403);

        // Sinkronkan soal ujian ke bank soal
        $this->syncExamQuestionsIntoBanks($guruId);

        $query = QuestionBank::with([
                'mataPelajaran',
                'sourceExam.kelasData'
            ])
            ->withCount('questions')
            ->where('guru_id', $guruId);

        if ($request->filled('q')) {
            $query->where(
                'nama',
                'like',
                '%' . $request->string('q') . '%'
            );
        }

        if ($request->filled('mata_pelajaran_id')) {
            $query->where(
                'mata_pelajaran_id',
                $request->integer('mata_pelajaran_id')
            );
        }

        if ($request->filled('tipe')) {
            $query->whereHas(
                'questions',
                fn ($questions) =>
                    $questions->where(
                        'tipe',
                        $request->string('tipe')
                    )
            );
        }

        $banks = $query->latest()->get();

        return view('guru.bank-soal.index', [
            'banks' => $banks,
            'subjects' => \App\Models\MataPelajaran::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $guruId = $request->user()->guru?->id;
        abort_unless($guruId, 403);

        $data = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255'
            ],

            'mata_pelajaran_id' => [
                'required',
                'exists:mata_pelajarans,id'
            ],
        ]);

        QuestionBank::create([
            ...$data,
            'guru_id' => $guruId,
        ]);

        return back()->with(
            'success',
            'Bank soal berhasil dibuat.'
        );
    }

    public function show(
        QuestionBank $bankSoal,
        Request $request
    ): View {
        $this->owned($bankSoal, $request);

        return view('guru.bank-soal.show', [
            'bank' => $bankSoal->load([
                'mataPelajaran',
                'sourceExam.kelasData'
            ]),

            'questions' => $bankSoal
                ->questions()
                ->latest()
                ->paginate(15),
        ]);
    }

    public function storeQuestion(
        QuestionBank $bankSoal,
        Request $request
    ): RedirectResponse {
        $this->owned($bankSoal, $request);

        $this->guardEditableBank($bankSoal);

        $data = $request->validate([
            'tipe' => [
                'required',
                'in:pg,essay_1,essay_2'
            ],

            'pertanyaan' => [
                'required',
                'string'
            ],

            'petunjuk_jawaban' => [
                'nullable',
                'string'
            ],

            'opsi_a' => [
                'nullable',
                'string'
            ],

            'opsi_b' => [
                'nullable',
                'string'
            ],

            'opsi_c' => [
                'nullable',
                'string'
            ],

            'opsi_d' => [
                'nullable',
                'string'
            ],

            'opsi_e' => [
                'nullable',
                'string'
            ],

            'kunci' => [
                'nullable',
                'in:A,B,C,D,E'
            ],

            'bobot' => [
                'required',
                'numeric',
                'min:0'
            ],
        ]);

        BankQuestion::create([
            ...$data,
            'question_bank_id' => $bankSoal->id,
        ]);

        return back()->with(
            'success',
            'Soal disimpan ke bank.'
        );
    }

    public function destroyQuestion(
        QuestionBank $bankSoal,
        BankQuestion $bankQuestion,
        Request $request
    ): RedirectResponse {
        $this->owned($bankSoal, $request);

        $this->guardEditableBank($bankSoal);

        abort_unless(
            $bankQuestion->question_bank_id === $bankSoal->id,
            404
        );

        $bankQuestion->delete();

        return back()->with(
            'success',
            'Soal bank dihapus.'
        );
    }

    /**
     * Memastikan bank soal hanya bisa diakses
     * oleh guru pemiliknya.
     */
    private function owned(
        QuestionBank $bank,
        Request $request
    ): void {
        abort_unless(
            $bank->guru_id === $request->user()->guru?->id,
            403
        );
    }

    /**
     * Bank yang berasal dari ujian tidak boleh
     * diedit manual.
     */
    private function guardEditableBank(
        QuestionBank $bank
    ): void {
        if ($bank->source_exam_id) {
            abort(
                403,
                'Soal ini sudah dipakai untuk ujian tertentu dan tidak bisa diubah lagi. Gunakan bank soal stok untuk menyiapkan soal baru.'
            );
        }
    }

    /**
     * Sinkronkan soal dari ujian ke Bank Soal.
     *
     * Nama bank dibuat:
     *
     * UAS • Matematika • Kelas 8
     *
     * atau:
     *
     * UTS • Matematika • Taimiyah
     */
    private function syncExamQuestionsIntoBanks(
        int $guruId
    ): void {
        $exams = \App\Models\Exam::with([
                'questions',
                'kelasData',
                'mataPelajaran'
            ])
            ->where('guru_id', $guruId)
            ->get();

        foreach ($exams as $exam) {

            // Kalau belum ada soal, tidak perlu
            // dibuatkan bank.
            if ($exam->questions->isEmpty()) {
                continue;
            }

            /*
             * Ambil nama mata pelajaran.
             *
             * Contoh:
             * Matematika
             * Bahasa Indonesia
             * Prakarya
             */
            $mataPelajaran = $exam->mataPelajaran?->nama
                ?? 'Mata Pelajaran';

            /*
             * Ambil nama kelas.
             *
             * Kalau menggunakan data kelas:
             * Kelas 8
             * Taimiyah
             * VIII A
             *
             * Maka otomatis mengikuti database.
             */
            $kelasLabel = $exam->kelasData?->nama_kelas
                ?? $exam->kelas
                ?? 'Kelas tertentu';

            /*
             * Nama bank soal yang akan ditampilkan.
             *
             * Contoh:
             *
             * UAS • Matematika • Kelas 8
             * UTS • Bahasa Indonesia • Taimiyah
             * UAS • Prakarya • Kelas 8
             */
            $namaBank = $exam->nama
                . ' • '
                . $mataPelajaran
                . ' • '
                . $kelasLabel;

            /*
             * Cari bank berdasarkan ujian sumber.
             *
             * Ini penting supaya ketika nama bank berubah,
             * tidak dibuat bank baru terus-menerus.
             */
            $bank = QuestionBank::where('guru_id', $guruId)
                ->where('source_exam_id', $exam->id)
                ->first();

            /*
             * Kalau belum ada bank dari ujian tersebut,
             * cek kemungkinan bank lama yang dibuat oleh
             * sistem versi sebelumnya.
             */
            if (!$bank) {
                $bank = QuestionBank::where('guru_id', $guruId)
                    ->where('mata_pelajaran_id', $exam->mata_pelajaran_id)
                    ->where('nama', $exam->nama)
                    ->first();
            }

            /*
             * Kalau belum ada juga, buat baru.
             */
            if (!$bank) {
                $bank = QuestionBank::create([
                    'guru_id' => $guruId,
                    'mata_pelajaran_id' => $exam->mata_pelajaran_id,
                    'nama' => $namaBank,
                    'source_exam_id' => $exam->id,
                    'keterangan' =>
                        'Sudah dipakai untuk ujian '
                        . $exam->nama
                        . ' pada '
                        . $mataPelajaran
                        . ' kelas '
                        . $kelasLabel,
                ]);
            } else {

                /*
                 * Update nama bank lama supaya bank yang
                 * sebelumnya hanya bernama "UAS" menjadi:
                 *
                 * UAS • Matematika • Kelas 8
                 */
                $bank->update([
                    'nama' => $namaBank,
                    'mata_pelajaran_id' => $exam->mata_pelajaran_id,
                    'source_exam_id' => $exam->id,
                    'keterangan' =>
                        'Sudah dipakai untuk ujian '
                        . $exam->nama
                        . ' pada '
                        . $mataPelajaran
                        . ' kelas '
                        . $kelasLabel,
                ]);
            }

            /*
             * Masukkan setiap soal ujian ke bank.
             */
            foreach ($exam->questions as $question) {

                $bank->questions()->firstOrCreate(
                    [
                        'question_bank_id' => $bank->id,
                        'pertanyaan' => $question->pertanyaan,
                    ],
                    [
                        'tipe' => $question->tipe,
                        'petunjuk_jawaban' =>
                            $question->petunjuk_jawaban,

                        'opsi_a' => $question->opsi_a,
                        'opsi_b' => $question->opsi_b,
                        'opsi_c' => $question->opsi_c,
                        'opsi_d' => $question->opsi_d,
                        'opsi_e' => $question->opsi_e,

                        'kunci' => $question->kunci,
                        'bobot' => $question->bobot,
                    ]
                );
            }
        }
    }
}