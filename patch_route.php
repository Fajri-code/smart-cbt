<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$search = "->name('kartu-ujian.preview');";
$replace = "->name('kartu-ujian.preview');\n    Route::match(['get', 'post'], '/kartu-ujian/pengaturan', [App\Http\Controllers\ExamCardController::class, 'settings'])->name('kartu-ujian.settings');";
$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);
