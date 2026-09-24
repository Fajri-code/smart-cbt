    /**
     * Export 4: Rekap Jawaban per Soal untuk satu Attempt
     */
    public function exportRekapJawaban(Request $request, $id)
    {
        $attempt = ExamAttempt::with([
            'exam.mataPelajaran', 
            'siswa', 
            'answers.question'
        ])->findOrFail($id);

        if ($request->user()->isGuru()) {
            abort_unless($attempt->exam->guru_id === $request->user()->guru?->id, 403, 'Tidak ada akses.');
        }

        $answers = $attempt->answers->sortBy(function($answer) {
            return $answer->question->urutan ?? 9999;
        });

        $data = [];
        $no = 1;
        foreach ($answers as $ans) {
            $isCorrect = $ans->is_correct;
            $hasAnswered = !empty($ans->jawaban);
            
            if (!$hasAnswered) {
                $statusText = 'Tidak Dijawab';
            } elseif ($ans->question->tipe === 'pilihan_ganda' || $ans->question->tipe === 'pg') {
                $statusText = $isCorrect ? 'Benar' : 'Salah';
            } else {
                $statusText = $ans->sudah_dinilai ? 'Sudah Dinilai' : 'Belum Dinilai';
            }

            $data[] = [
                'No' => $no++,
                'Tipe Soal' => $ans->question->tipe === 'pilihan_ganda' || $ans->question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay',
                'Jawaban Siswa' => $ans->jawaban ?: '-',
                'Kunci' => $ans->question->tipe === 'pilihan_ganda' || $ans->question->tipe === 'pg' ? ($ans->question->kunci ?: '-') : '(Essay)',
                'Status' => $statusText,
                'Skor' => $ans->skor ?? 0,
            ];
        }

        $filename = 'Rekap_Jawaban_' . \Illuminate\Support\Str::slug($attempt->siswa->nama) . '_' . \Illuminate\Support\Str::slug($attempt->exam->nama) . '_' . date('YmdHis') . '.xlsx';

        return (new FastExcel($data))->download($filename);
    }
}
