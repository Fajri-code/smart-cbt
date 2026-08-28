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
    private const COMPONENTS = ['pg', 'essay_1', 'essay_2'];
    private const STATUSES = ['draft', 'aktif'];

    public function index(): View
    {
        $exams = Exam::with(['mataPelajaran', 'guru', 'guruPengawas'])
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        return view('ujian.index', compact('exams'));
    }

    public function create(): View
    {
        return view('ujian.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['tanggal_mulai'] = $this->combineDateTime($data['tanggal_mulai_tanggal'] ?? null, $data['tanggal_mulai_jam'] ?? null);
        $data['tanggal_selesai'] = $this->combineDateTime($data['tanggal_selesai_tanggal'] ?? null, $data['tanggal_selesai_jam'] ?? null);
        unset($data['tanggal_mulai_tanggal'], $data['tanggal_mulai_jam'], $data['tanggal_selesai_tanggal'], $data['tanggal_selesai_jam']);
        $data['durasi_menit'] = $this->durationInMinutes($data['tanggal_mulai'], $data['tanggal_selesai'], $data['durasi_menit']);
        $data['jenis'] = $this->legacyType($data['komponen_soal']);
        $data['kelas'] = $data['kelas_id'] ?? '-';
        $data['kode_ujian'] = $this->uniqueCode();

        Exam::create($data);

        return to_route('ujian.index')->with('success', 'Ujian berhasil ditambahkan.');
    }

    public function show(Exam $ujian): View
    {
        $ujian->load(['mataPelajaran', 'guru', 'kelasData', 'guruPengawas']);
        $questionCounts = $ujian->questions()
            ->selectRaw('tipe, count(*) as total')
            ->groupBy('tipe')
            ->pluck('total', 'tipe');

        return view('ujian.show', [
            'exam' => $ujian,
            'pgCount' => (int) ($questionCounts['pg'] ?? 0),
            'essayCount' => (int) $questionCounts->except('pg')->sum(),
            'totalQuestions' => $questionCounts->sum(),
            'totalSchedules' => 0,
            'totalParticipants' => $ujian->examAttempts()->count(),
        ]);
    }

    public function edit(Exam $ujian): View
    {
        return view('ujian.edit', array_merge(['exam' => $ujian], $this->formData()));
    }

    public function update(Request $request, Exam $ujian): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['tanggal_mulai'] = $this->combineDateTime($data['tanggal_mulai_tanggal'] ?? null, $data['tanggal_mulai_jam'] ?? null);
        $data['tanggal_selesai'] = $this->combineDateTime($data['tanggal_selesai_tanggal'] ?? null, $data['tanggal_selesai_jam'] ?? null);
        unset($data['tanggal_mulai_tanggal'], $data['tanggal_mulai_jam'], $data['tanggal_selesai_tanggal'], $data['tanggal_selesai_jam']);
        $data['durasi_menit'] = $this->durationInMinutes($data['tanggal_mulai'], $data['tanggal_selesai'], $data['durasi_menit']);
        $data['jenis'] = $this->legacyType($data['komponen_soal']);
        $existingComponents = $ujian->komponen_soal ?: $this->componentsFromLegacyType($ujian->jenis);
        sort($existingComponents);
        $newComponents = $data['komponen_soal'];
        sort($newComponents);

        if ($ujian->questions()->exists() && $newComponents !== $existingComponents) {
            return back()->withInput()->withErrors([
                'jenis' => 'Jenis ujian tidak dapat diubah karena ujian sudah memiliki soal.',
            ]);
        }

        $ujian->update($data);

        return to_route('ujian.index')->with('success', 'Ujian berhasil diperbarui.');
    }

    public function destroy(Exam $ujian): RedirectResponse
    {
        if ($ujian->questions()->exists() || $ujian->examAttempts()->exists()) {
            return to_route('ujian.index')->with('error', 'Ujian tidak dapat dihapus karena sudah memiliki soal atau peserta.');
        }

        $ujian->delete();

        return to_route('ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'mataPelajarans' => MataPelajaran::orderBy('nama')->get(),
            'gurus' => Guru::orderBy('nama')->get(),
            'kelasData' => Kelas::where('status', 'aktif')->orderBy('nama_kelas')->get(),
        ];
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'guru_id' => ['required', 'exists:gurus,id'],
            'guru_pengawas_id' => ['required', 'integer', 'exists:gurus,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'ruangan' => ['required', 'string', 'max:100'],
            'tanggal_mulai_tanggal' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_mulai_jam' => ['nullable', 'date_format:H:i'],
            'tanggal_selesai_tanggal' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai_jam' => ['nullable', 'date_format:H:i'],
            'komponen_soal' => ['required', 'array', 'min:1'],
            'komponen_soal.*' => ['required', 'in:'.implode(',', self::COMPONENTS)],
            'durasi_menit' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
        ]);
    }

    private function legacyType(array $components): string
    {
        sort($components);

        if ($components === ['pg']) {
            return 'pg';
        }

        if (! in_array('pg', $components, true)) {
            return 'essay';
        }

        return 'pg_essay';
    }

    private function componentsFromLegacyType(?string $type): array
    {
        return match ($type) {
            'pg' => ['pg'],
            'essay' => ['essay_1', 'essay_2'],
            default => ['pg', 'essay_1', 'essay_2'],
        };
    }

    private function uniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (Exam::where('kode_ujian', $code)->exists());

        return $code;
    }

    private function combineDateTime(?string $date, ?string $time): ?string
    {
        return $date && $time ? $date.' '.$time : null;
    }

    private function durationInMinutes(?string $start, ?string $end, int $fallback): int
    {
        if (! $start || ! $end) {
            return $fallback;
        }

        return max(1, Carbon::parse($start)->diffInMinutes(Carbon::parse($end)));
    }

}