<?php

use App\Models\Exam;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Exam::query()
        ->where('token_aktif', true)
        ->whereNotNull('token_kedaluwarsa_at')
        ->where('token_kedaluwarsa_at', '<=', now())
        ->get()
        ->each(fn (Exam $exam) => $exam->activateToken());
})->everyMinute();

Artisan::command('exam:restore-attempt {attempt_id} {json_answers}', function ($attemptId, $jsonAnswers) {
    $attempt = \App\Models\ExamAttempt::find($attemptId);
    if (! $attempt) {
        $this->error("ExamAttempt ID {$attemptId} tidak ditemukan!");
        return 1;
    }

    $answers = json_decode($jsonAnswers, true);
    if (! is_array($answers)) {
        $this->error("Format JSON tidak valid!");
        return 1;
    }

    $attempt->load('exam.questions');
    $totalWeight = (float) $attempt->exam->questions->sum(fn ($q) => $q->bobot ?: 1);
    $earned = 0;
    $savedCount = 0;

    foreach ($attempt->exam->questions as $question) {
        $ans = $answers[$question->id] ?? null;
        if ($ans === null) {
            continue;
        }

        $correct = $question->tipe === 'pg' && strtoupper((string) $ans) === strtoupper((string) $question->kunci);
        $score = $correct ? (float) ($question->bobot ?: 1) : 0;
        $earned += $score;

        \App\Models\Answer::updateOrCreate(
            ['exam_attempt_id' => $attempt->id, 'question_id' => $question->id],
            [
                'jawaban' => (string) $ans,
                'is_correct' => $question->tipe === 'pg' ? $correct : null,
                'skor' => $question->tipe === 'pg' ? $score : null,
            ]
        );
        $savedCount++;
    }

    $nilaiAkhir = $totalWeight > 0 ? round($earned / $totalWeight * 100, 2) : 0;
    $attempt->update([
        'status' => 'submitted',
        'submitted_at' => $attempt->submitted_at ?? now(),
        'nilai_akhir' => $nilaiAkhir,
    ]);

    $this->info("BERHASIL! {$savedCount} jawaban berhasil dipulihkan untuk Attempt ID {$attemptId}.");
    $this->info("Nilai akhir: {$nilaiAkhir}");
    return 0;
})->purpose('Pulihkan jawaban siswa dari backup JSON localStorage');

Artisan::command('exam:force-close', function () {
    $this->info("Mencari ujian yang statusnya masih 'in_progress' tapi sudah lewat waktu deadline...");
    $count = 0;

    \App\Models\ExamAttempt::with('exam.questions')
        ->where('status', 'in_progress')
        ->chunk(50, function ($attempts) use (&$count) {
            foreach ($attempts as $attempt) {
                // Hitung deadline
                $deadline = $attempt->started_at->copy()->addMinutes($attempt->exam->durasi_menit);
                if ($attempt->exam->tanggal_selesai && $attempt->exam->tanggal_selesai->lt($deadline)) {
                    $deadline = $attempt->exam->tanggal_selesai;
                }

                // Berikan toleransi keterlambatan 5 menit
                if (now()->gt($deadline->addMinutes(5))) {
                    $this->info("Menutup paksa Attempt ID: {$attempt->id} (Siswa ID: {$attempt->siswa_id})");

                    // Hitung nilai akhir dari jawaban yang sudah tersimpan di database
                    $totalWeight = (float) $attempt->exam->questions->sum(fn ($q) => $q->bobot ?: 1);
                    $earned = 0;
                    
                    $savedAnswers = $attempt->answers->keyBy('question_id');

                    foreach ($attempt->exam->questions as $question) {
                        $ans = $savedAnswers->get($question->id);
                        if ($ans) {
                            $earned += (float) $ans->skor;
                        }
                    }

                    $nilaiAkhir = $totalWeight > 0 ? round($earned / $totalWeight * 100, 2) : 0;
                    
                    $attempt->update([
                        'status' => 'expired', // Pakai expired agar tahu ini ditutup paksa oleh sistem
                        'submitted_at' => $deadline,
                        'nilai_akhir' => $nilaiAkhir,
                    ]);
                    $count++;
                }
            }
        });

    $this->info("Selesai! {$count} ujian yang nyangkut berhasil ditutup paksa dan dihitung nilainya.");
})->purpose('Menutup paksa dan menghitung nilai ujian yang lewat waktu deadline (karena browser siswa ditutup)');

Schedule::command('exam:force-close')->everyFiveMinutes();
