<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$siswa1 = App\Models\Siswa::first();
$siswa2 = App\Models\Siswa::skip(1)->first();
$exam = App\Models\Exam::where('acak_soal', true)->where('status', 'aktif')->first();

if (!$exam) {
    $exam = App\Models\Exam::where('acak_soal', true)->first();
    $exam->update(['status' => 'aktif', 'token_aktif' => true, 'token' => '12345']);
}

// Ensure attempts
$attempt1 = App\Models\ExamAttempt::firstOrCreate(['exam_id' => $exam->id, 'siswa_id' => $siswa1->id], ['started_at' => now(), 'status' => 'in_progress', 'token_used' => '12345']);
$attempt2 = App\Models\ExamAttempt::firstOrCreate(['exam_id' => $exam->id, 'siswa_id' => $siswa2->id], ['started_at' => now(), 'status' => 'in_progress', 'token_used' => '12345']);

// Call controller method
$controller = new App\Http\Controllers\SiswaExamController();
$req1 = Illuminate\Http\Request::create('/siswa/ujian/'.$exam->id.'/work', 'GET');
$req1->setUserResolver(function() use ($siswa1) {
    $user = new App\Models\User();
    $user->id = $siswa1->id; // hack
    // wait, request->user()->siswa needs to work
    $user->setRelation('siswa', $siswa1);
    return $user;
});
session()->put('siswa.exam_token_verified.'.$exam->id, $attempt1->id);
$req1->setLaravelSession(session());

try {
    $res1 = $controller->work($req1, $exam);
    $view1 = $res1->gatherData();
    echo "Siswa 1 Order: " . $view1['exam']->questions->pluck('id')->implode(',') . "\n";
} catch (\Exception $e) {
    echo "Error Siswa 1: " . $e->getMessage() . "\n";
}

$req2 = Illuminate\Http\Request::create('/siswa/ujian/'.$exam->id.'/work', 'GET');
$req2->setUserResolver(function() use ($siswa2) {
    $user = new App\Models\User();
    $user->setRelation('siswa', $siswa2);
    return $user;
});
session()->put('siswa.exam_token_verified.'.$exam->id, $attempt2->id);
$req2->setLaravelSession(session());

try {
    $res2 = $controller->work($req2, $exam);
    $view2 = $res2->gatherData();
    echo "Siswa 2 Order: " . $view2['exam']->questions->pluck('id')->implode(',') . "\n";
} catch (\Exception $e) {
    echo "Error Siswa 2: " . $e->getMessage() . "\n";
}
