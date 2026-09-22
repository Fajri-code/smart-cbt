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

        return view('guru.token-index', [
            'exams' => Exam::where(function ($q) use ($guru) {
                $q->where('guru_id', $guru->id)
                  ->orWhere('guru_pengawas_id', $guru->id);
            })->latest()->paginate(10)
        ]);
    }

    public function show(Exam $ujian, Request $request): View
    {
        $this->owned($ujian, $request);
        $ujian->rotateExpiredToken();
        
        $qrOptions = new \chillerlan\QRCode\QROptions([
            'version'         => \chillerlan\QRCode\Common\Version::AUTO,
            'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::M,
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'outputBase64'    => true,
            'addQuietzone'    => true,
        ]);
        $qrUrl = route('siswa.ujian.token', ['ujian' => $ujian, 't' => $ujian->token]);
        $qrCode = (new \chillerlan\QRCode\QRCode($qrOptions))->render($qrUrl);

        return view('guru.token', [
            'exam' => $ujian,
            'qrCode' => $qrCode,
            'qrUrl' => $qrUrl,
        ]);
    }

    public function generate(Exam $ujian, Request $request): RedirectResponse
    {
        $this->owned($ujian, $request);
        abort_if($ujian->status === 'selesai', 403, 'Ujian sudah selesai, token tidak bisa dibuat lagi.');
        
        $autoRotate = $request->boolean('auto_rotate');
        $ujian->activateToken($autoRotate);
        
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
        $guruId = $request->user()->guru?->id;
        abort_unless($exam->guru_id === $guruId || $exam->guru_pengawas_id === $guruId, 403);
    }
}