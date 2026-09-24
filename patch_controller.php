<?php
$file = 'app/Http/Controllers/ExamCardController.php';
$content = file_get_contents($file);

$search = "use App\Models\Siswa;";
$replace = "use App\Models\Siswa;\nuse App\Models\ExamCardSetting;\nuse Illuminate\Support\Facades\Storage;";
$content = str_replace($search, $replace, $content);

$search2 = "public function preview(Request \$request): View\n    {";
$replace2 = "public function settings(Request \$request)\n    {\n        \$setting = ExamCardSetting::first() ?? new ExamCardSetting();\n\n        if (\$request->isMethod('post')) {\n            \$data = \$request->validate([\n                'header_1' => 'nullable|string',\n                'header_2' => 'nullable|string',\n                'header_3' => 'nullable|string',\n                'header_4' => 'nullable|string',\n                'judul_kartu' => 'nullable|string',\n                'tempat_tanggal' => 'nullable|string',\n                'jabatan_penandatangan' => 'nullable|string',\n                'nama_penandatangan' => 'nullable|string',\n                'nip_penandatangan' => 'nullable|string',\n                'logo_kiri' => 'nullable|image|max:2048',\n                'ttd_image' => 'nullable|image|max:2048',\n            ]);\n\n            if (\$request->hasFile('logo_kiri')) {\n                if (\$setting->logo_kiri) Storage::disk('public')->delete(\$setting->logo_kiri);\n                \$data['logo_kiri'] = \$request->file('logo_kiri')->store('exam-cards', 'public');\n            }\n            if (\$request->hasFile('ttd_image')) {\n                if (\$setting->ttd_image) Storage::disk('public')->delete(\$setting->ttd_image);\n                \$data['ttd_image'] = \$request->file('ttd_image')->store('exam-cards', 'public');\n            }\n\n            \$setting->fill(\$data);\n            \$setting->save();\n\n            return back()->with('success', 'Pengaturan kartu ujian berhasil disimpan beserta history pembuatannya.');\n        }\n\n        return view('exam-cards.settings', compact('setting'));\n    }\n\n    public function preview(Request \$request): View\n    {";

$content = str_replace($search2, $replace2, $content);

// Also pass setting to preview and pdf
$search3 = "return view('exam-cards.preview', compact('namaUjian', 'ruangan', 'kelas', 'students'));";
$replace3 = "\$setting = ExamCardSetting::first();\n        return view('exam-cards.preview', compact('namaUjian', 'ruangan', 'kelas', 'students', 'setting'));";
$content = str_replace($search3, $replace3, $content);

$search4 = "\$pdf = Pdf::loadView('exam-cards.pdf', compact('namaUjian', 'ruangan', 'kelas', 'students'))";
$replace4 = "\$setting = ExamCardSetting::first();\n        \$pdf = Pdf::loadView('exam-cards.pdf', compact('namaUjian', 'ruangan', 'kelas', 'students', 'setting'))";
$content = str_replace($search4, $replace4, $content);

file_put_contents($file, $content);
