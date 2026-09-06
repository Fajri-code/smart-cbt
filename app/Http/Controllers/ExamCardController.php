<?php

namespace App\Http\Controllers;

use App\Models\ExamCardPrint;
use App\Models\Kelas;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ExamCardController extends Controller
{
    public function index(Request $request): View
    {
        $classes = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
            
        $namaUjian = $request->input('nama_ujian');
        $ruangan = $request->input('ruangan');
        $selectedClass = $request->integer('kelas_id');
        
        $participantCount = 0;
        $students = collect();
        $printStatus = null;

        if ($namaUjian && $selectedClass && $ruangan) {
            $students = $this->participantQuery($selectedClass)
                ->with('user')
                ->orderBy('nama')
                ->orderBy('id')
                ->get();
            $participantCount = $students->count();
            $printStatus = ExamCardPrint::where('nama_ujian', $namaUjian)->where('kelas_id', $selectedClass)->first();
        }

        return view('exam-cards.index', compact(
            'classes',
            'namaUjian',
            'ruangan',
            'selectedClass',
            'participantCount',
            'students',
            'printStatus'
        ));
    }

    public function preview(Request $request): View
    {
        [$namaUjian, $ruangan, $kelas, $students] = $this->cardData($request);

        return view('exam-cards.preview', compact('namaUjian', 'ruangan', 'kelas', 'students'));
    }

    public function pdf(Request $request)
    {
        [$namaUjian, $ruangan, $kelas, $students] = $this->cardData($request);

        $pdf = Pdf::loadView('exam-cards.pdf', compact('namaUjian', 'ruangan', 'kelas', 'students'))
            ->setPaper('a4', 'portrait');
            
        ExamCardPrint::updateOrCreate(
            ['nama_ujian' => $namaUjian, 'kelas_id' => $kelas->id],
            ['ruangan' => $ruangan, 'jumlah_kartu' => $students->count()]
        );
        
        $filename = 'kartu-ujian-'.Str::slug($kelas->nama_kelas).'-'.Str::slug($namaUjian).'.pdf';

        return $pdf->download($filename);
    }

    private function cardData(Request $request): array
    {
        $validated = $request->validate([
            'nama_ujian' => ['required', 'string', 'max:255'],
            'ruangan' => ['required', 'string', 'max:255'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
        ]);
        
        $kelas = Kelas::findOrFail($validated['kelas_id']);
        $students = $this->participantQuery($kelas->id)
            ->with('user')
            ->orderBy('nama')
            ->orderBy('id')
            ->get();

        abort_if($students->isEmpty(), 422, 'Belum ada peserta ujian untuk kelas ini.');

        $mode_duduk = $request->input('mode_duduk', 'otomatis');
        $tempat_duduk = $request->input('tempat_duduk', []);

        foreach ($students as $index => $student) {
            if ($mode_duduk === 'manual' && isset($tempat_duduk[$student->id])) {
                $student->nomor_bangku = $tempat_duduk[$student->id];
            } else {
                $student->nomor_bangku = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            }
        }

        return [$validated['nama_ujian'], $validated['ruangan'], $kelas, $students];
    }

    private function participantQuery(?int $classId)
    {
        return Siswa::query()
            ->where('kelas_id', $classId)
            ->where('status_aktif', true);
    }
}
