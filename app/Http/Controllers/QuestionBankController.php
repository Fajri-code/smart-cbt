<?php

namespace App\Http\Controllers;

use App\Models\BankQuestion;
use App\Models\QuestionBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $guruId = $request->user()->guru?->id;
        abort_unless($guruId, 403);
        $query = QuestionBank::with('mataPelajaran')->withCount('questions')->where('guru_id', $guruId);
        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->string('q').'%');
        }
        if ($request->filled('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $request->integer('mata_pelajaran_id'));
        }
        if ($request->filled('tipe')) {
            $query->whereHas('questions', fn ($questions) => $questions->where('tipe', $request->string('tipe')));
        }
        $banks = $query->latest()->get();
        return view('guru.bank-soal.index', ['banks' => $banks, 'subjects' => \App\Models\MataPelajaran::orderBy('nama')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $guruId = $request->user()->guru?->id;
        abort_unless($guruId, 403);
        $data = $request->validate(['nama' => ['required', 'string', 'max:255'], 'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id']]);
        QuestionBank::create([...$data, 'guru_id' => $guruId]);
        return back()->with('success', 'Bank soal berhasil dibuat.');
    }

    public function show(QuestionBank $bankSoal, Request $request): View
    {
        $this->owned($bankSoal, $request);
        return view('guru.bank-soal.show', ['bank' => $bankSoal->load('mataPelajaran'), 'questions' => $bankSoal->questions()->latest()->paginate(15)]);
    }

    public function storeQuestion(QuestionBank $bankSoal, Request $request): RedirectResponse
    {
        $this->owned($bankSoal, $request);
        $data = $request->validate(['tipe' => ['required', 'in:pg,essay_1,essay_2'], 'pertanyaan' => ['required', 'string'], 'petunjuk_jawaban' => ['nullable', 'string'], 'opsi_a' => ['nullable', 'string'], 'opsi_b' => ['nullable', 'string'], 'opsi_c' => ['nullable', 'string'], 'opsi_d' => ['nullable', 'string'], 'opsi_e' => ['nullable', 'string'], 'kunci' => ['nullable', 'in:A,B,C,D,E'], 'bobot' => ['required', 'numeric', 'min:0']]);
        BankQuestion::create([...$data, 'question_bank_id' => $bankSoal->id]);
        return back()->with('success', 'Soal disimpan ke bank.');
    }

    public function destroyQuestion(QuestionBank $bankSoal, BankQuestion $bankQuestion, Request $request): RedirectResponse
    {
        $this->owned($bankSoal, $request);
        abort_unless($bankQuestion->question_bank_id === $bankSoal->id, 404);
        $bankQuestion->delete();
        return back()->with('success', 'Soal bank dihapus.');
    }

    private function owned(QuestionBank $bank, Request $request): void
    {
        abort_unless($bank->guru_id === $request->user()->guru?->id, 403);
    }
}