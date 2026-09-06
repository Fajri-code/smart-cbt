<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class ExportController extends Controller
{
    /**
     * Export Soal Ujian
     */
    public function exportSoalUjian(Request $request, Exam $ujian)
    {
        // Otorisasi: Admin boleh semua. Guru hanya boleh ujian miliknya.
        if ($request->user()->isGuru()) {
            abort_unless($ujian->guru_id === $request->user()->guru?->id, 403, 'Anda tidak memiliki akses ke ujian ini.');
        }

        $questions = $ujian->questions()->orderBy('urutan')->get();
        $fileName = 'soal_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $ujian->nama) . '.xlsx';

        return (new FastExcel($questions))->download($fileName, function ($question) {
            return [
                'No' => $question->urutan,
                'Tipe Soal' => $question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay',
                'Pertanyaan' => strip_tags($question->pertanyaan),
                'Pilihan A' => strip_tags($question->opsi_a),
                'Pilihan B' => strip_tags($question->opsi_b),
                'Pilihan C' => strip_tags($question->opsi_c),
                'Pilihan D' => strip_tags($question->opsi_d),
                'Jawaban Benar' => strtoupper($question->kunci),
                'Bobot' => $question->bobot,
            ];
        });
    }

    /**
     * Export Soal Bank Soal
     */
    public function exportBankSoal(Request $request, QuestionBank $bankSoal)
    {
        // Otorisasi: Admin boleh semua. Guru hanya boleh bank miliknya.
        if ($request->user()->isGuru()) {
            abort_unless($bankSoal->guru_id === $request->user()->guru?->id, 403, 'Anda tidak memiliki akses ke bank soal ini.');
        }

        $questions = $bankSoal->questions()->get();
        $fileName = 'bank_soal_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $bankSoal->nama) . '.xlsx';

        $no = 1;
        return (new FastExcel($questions))->download($fileName, function ($question) use (&$no) {
            return [
                'No' => $no++,
                'Tipe Soal' => $question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay',
                'Pertanyaan' => strip_tags($question->pertanyaan),
                'Pilihan A' => strip_tags($question->opsi_a),
                'Pilihan B' => strip_tags($question->opsi_b),
                'Pilihan C' => strip_tags($question->opsi_c),
                'Pilihan D' => strip_tags($question->opsi_d),
                'Jawaban Benar' => strtoupper($question->kunci),
                'Bobot' => $question->bobot,
            ];
        });
    }

    /**
     * Helper Query untuk export hasil
     */
    private function getFilteredQuery(Request $request)
    {
        $query = ExamAttempt::with(['exam.mataPelajaran', 'exam.kelasData', 'siswa.kelasData', 'siswa.user']);

        // Otorisasi Guru
        if ($request->user()->isGuru()) {
            $guruId = $request->user()->guru?->id;
            $query->whereHas('exam', fn ($q) => $q->where('guru_id', $guruId));
        }

        // Apply filters
        $query->when($request->filled('exam_id'), fn ($q) => $q->where('exam_id', $request->integer('exam_id')))
            ->when($request->filled('kelas_id'), fn ($q) => $q->whereHas('exam', fn ($exam) => $exam->where('kelas_id', $request->integer('kelas_id'))))
            ->when($request->filled('mata_pelajaran_id'), fn ($q) => $q->whereHas('exam', fn ($exam) => $exam->where('mata_pelajaran_id', $request->integer('mata_pelajaran_id'))))
            ->when($request->filled('tahun_ajaran'), fn ($q) => $q->whereHas('exam', fn ($exam) => $exam->where('tahun_ajaran', (string) $request->string('tahun_ajaran'))))
            ->when($request->filled('semester'), fn ($q) => $q->whereHas('exam', fn ($exam) => $exam->where('semester', (string) $request->string('semester'))))
            ->when($request->filled('status'), fn ($q) => $q->where('status', (string) $request->string('status')))
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('started_at', '>=', $request->date('start_date')))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('started_at', '<=', $request->date('end_date')));

        return $query;
    }

    private function getExportCallback()
    {
        $no = 1;
        return function ($attempt) use (&$no) {
            return [
                'No' => $no++,
                'Nama Siswa' => $attempt->siswa->nama ?? '-',
                'NIS/NISN' => $attempt->siswa->nisn ?: $attempt->siswa->nis,
                'Kelas' => $attempt->siswa->kelasData?->nama_kelas ?? $attempt->siswa->kelas ?? '-',
                'Ujian' => $attempt->exam->nama ?? '-',
                'Mata Pelajaran' => $attempt->exam->mataPelajaran?->nama ?? '-',
                'Nilai' => $attempt->nilai_akhir ?? 0,
                'Status' => $attempt->status === 'submitted' ? 'Selesai' : ($attempt->status === 'in_progress' ? 'Sedang Berlangsung' : 'Waktu Habis'),
                'Waktu Mulai' => $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i:s') : '-',
                'Waktu Selesai' => $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i:s') : '-',
            ];
        };
    }

    /**
     * Export 1: Berdasarkan Kelas (Mewajibkan Ujian dan Kelas)
     */
    public function exportHasilKelas(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $query = $this->getFilteredQuery($request);
        // Pastikan hanya memfilter siswa yang berada di kelas tsb
        $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));

        $attempts = $query->get();
        if ($attempts->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
        }

        $exam = \App\Models\Exam::find($request->exam_id);
        $kelas = \App\Models\Kelas::find($request->kelas_id);

        $fileName = 'hasil_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $exam->nama) . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $kelas->nama_kelas) . '.xlsx';

        return (new FastExcel($attempts))->download($fileName, $this->getExportCallback());
    }

    /**
     * Export 2: Berdasarkan Filter (Sesuai parameter di layar)
     */
    public function exportHasilFilter(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        
        $attempts = $query->get();
        if ($attempts->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang sesuai filter untuk diexport.');
        }

        $fileName = 'hasil_filter_' . now()->format('Y-m-d_His') . '.xlsx';
        
        // Custom nama file yang rapi jika filter spesifik
        if ($request->filled('exam_id') && $request->filled('kelas_id')) {
            $exam = \App\Models\Exam::find($request->exam_id);
            $kelas = \App\Models\Kelas::find($request->kelas_id);
            $fileName = 'hasil_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $exam->nama) . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $kelas->nama_kelas) . '.xlsx';
        } elseif ($request->filled('exam_id') && $request->filled('tahun_ajaran')) {
            $exam = \App\Models\Exam::find($request->exam_id);
            $fileName = 'hasil_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $exam->nama) . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $request->tahun_ajaran) . '.xlsx';
        }

        return (new FastExcel($attempts))->download($fileName, $this->getExportCallback());
    }

    /**
     * Export 3: Berdasarkan Siswa Tertentu
     */
    public function exportHasilSiswa(Request $request, \App\Models\Siswa $siswa)
    {
        $query = ExamAttempt::with(['exam.mataPelajaran', 'exam.kelasData', 'siswa.kelasData'])
            ->where('siswa_id', $siswa->id);

        // Otorisasi Guru
        if ($request->user()->isGuru()) {
            $guruId = $request->user()->guru?->id;
            $query->whereHas('exam', fn ($q) => $q->where('guru_id', $guruId));
        }

        $attempts = $query->orderBy('started_at', 'desc')->get();
        if ($attempts->isEmpty()) {
            return back()->with('error', 'Siswa belum memiliki hasil ujian.');
        }

        $fileName = 'nilai_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $siswa->nama) . '.xlsx';

        $no = 1;
        return (new FastExcel($attempts))->download($fileName, function ($attempt) use (&$no) {
            return [
                'No' => $no++,
                'Ujian' => $attempt->exam->nama ?? '-',
                'Mata Pelajaran' => $attempt->exam->mataPelajaran?->nama ?? '-',
                'Kelas' => $attempt->exam->kelasData?->nama_kelas ?? $attempt->exam->kelas ?? '-',
                'Tahun Ajaran' => $attempt->exam->tahun_ajaran ?? '-',
                'Semester' => $attempt->exam->semester ?? '-',
                'Nilai' => $attempt->nilai_akhir ?? 0,
                'Status' => $attempt->status === 'submitted' ? 'Selesai' : ($attempt->status === 'in_progress' ? 'Sedang Berlangsung' : 'Waktu Habis'),
                'Waktu Mulai' => $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i:s') : '-',
                'Waktu Selesai' => $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i:s') : '-',
            ];
        });
    }
}

