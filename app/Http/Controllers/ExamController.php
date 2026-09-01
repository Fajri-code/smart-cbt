<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ExamController extends Controller
{
    private const COMPONENTS = [
        'pg',
        'essay_1',
        'essay_2',
    ];

    private const STATUSES = [
        'draft',
        'aktif',
        'selesai',
    ];

    /**
     * =========================================================
     * INDEX
     * =========================================================
     */
    public function index(): View
    {
        // Sinkronisasi status semua ujian yang sudah lewat
        $this->syncExpiredExams();

        $exams = Exam::with([
                'mataPelajaran',
                'guru',
                'guruPengawas',
            ])
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        return view('ujian.index', compact('exams'));
    }

    /**
     * =========================================================
     * CREATE
     * =========================================================
     */
    public function create(): View
    {
        return view('ujian.create', $this->formData());
    }

    /**
     * =========================================================
     * STORE
     * =========================================================
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['tanggal_mulai'] = $this->combineDateTime(
            $data['tanggal_mulai_tanggal'] ?? null,
            $data['tanggal_mulai_jam'] ?? null
        );

        $data['tanggal_selesai'] = $this->combineDateTime(
            $data['tanggal_selesai_tanggal'] ?? null,
            $data['tanggal_selesai_jam'] ?? null
        );

        unset(
            $data['tanggal_mulai_tanggal'],
            $data['tanggal_mulai_jam'],
            $data['tanggal_selesai_tanggal'],
            $data['tanggal_selesai_jam']
        );

        $data['durasi_menit'] = $this->durationInMinutes(
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['durasi_menit']
        );

        $data['komponen_soal'] = ['pg', 'essay_1', 'essay_2'];
        $data['jenis'] = 'pg_essay';

        $data['kelas'] = $data['kelas_id'] ?? '-';

        $data['kode_ujian'] = $this->uniqueCode();

        // Kalau dibuat aktif tetapi waktunya sudah lewat,
        // langsung jadikan selesai.
        if (
            ($data['status'] ?? 'draft') === 'aktif' &&
            !empty($data['tanggal_selesai']) &&
            now()->greaterThanOrEqualTo(
                Carbon::parse($data['tanggal_selesai'])
            )
        ) {
            $data['status'] = 'selesai';
            $data['token_aktif'] = false;
        }

        Exam::create($data);

        return to_route('ujian.index')
            ->with('success', 'Ujian berhasil ditambahkan.');
    }

    /**
     * =========================================================
     * SHOW
     * =========================================================
     */
    public function show(Exam $ujian): View
    {
        // Pastikan status ujian selalu mengikuti jadwal
        $this->syncExamStatus($ujian);

        $ujian->load([
            'mataPelajaran',
            'guru',
            'kelasData',
            'guruPengawas',
        ]);

        /**
         * IMPORTANT:
         *
         * ->reorder() diperlukan karena relationship questions()
         * kemungkinan mempunyai default orderBy('urutan').
         *
         * PostgreSQL tidak mengizinkan:
         *
         * GROUP BY tipe
         * ORDER BY urutan
         *
         * karena urutan bukan bagian GROUP BY.
         */
        $questionCounts = $ujian->questions()
            ->reorder()
            ->selectRaw('tipe, count(*) as total')
            ->groupBy('tipe')
            ->pluck('total', 'tipe');

        return view('ujian.show', [
            'exam' => $ujian,

            'pgCount' => (int) (
                $questionCounts['pg'] ?? 0
            ),

            'essayCount' => (int) (
                $questionCounts
                    ->except('pg')
                    ->sum()
            ),

            'totalQuestions' => (int) (
                $questionCounts->sum()
            ),

            'totalSchedules' => 0,

            'totalParticipants' => $ujian
                ->examAttempts()
                ->count(),
        ]);
    }

    /**
     * =========================================================
     * EDIT
     * =========================================================
     */
    public function edit(Exam $ujian): View
    {
        $this->syncExamStatus($ujian);

        return view(
            'ujian.edit',
            array_merge(
                ['exam' => $ujian],
                $this->formData()
            )
        );
    }

    /**
     * =========================================================
     * UPDATE
     * =========================================================
     */
    public function update(
        Request $request,
        Exam $ujian
    ): RedirectResponse {
        $data = $this->validatedData($request);

        $data['tanggal_mulai'] = $this->combineDateTime(
            $data['tanggal_mulai_tanggal'] ?? null,
            $data['tanggal_mulai_jam'] ?? null
        );

        $data['tanggal_selesai'] = $this->combineDateTime(
            $data['tanggal_selesai_tanggal'] ?? null,
            $data['tanggal_selesai_jam'] ?? null
        );

        unset(
            $data['tanggal_mulai_tanggal'],
            $data['tanggal_mulai_jam'],
            $data['tanggal_selesai_tanggal'],
            $data['tanggal_selesai_jam']
        );

        $data['durasi_menit'] = $this->durationInMinutes(
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['durasi_menit']
        );

        /**
         * Komponen soal ditentukan di area guru saat pengelolaan soal.
         * Admin tidak lagi mengubah jenis komponen soal pada form pembuatan/edisi ujian.
         */
        $data['komponen_soal'] = $ujian->komponen_soal ?: ['pg', 'essay_1', 'essay_2'];
        $data['jenis'] = $this->legacyType($data['komponen_soal']);

        /**
         * Kalau tanggal selesai sudah lewat,
         * jangan biarkan ujian tetap aktif.
         */
        if (
            !empty($data['tanggal_selesai']) &&
            now()->greaterThanOrEqualTo(
                Carbon::parse($data['tanggal_selesai'])
            )
        ) {
            $data['status'] = 'selesai';
            $data['token_aktif'] = false;
        }

        $ujian->update($data);

        return to_route('ujian.index')
            ->with('success', 'Ujian berhasil diperbarui.');
    }

    /**
     * =========================================================
     * DESTROY
     * =========================================================
     */
    public function destroy(Exam $ujian): RedirectResponse
    {
        if (
            $ujian->questions()->exists() ||
            $ujian->examAttempts()->exists()
        ) {
            return to_route('ujian.index')
                ->with(
                    'error',
                    'Ujian tidak dapat dihapus karena sudah memiliki soal atau peserta.'
                );
        }

        $ujian->delete();

        return to_route('ujian.index')
            ->with('success', 'Ujian berhasil dihapus.');
    }

    /**
     * =========================================================
     * AUTO SYNC SEMUA UJIAN YANG SUDAH SELESAI
     * =========================================================
     */
    private function syncExpiredExams(): void
    {
        Exam::query()
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_selesai')
            ->where(
                'tanggal_selesai',
                '<=',
                now()
            )
            ->update([
                'status' => 'selesai',
                'token_aktif' => false,
            ]);
    }

    /**
     * =========================================================
     * AUTO SYNC SATU UJIAN
     * =========================================================
     */
    private function syncExamStatus(Exam $exam): void
    {
        if (
            $exam->status === 'aktif' &&
            $exam->tanggal_selesai &&
            now()->greaterThanOrEqualTo(
                $exam->tanggal_selesai
            )
        ) {
            $exam->update([
                'status' => 'selesai',
                'token_aktif' => false,
            ]);

            $exam->refresh();
        }
    }

    /**
     * =========================================================
     * FORM DATA
     * =========================================================
     */
    private function formData(): array
    {
        return [
            'mataPelajarans' => MataPelajaran::orderBy('nama')
                ->get(),

            'gurus' => Guru::orderBy('nama')
                ->get(),

            'kelasData' => Kelas::query()
                ->orderBy('nama_kelas')
                ->get(),
        ];
    }

    /**
     * =========================================================
     * VALIDATION
     * =========================================================
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'tahun_ajaran' => [
                'nullable',
                'string',
                'max:20',
            ],

            'semester' => [
                'nullable',
                'in:ganjil,genap',
            ],

            'mata_pelajaran_id' => [
                'required',
                'exists:mata_pelajarans,id',
            ],

            'guru_id' => [
                'required',
                'exists:gurus,id',
            ],

            'guru_pengawas_id' => [
                'required',
                'integer',
                'exists:gurus,id',
            ],

            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'ruangan' => [
                'required',
                'string',
                'max:100',
            ],

            'tanggal_mulai_tanggal' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'tanggal_mulai_jam' => [
                'nullable',
                'date_format:H:i',
            ],

            'tanggal_selesai_tanggal' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'tanggal_selesai_jam' => [
                'nullable',
                'date_format:H:i',
            ],

            'durasi_menit' => [
                'required',
                'integer',
                'min:1',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:' . implode(',', self::STATUSES),
            ],
        ]);
    }

    /**
     * =========================================================
     * LEGACY TYPE
     * =========================================================
     */
    private function legacyType(array $components): string
    {
        sort($components);

        if ($components === ['pg']) {
            return 'pg';
        }

        if (!in_array('pg', $components, true)) {
            return 'essay';
        }

        return 'pg_essay';
    }

    /**
     * =========================================================
     * COMPONENTS FROM LEGACY TYPE
     * =========================================================
     */
    private function componentsFromLegacyType(
        ?string $type
    ): array {
        return match ($type) {
            'pg' => ['pg'],

            'essay' => [
                'essay_1',
                'essay_2',
            ],

            default => [
                'pg',
                'essay_1',
                'essay_2',
            ],
        };
    }

    /**
     * =========================================================
     * UNIQUE CODE
     * =========================================================
     */
    private function uniqueCode(): string
    {
        do {
            $code = Str::upper(
                Str::random(8)
            );
        } while (
            Exam::where(
                'kode_ujian',
                $code
            )->exists()
        );

        return $code;
    }

    /**
     * =========================================================
     * COMBINE DATE + TIME
     * =========================================================
     */
    private function combineDateTime(
        ?string $date,
        ?string $time
    ): ?string {
        return $date && $time
            ? $date . ' ' . $time
            : null;
    }

    /**
     * =========================================================
     * CALCULATE DURATION
     * =========================================================
     */
    private function durationInMinutes(
        ?string $start,
        ?string $end,
        int $fallback
    ): int {
        if (!$start || !$end) {
            return $fallback;
        }

        return max(
            1,
            Carbon::parse($start)
                ->diffInMinutes(
                    Carbon::parse($end)
                )
        );
    }
}