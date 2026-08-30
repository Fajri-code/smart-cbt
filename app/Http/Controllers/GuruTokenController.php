<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruTokenController extends Controller
{
    public function index(Request $request): View
    {
        $guru = $request->user()->guru;
        abort_unless($guru, 403);

        return view('guru.token-index', ['exams' => Exam::where('guru_id', $guru->id)->latest()->paginate(10)]);
    }

    public function show(Exam $ujian, Request $request): View
    {
        $this->owned($ujian, $request);
        $ujian->rotateExpiredToken();
        return view('guru.token', ['exam' => $ujian]);
    }

    public function generate(Exam $ujian, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_if($ujian->status === 'selesai', 403, 'Ujian sudah selesai, token tidak bisa dibuat lagi.');
        $ujian->activateToken();
        return back()->with('success', 'Token ujian berhasil dibuat.');
    }

    public function toggle(Exam $ujian, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_if($ujian->status === 'selesai', 403, 'Ujian sudah selesai, status token tidak bisa diubah lagi.');
        $ujian->update(['token_aktif' => ! $ujian->token_aktif]);
        return back()->with('success', 'Status token diperbarui.');
    }

    private function owned(Exam $exam, Request $request): void
    {
        abort_unless($exam->guru_id === $request->user()->guru?->id, 403);
    }
}