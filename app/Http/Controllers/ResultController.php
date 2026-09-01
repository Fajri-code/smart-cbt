<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\Exam;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isGuru(), 403);

        $query = ExamAttempt::with(['exam.mataPelajaran', 'exam.kelasData', 'siswa.kelasData']);
        if ($request->user()->isGuru()) {
            $guruId = $request->user()->guru?->id;
            abort_unless($guruId, 403);
            $query->whereHas('exam', fn ($exam) => $exam->where('guru_id', $guruId));
        }

        $query->when($request->filled('search_siswa'), function ($query) use ($request) {
            $search = $request->string('search_siswa')->trim();
            $query->whereHas('siswa', fn ($siswa) => $siswa->where('nama', 'like', "%{$search}%"));
        });

        $query->when($request->filled('exam_id'), fn ($query) => $query->where('exam_id', $request->integer('exam_id')))
            ->when($request->filled('kelas_id'), fn ($query) => $query->whereHas('exam', fn ($exam) => $exam->where('kelas_id', $request->integer('kelas_id'))))
            ->when($request->filled('mata_pelajaran_id'), fn ($query) => $query->whereHas('exam', fn ($exam) => $exam->where('mata_pelajaran_id', $request->integer('mata_pelajaran_id'))))
            ->when($request->filled('tahun_ajaran'), fn ($query) => $query->whereHas('exam', fn ($exam) => $exam->where('tahun_ajaran', (string) $request->string('tahun_ajaran'))))
            ->when($request->filled('semester'), fn ($query) => $query->whereHas('exam', fn ($exam) => $exam->where('semester', (string) $request->string('semester'))))
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('started_at', '>=', $request->date('start_date')))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('started_at', '<=', $request->date('end_date')));

        $totalPeserta = (clone $query)->count();
        $totalSelesai = (clone $query)->where('status', 'submitted')->count();
        $rataRata = (clone $query)->whereNotNull('nilai_akhir')->avg('nilai_akhir');
        $nilaiTertinggi = (clone $query)->whereNotNull('nilai_akhir')->max('nilai_akhir');

        $examOptions = Exam::query()
            ->when($request->user()->isGuru(), fn ($exam) => $exam->where('guru_id', $request->user()->guru?->id))
            ->orderBy('nama')
            ->get(['id', 'nama']);
        $kelasOptions = Kelas::query()->orderBy('nama_kelas')->get(['id', 'nama_kelas']);
        $mataPelajaranOptions = MataPelajaran::query()->orderBy('nama')->get(['id', 'nama']);
        $tahunAjaranOptions = Exam::query()
            ->whereNotNull('tahun_ajaran')
            ->where('tahun_ajaran', '!=', '')
            ->when($request->user()->isGuru(), fn ($exam) => $exam->where('guru_id', $request->user()->guru?->id))
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        return view('hasil.index', [
            'attempts' => $query->latest()->paginate(20)->withQueryString(),
            'examOptions' => $examOptions,
            'kelasOptions' => $kelasOptions,
            'mataPelajaranOptions' => $mataPelajaranOptions,
            'tahunAjaranOptions' => $tahunAjaranOptions,
            'totalPeserta' => $totalPeserta,
            'totalSelesai' => $totalSelesai,
            'rataRata' => $rataRata,
            'nilaiTertinggi' => $nilaiTertinggi,
        ]);
    }
}