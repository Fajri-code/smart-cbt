<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $guru = $request->user()->guru;
        abort_unless($guru, 403);

        $baseQuery = Exam::where('guru_id', $guru->id);
        $now = now();

        $exams = (clone $baseQuery)
            ->with(['mataPelajaran', 'kelasData'])
            ->withCount('questions')
            ->orderByRaw('CASE WHEN tanggal_mulai >= ? THEN 0 ELSE 1 END', [$now])
            ->orderBy('tanggal_mulai')
            ->limit(8)
            ->get();

        return view('guru.dashboard', [
            'exams' => $exams,
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'mendatang' => (clone $baseQuery)->where('tanggal_mulai', '>', $now)->count(),
                'aktif' => (clone $baseQuery)->where(function ($query) use ($now): void {
                    $query->where('status', 'aktif')
                        ->orWhere(function ($query) use ($now): void {
                            $query->where('tanggal_mulai', '<=', $now)
                                ->where('tanggal_selesai', '>=', $now);
                        });
                })->count(),
                'selesai' => (clone $baseQuery)->where(function ($query) use ($now): void {
                    $query->where('status', 'selesai')
                        ->orWhere('tanggal_selesai', '<', $now);
                })->count(),
            ],
        ]);
    }
}