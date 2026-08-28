<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionController extends Controller
{
    private function owned(Exam $exam, Request $request): void
    {
        abort_unless($exam->guru_id === $request->user()->guru?->id, 403);
    }

    public function index(Exam $ujian, Request $request): View
    {
        $this->owned($ujian, $request);
        $ujian->load(['mataPelajaran', 'kelasData']);
        $questions = $ujian->questions()->paginate(15);
        $banks = QuestionBank::with('questions')
            ->where('guru_id', $request->user()->guru?->id)
            ->latest()
            ->get();
        $importedQuestions = $ujian->questions()->pluck('pertanyaan')->all();
        $totalQuestions = $ujian->questions()->count();
        $pgCount = $ujian->questions()->where('tipe', 'pg')->count();
        $essayCount = $ujian->questions()->whereIn('tipe', ['essay_1', 'essay_2'])->count();
        $totalBobot = (float) $ujian->questions()->sum('bobot');

        return view('guru.soal.index', [
            'exam' => $ujian,
            'questions' => $questions,
            'banks' => $banks,
            'importedQuestions' => $importedQuestions,
            'totalQuestions' => $totalQuestions,
            'pgCount' => $pgCount,
            'essayCount' => $essayCount,
            'totalBobot' => $totalBobot,
        ]);
    }

    public function publish(Exam $ujian, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);

        if (! $ujian->questions()->exists()) {
            return back()->withErrors(['questions' => 'Tambahkan minimal satu soal sebelum menerbitkan ujian.']);
        }

        $ujian->activateToken();

        return back()->with('success', 'Ujian siap dikerjakan siswa dan token aktif telah dibuat.');
    }

    public function create(Exam $ujian, Request $request): View
    {
        $this->owned($ujian, $request);
        $ujian->load(['mataPelajaran', 'kelasData']);

        return view('guru.soal.form', ['exam' => $ujian, 'question' => new Question(), 'allowedTypes' => $this->allowedTypes($ujian)]);
    }

    public function store(Exam $ujian, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        $data = $this->validated($request, $ujian);
        $data['exam_id'] = $ujian->id;
        $data['urutan'] = ((int) $ujian->questions()->max('urutan')) + 1;
        Question::create($data);
        $ujian->prepareToken();

        return to_route('guru.soal.index', $ujian)->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Exam $ujian, Question $soal, Request $request): View
    {
        $this->owned($ujian, $request);
        abort_unless($soal->exam_id === $ujian->id, 404);
        $ujian->load(['mataPelajaran', 'kelasData']);

        return view('guru.soal.form', ['exam' => $ujian, 'question' => $soal, 'allowedTypes' => $this->allowedTypes($ujian)]);
    }

    public function update(Exam $ujian, Question $soal, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_unless($soal->exam_id === $ujian->id, 404);
        $soal->update($this->validated($request, $ujian));
        $ujian->prepareToken();

        return to_route('guru.soal.index', $ujian)->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Exam $ujian, Question $soal, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_unless($soal->exam_id === $ujian->id, 404);
        $soal->delete();

        return to_route('guru.soal.index', $ujian)->with('success', 'Soal berhasil dihapus.');
    }

    public function duplicate(Exam $ujian, Question $soal, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_unless($soal->exam_id === $ujian->id, 404);
        $copy = $soal->replicate();
        $copy->urutan = ((int) $ujian->questions()->max('urutan')) + 1;
        $copy->save();
        $ujian->prepareToken();
        return back()->with('success', 'Soal berhasil diduplikat.');
    }

    public function reorder(Exam $ujian, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        $data = $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer', 'min:1']]);
        $questionIds = $ujian->questions()->whereIn('questions.id', array_keys($data['order']))->pluck('questions.id');
        DB::transaction(function () use ($ujian, $questionIds, $data): void {
            DB::table('questions')->where('exam_id', $ujian->id)->whereIn('id', $questionIds)->update(['urutan' => DB::raw('urutan + 100000')]);
            foreach ($data['order'] as $questionId => $position) {
                if ($questionIds->contains((int) $questionId)) {
                    DB::table('questions')->where('exam_id', $ujian->id)->where('id', $questionId)->update(['urutan' => (int) $position]);
                }
            }
        });
        return back()->with('success', 'Urutan soal diperbarui.');
    }

    public function import(Exam $ujian, \App\Models\BankQuestion $bankQuestion, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_unless($bankQuestion->bank->guru_id === $request->user()->guru?->id, 403);
        abort_unless(in_array($bankQuestion->tipe, $this->allowedTypes($ujian), true), 422);
        if ($ujian->questions()->where('pertanyaan', $bankQuestion->pertanyaan)->exists()) {
            return to_route('guru.soal.index', $ujian)->with('success', 'Soal tersebut sudah ada di ujian.');
        }
        $question = new Question([
            'tipe' => $bankQuestion->tipe,
            'pertanyaan' => $bankQuestion->pertanyaan,
            'petunjuk_jawaban' => $bankQuestion->petunjuk_jawaban,
            'opsi_a' => $bankQuestion->opsi_a,
            'opsi_b' => $bankQuestion->opsi_b,
            'opsi_c' => $bankQuestion->opsi_c,
            'opsi_d' => $bankQuestion->opsi_d,
            'opsi_e' => $bankQuestion->opsi_e,
            'kunci' => $bankQuestion->kunci,
            'bobot' => $bankQuestion->bobot,
            'exam_id' => $ujian->id,
            'urutan' => ((int) $ujian->questions()->max('urutan')) + 1,
        ]);
        $question->save();
        $ujian->prepareToken();
        return to_route('guru.soal.index', $ujian)->with('success', 'Soal berhasil dimasukkan dari bank soal.');
    }

    public function importBank(Exam $ujian, QuestionBank $bankSoal, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_unless($bankSoal->guru_id === $request->user()->guru?->id, 403);

        $allowedTypes = $this->allowedTypes($ujian);
        $existingTexts = $ujian->questions()->pluck('pertanyaan');
        $questions = $bankSoal->questions()
            ->whereIn('tipe', $allowedTypes)
            ->whereNotIn('pertanyaan', $existingTexts)
            ->get();
        $nextOrder = (int) $ujian->questions()->max('urutan');

        foreach ($questions as $bankQuestion) {
            Question::create([
                'tipe' => $bankQuestion->tipe,
                'pertanyaan' => $bankQuestion->pertanyaan,
                'petunjuk_jawaban' => $bankQuestion->petunjuk_jawaban,
                'opsi_a' => $bankQuestion->opsi_a,
                'opsi_b' => $bankQuestion->opsi_b,
                'opsi_c' => $bankQuestion->opsi_c,
                'opsi_d' => $bankQuestion->opsi_d,
                'opsi_e' => $bankQuestion->opsi_e,
                'kunci' => $bankQuestion->kunci,
                'bobot' => $bankQuestion->bobot,
                'exam_id' => $ujian->id,
                'urutan' => ++$nextOrder,
            ]);
        }

        if ($questions->isNotEmpty()) {
            $ujian->prepareToken();
        }

        return to_route('guru.soal.index', $ujian)->with('success', $questions->count().' soal berhasil diimport dari bank soal.');
    }

    private function validated(Request $request, ?Exam $exam = null): array
    {
        return $request->validate([
            'tipe' => ['required', 'in:pg,essay_1,essay_2'],
            'pertanyaan' => ['required', 'string'],
            'petunjuk_jawaban' => ['nullable', 'string'],
            'opsi_a' => ['nullable', 'string'], 'opsi_b' => ['nullable', 'string'],
            'opsi_c' => ['nullable', 'string'], 'opsi_d' => ['nullable', 'string'], 'opsi_e' => ['nullable', 'string'],
            'kunci' => ['nullable', 'in:A,B,C,D,E'],
            'bobot' => ['required', 'numeric', 'min:0'],
        ]);

        if ($exam && ! in_array($data['tipe'], $this->allowedTypes($exam), true)) {
            abort(422, 'Jenis soal tidak termasuk komponen ujian ini.');
        }
        if ($data['tipe'] === 'pg') {
            $request->validate([
                'opsi_a' => ['required', 'string'], 'opsi_b' => ['required', 'string'],
                'opsi_c' => ['required', 'string'], 'opsi_d' => ['required', 'string'],
                'opsi_e' => ['required_if:kunci,E', 'nullable', 'string'],
                'kunci' => ['required', 'in:A,B,C,D,E'],
            ]);
        } else {
            $data = array_merge($data, ['opsi_a' => null, 'opsi_b' => null, 'opsi_c' => null, 'opsi_d' => null, 'opsi_e' => null, 'kunci' => null]);
        }
        return $data;
    }

    private function allowedTypes(Exam $exam): array
    {
        return $exam->komponen_soal ?: match ($exam->jenis) {
            'pg' => ['pg'],
            'essay' => ['essay_1', 'essay_2'],
            default => ['pg', 'essay_1', 'essay_2'],
        };
    }
}