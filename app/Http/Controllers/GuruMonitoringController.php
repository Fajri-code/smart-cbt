<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\Exam;
use App\Models\Siswa;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $guruId = $request->user()->guru?->id;
        abort_unless($guruId, 403);

        $status = $request->string('status')->toString();
        $allowedStatuses = ['online', 'in_progress', 'completed', 'not_started'];
        $status = in_array($status, $allowedStatuses, true) ? $status : 'online';
        $ownedExamIds = Exam::where('guru_id', $guruId)->pluck('id');
        $base = ExamAttempt::whereIn('exam_id', $ownedExamIds);

        $counts = [
            'online' => (clone $base)->where('status', 'in_progress')->where('updated_at', '>=', now()->subMinutes(5))->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'completed' => (clone $base)->whereIn('status', ['submitted', 'completed'])->count(),
            'not_started' => $this->notStartedQuery($ownedExamIds)->count(),
        ];

        if ($status === 'not_started') {
            $rows = $this->notStartedQuery($ownedExamIds)->get();
        } else {
            $attemptsQuery = ExamAttempt::with([
                'exam' => fn ($query) => $query->with('kelasData')->withCount('questions'),
                'siswa',
            ])->withCount('answers')
                ->whereIn('exam_id', $ownedExamIds)
                ->when($status === 'online', fn ($query) => $query->where('status', 'in_progress')->where('updated_at', '>=', now()->subMinutes(5)))
                ->when($status === 'in_progress', fn ($query) => $query->where('status', 'in_progress'))
                ->when($status === 'completed', fn ($query) => $query->whereIn('status', ['submitted', 'completed']))
                ->latest();
            $rows = $attemptsQuery->get();
        }

        $attempts = new LengthAwarePaginator(
            $rows->forPage($request->integer('page', 1), 20)->values(),
            $rows->count(),
            20,
            $request->integer('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('guru.monitoring.index', [
            'attempts' => $attempts,
            'status' => $status,
            'counts' => $counts,
        ]);
    }

    private function notStartedQuery($examIds)
    {
        return Siswa::query()
            ->select('siswas.*', 'exams.id as exam_id', 'exams.nama as exam_nama', 'exams.kelas as exam_kelas', 'exams.tanggal_mulai', 'exams.tanggal_selesai')
            ->join('exams', function ($join) {
                $join->on(function ($query) {
                    $query->on('exams.kelas_id', '=', 'siswas.kelas_id')
                        ->orWhere(function ($legacy) {
                            $legacy->whereNull('exams.kelas_id')->whereColumn('exams.kelas', 'siswas.kelas');
                        });
                });
            })
            ->whereIn('exams.id', $examIds)
            ->where('siswas.status_aktif', true)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')->from('exam_attempts')
                    ->whereColumn('exam_attempts.exam_id', 'exams.id')
                    ->whereColumn('exam_attempts.siswa_id', 'siswas.id');
            })
            ->latest('exams.tanggal_mulai');
    }
}