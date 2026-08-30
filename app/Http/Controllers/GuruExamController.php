<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruExamController extends Controller
{
   public function show(Exam $ujian, Request $request): View
{
    // Pastikan ujian milik guru yang sedang login
    abort_unless(
        $ujian->guru_id === $request->user()->guru?->id,
        403
    );

    // Load relasi yang dibutuhkan
    $ujian->load([
        'mataPelajaran',
        'kelasData',
        'questions',
    ]);

    // Ambil semua soal untuk ujian ini
    $questions = $ujian->questions()
        ->orderBy('urutan')
        ->paginate(15);

    // Hitung jumlah soal PG
    $pgCount = $ujian->questions()
        ->where('tipe', 'pg')
        ->count();

    // Hitung jumlah soal Essay
    $essayCount = $ujian->questions()
        ->whereIn('tipe', ['essay_1', 'essay_2'])
        ->count();

    // Total seluruh soal
    $totalQuestions = $ujian->questions()->count();

    // Total bobot semua soal
    $totalBobot = $ujian->questions()->sum('bobot');

    // Pertanyaan yang sudah masuk ke ujian
    $importedQuestions = $ujian->questions()
        ->pluck('pertanyaan')
        ->toArray();

    // Ambil bank soal milik guru
    $banks = $request->user()
        ->guru
        ->questionBanks()
        ->with('questions')
        ->get();

    return view('ujian.show', [
        'exam' => $ujian,
        'questions' => $questions,
        'banks' => $banks,
        'importedQuestions' => $importedQuestions,

        'pgCount' => $pgCount,
        'essayCount' => $essayCount,
        'totalQuestions' => $totalQuestions,
        'totalBobot' => $totalBobot,

        'totalSchedules' => 0,
        'totalParticipants' => $ujian->examAttempts()->count(),
        'isGuruView' => true,
    ]);
}

    public function index(Request $request): View
    {
        $guru = $request->user()->guru;
        abort_unless($guru, 403);

        $query = Exam::with(['mataPelajaran', 'kelasData'])
            ->withCount('questions')
            ->where('guru_id', $guru->id);

        $totalExams = (clone $query)->count();
        $activeExams = (clone $query)->where('status', 'aktif')->where('token_aktif', true)->count();
        $draftExams = (clone $query)->where(function ($q) {
            $q->where('status', '!=', 'aktif')->orWhere('token_aktif', false);
        })->count();

        $exams = $query->latest()->paginate(10);

        return view('guru.ujian.index', compact('exams', 'totalExams', 'activeExams', 'draftExams'));
    }
}