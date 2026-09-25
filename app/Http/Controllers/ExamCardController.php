<?php

namespace App\Http\Controllers;

use App\Models\ExamCardPrint;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\ExamCardSetting;
use Illuminate\Support\Facades\Storage;
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

        public function settings(Request $request)
    {
        $setting = ExamCardSetting::first() ?? new ExamCardSetting();

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'header_1' => 'nullable|string',
                'header_2' => 'nullable|string',
                'header_3' => 'nullable|string',
                'header_4' => 'nullable|string',
                'judul_kartu' => 'nullable|string',
                'tempat_tanggal' => 'nullable|string',
                'jabatan_penandatangan' => 'nullable|string',
                'nama_penandatangan' => 'nullable|string',
                'nip_penandatangan' => 'nullable|string',
                'logo_kiri' => 'nullable|image|max:2048',
                'ttd_image' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('logo_kiri')) {
                if ($setting->logo_kiri) Storage::disk('public')->delete($setting->logo_kiri);
                $data['logo_kiri'] = $request->file('logo_kiri')->store('exam-cards', 'public');
            }
            if ($request->hasFile('ttd_image')) {
                if ($setting->ttd_image) Storage::disk('public')->delete($setting->ttd_image);
                $data['ttd_image'] = $request->file('ttd_image')->store('exam-cards', 'public');
            }

            $setting->fill($data);
            $setting->save();

            return back()->with('success', 'Pengaturan kartu ujian berhasil disimpan beserta history pembuatannya.');
        }

        return view('exam-cards.settings', compact('setting'));
    }

    public function preview(Request $request): View
    {
        [$namaUjian, $ruangan, $kelas, $students] = $this->cardData($request);

        $setting = ExamCardSetting::first() ?? new ExamCardSetting();
        return view('exam-cards.preview', compact('namaUjian', 'ruangan', 'kelas', 'students', 'setting'));
    }

    public function pdf(Request $request)
    {
        [$namaUjian, $ruangan, $kelas, $students] = $this->cardData($request);

        $setting = ExamCardSetting::first() ?? new ExamCardSetting();
        $logoBase64 = $this->getImageBase64($setting->logo_kiri);
        $ttdBase64 = $this->getImageBase64($setting->ttd_image);

        $pdf = Pdf::loadView('exam-cards.pdf', compact('namaUjian', 'ruangan', 'kelas', 'students', 'setting', 'logoBase64', 'ttdBase64'))
            ->setPaper('a4', 'portrait');
            
        ExamCardPrint::updateOrCreate(
            ['nama_ujian' => $namaUjian, 'kelas_id' => $kelas->id],
            ['ruangan' => $ruangan, 'jumlah_kartu' => $students->count()]
        );
        
        $filename = 'kartu-ujian-'.Str::slug($kelas->nama_kelas).'-'.Str::slug($namaUjian).'.pdf';

        return $pdf->download($filename);
    }

    private function getImageBase64(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        try {
            $cleanPath = ltrim(preg_replace('#^/?storage/#', '', $path), '/');

            if (Storage::disk('public')->exists($cleanPath)) {
                $content = Storage::disk('public')->get($cleanPath);
                $mime = Storage::disk('public')->mimeType($cleanPath) ?: 'image/png';
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }

            $publicPath = public_path('storage/' . $cleanPath);
            if (file_exists($publicPath)) {
                $content = file_get_contents($publicPath);
                $mime = mime_content_type($publicPath) ?: 'image/png';
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }

            $storageAppPath = storage_path('app/public/' . $cleanPath);
            if (file_exists($storageAppPath)) {
                $content = file_get_contents($storageAppPath);
                $mime = mime_content_type($storageAppPath) ?: 'image/png';
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        } catch (\Throwable $e) {
            // Silently fallback
        }

        return null;
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
