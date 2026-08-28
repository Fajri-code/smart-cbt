<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isGuru(), 403);

        $query = ExamAttempt::with(['exam.mataPelajaran', 'siswa']);
        if ($request->user()->isGuru()) {
            $guruId = $request->user()->guru?->id;
            abort_unless($guruId, 403);
            $query->whereHas('exam', fn ($exam) => $exam->where('guru_id', $guruId));
        }

        return view('hasil.index', ['attempts' => $query->latest()->paginate(20)]);
    }
}