<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SiswaExamController extends Controller
{
    public function dashboard(Request $request): View
    {
        $siswa = $this->siswa($request);
        $exams = $this->availableExams($siswa)->get();

        return view('siswa.dashboard', $this->grouped($exams, $siswa));
    }

    public function index(Request $request): View
    {
        $siswa = $this->siswa($request);
        $exams = $this->availableExams($siswa)->get();

        return view('siswa.exams.index', compact('exams', 'siswa'));
    }

    public function show(Request $request, Exam $ujian): View
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $attempt = $this->attempt($ujian, $siswa);

        if ($attempt?->status === 'in_progress' && $this->deadline($attempt) <= now()) {
            $this->submitAttempt($attempt, []);
            $attempt->refresh();
        }

        return view('siswa.exams.show', [
            'exam' => $ujian->load(['mataPelajaran', 'guru', 'kelasData'])->loadCount('questions'),
            'attempt' => $attempt,
            'status' => $this->status($ujian, $attempt),
        ]);
    }

    public function token(Request $request, Exam $ujian): View|RedirectResponse
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $ujian->rotateExpiredToken();
        $attempt = $this->attempt($ujian, $siswa);

        abort_unless($ujian->token_aktif && $ujian->token && $ujian->questions()->exists(), 404);

        if ($attempt?->status !== null && $attempt->status !== 'in_progress') {
            return to_route('siswa.ujian.result', $ujian);
        }

        return view('siswa.exams.token', ['exam' => $ujian]);
    }

    public function start(Request $request, Exam $ujian): RedirectResponse
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $ujian->rotateExpiredToken();
        $now = now();

        if ($ujian->status !== 'aktif' || ! $ujian->token_aktif || ! $ujian->token || ! $ujian->questions()->exists() || ! $ujian->tanggal_mulai || $now->lt($ujian->tanggal_mulai)) {
            return back()->with('error', 'Ujian belum dapat dimulai.');
        }

        if ($ujian->tanggal_selesai && $now->gte($ujian->tanggal_selesai)) {
            return back()->with('error', 'Waktu ujian sudah berakhir.');
        }

        $data = $request->validate(['token' => ['required', 'string', 'max:20']]);
        $token = strtoupper(trim($data['token']));
        if (! $ujian->token_aktif || ! $ujian->token || ! hash_equals(strtoupper((string) $ujian->token), $token)) {
            return back()->withErrors(['token' => 'Token ujian tidak valid.'])->withInput();
        }

        $attempt = ExamAttempt::firstOrCreate(
            ['exam_id' => $ujian->id, 'siswa_id' => $siswa->id],
            ['started_at' => $now, 'status' => 'in_progress']
        );

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Ujian ini sudah selesai dikerjakan.');
        }

        $request->session()->put($this->tokenSessionKey($ujian), $attempt->id);

        return to_route('siswa.ujian.work', $ujian);
    }

    public function work(Request $request, Exam $ujian): View|RedirectResponse
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $ujian->rotateExpiredToken();
        abort_unless($ujian->token_aktif && $ujian->token && $ujian->questions()->exists(), 404);
        $attempt = $this->attempt($ujian, $siswa);
        abort_unless($attempt?->status === 'in_progress', 403);
        abort_unless($request->session()->get($this->tokenSessionKey($ujian)) === $attempt->id, 403, 'Token ujian tidak valid.');

        if ($this->deadline($attempt) <= now()) {
            $this->submitAttempt($attempt, []);
            return to_route('siswa.ujian.result', $ujian)->with('success', 'Waktu habis. Jawaban telah dikumpulkan otomatis.');
        }

        $ujian->load(['mataPelajaran', 'questions']);
        $answers = $attempt->answers()->pluck('jawaban', 'question_id');
        $exam = $ujian;

        return view('siswa.exams.work', compact('exam', 'attempt', 'answers'));
    }

    public function submit(Request $request, Exam $ujian): RedirectResponse
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $attempt = $this->attempt($ujian, $siswa);
        abort_unless($attempt?->status === 'in_progress', 403);
        abort_unless($request->session()->get($this->tokenSessionKey($ujian)) === $attempt->id, 403, 'Token ujian tidak valid.');

        $answers = $request->input('answers', []);
        $this->submitAttempt($attempt, is_array($answers) ? $answers : []);

        return to_route('siswa.ujian.result', $ujian)->with('success', 'Ujian berhasil dikumpulkan.');
    }

    public function saveAnswers(Request $request, Exam $ujian): \Illuminate\Http\JsonResponse
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $attempt = $this->attempt($ujian, $siswa);
        abort_unless($attempt?->status === 'in_progress', 403);
        abort_unless($request->session()->get($this->tokenSessionKey($ujian)) === $attempt->id, 403, 'Token ujian tidak valid.');

        $answers = $request->input('answers', []);
        abort_unless(is_array($answers), 422);
        $questionIds = $ujian->questions()->pluck('id')->all();

        DB::transaction(function () use ($answers, $attempt, $questionIds): void {
            foreach ($answers as $questionId => $answer) {
                if (! in_array((int) $questionId, $questionIds, true) || ! is_scalar($answer)) {
                    continue;
                }

                Answer::updateOrCreate(
                    ['exam_attempt_id' => $attempt->id, 'question_id' => (int) $questionId],
                    ['jawaban' => (string) $answer]
                );
            }
        });

        return response()->json(['saved' => true]);
    }

    public function leave(Request $request, Exam $ujian): \Illuminate\Http\JsonResponse
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $attempt = $this->attempt($ujian, $siswa);

        if ($attempt?->status === 'in_progress' && $request->session()->get($this->tokenSessionKey($ujian)) === $attempt->id) {
            $request->session()->forget($this->tokenSessionKey($ujian));
        }

        return response()->json(['left' => true]);
    }

    public function result(Request $request, Exam $ujian): View
    {
        $siswa = $this->siswa($request);
        $this->ensureAssigned($ujian, $siswa);
        $attempt = $this->attempt($ujian, $siswa);
        abort_unless($attempt && $attempt->status !== 'in_progress', 404);

        return view('siswa.exams.result', ['exam' => $ujian->load(['mataPelajaran', 'guru']), 'attempt' => $attempt]);
    }

    private function siswa(Request $request): Siswa
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa && $siswa->status_aktif, 403);

        return $siswa;
    }

    private function availableExams(Siswa $siswa)
    {
        if (! $siswa->kelas_id) {
            return Exam::query()->whereRaw('1 = 0');
        }

        return Exam::with(['mataPelajaran', 'guru', 'kelasData'])
            ->withCount('questions')
            ->whereNotNull('kelas_id')
            ->where('kelas_id', $siswa->kelas_id)
            ->where('status', '!=', 'draft')
            ->where(function ($query) use ($siswa): void {
                $query->where(function ($readyQuery): void {
                    $readyQuery->where('token_aktif', true)
                        ->whereNotNull('token')
                        ->whereHas('questions');
                })->orWhereHas('examAttempts', fn ($attemptQuery) => $attemptQuery->where('siswa_id', $siswa->id));
            })
            ->with(['examAttempts' => fn ($query) => $query->where('siswa_id', $siswa->id)])
            ->orderBy('tanggal_mulai');
    }

    private function grouped($exams, Siswa $siswa): array
    {
        $today = now()->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $todayExams = $exams->filter(fn (Exam $exam) => $exam->tanggal_mulai?->betweenIncluded($today, $tomorrow->copy()->subSecond()));

        return [
            'siswa' => $siswa->load('kelasData'),
            'todayExams' => $todayExams,
            'upcomingExams' => $exams->filter(fn (Exam $exam) => $exam->tanggal_mulai?->gte($tomorrow)),
            'completedExams' => $exams
                ->filter(fn (Exam $exam) => $exam->examAttempts->first()?->status !== null && $exam->examAttempts->first()?->status !== 'in_progress')
                ->sortByDesc(fn (Exam $exam) => $exam->examAttempts->first()?->submitted_at)
                ->values(),
        ];
    }

    private function ensureAssigned(Exam $exam, Siswa $siswa): void
    {
        abort_unless(
            $siswa->kelas_id && $exam->kelas_id && (int) $exam->kelas_id === (int) $siswa->kelas_id,
            403,
            'Ujian ini tidak ditujukan untuk kelas Anda.'
        );
    }

    private function attempt(Exam $exam, Siswa $siswa): ?ExamAttempt
    {
        return $exam->examAttempts()->where('siswa_id', $siswa->id)->first();
    }

    private function tokenSessionKey(Exam $exam): string
    {
        return 'siswa.exam_token_verified.' . $exam->id;
    }

    private function status(Exam $exam, ?ExamAttempt $attempt): string
    {
        if ($attempt && $attempt->status !== 'in_progress') return 'Sudah Dikerjakan';
        if ($exam->tanggal_mulai?->gt(now())) return 'Belum Dimulai';
        if ($exam->tanggal_selesai && $exam->tanggal_selesai->lte(now())) return 'Sudah Selesai';
        return 'Sedang Berlangsung';
    }

    private function deadline(ExamAttempt $attempt): Carbon
    {
        $deadline = $attempt->started_at->copy()->addMinutes($attempt->exam->durasi_menit);
        if ($attempt->exam->tanggal_selesai && $attempt->exam->tanggal_selesai->lt($deadline)) $deadline = $attempt->exam->tanggal_selesai;

        return $deadline;
    }

    private function submitAttempt(ExamAttempt $attempt, array $submittedAnswers): void
    {
        DB::transaction(function () use ($attempt, $submittedAnswers): void {
            $attempt->load('exam.questions');
            $submittedAnswers = array_replace($attempt->answers()->pluck('jawaban', 'question_id')->all(), $submittedAnswers);
            $totalWeight = (float) $attempt->exam->questions->sum(fn ($question) => $question->bobot ?: 1);
            $earned = 0;

            foreach ($attempt->exam->questions as $question) {
                $answer = $submittedAnswers[$question->id] ?? null;
                $correct = $question->tipe === 'pg' && strtoupper((string) $answer) === strtoupper((string) $question->kunci);
                $score = $correct ? (float) ($question->bobot ?: 1) : 0;
                $earned += $score;
                Answer::updateOrCreate(
                    ['exam_attempt_id' => $attempt->id, 'question_id' => $question->id],
                    ['jawaban' => is_scalar($answer) ? (string) $answer : null, 'is_correct' => $question->tipe === 'pg' ? $correct : null, 'skor' => $question->tipe === 'pg' ? $score : null]
                );
            }

            $attempt->update(['status' => now()->gte($this->deadline($attempt)) ? 'expired' : 'submitted', 'submitted_at' => now(), 'nilai_akhir' => $totalWeight > 0 ? round($earned / $totalWeight * 100, 2) : 0]);
        });
    }
}