<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruExamController extends Controller
{
    public function show(Exam $ujian, Request $request): View
    {
        abort_unless($ujian->guru_id === $request->user()->guru?->id, 403);
        $ujian->load(['mataPelajaran', 'kelasData']);
        return view('ujian.show', [
            'exam' => $ujian,
            'pgCount' => $ujian->questions()->where('tipe', 'pg')->count(),
            'essayCount' => $ujian->questions()->whereIn('tipe', ['essay_1', 'essay_2'])->count(),
            'totalQuestions' => $ujian->questions()->count(),
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