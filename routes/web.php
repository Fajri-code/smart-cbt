<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GuruExamController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\GuruTokenController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\GuruMonitoringController;
use App\Http\Controllers\SiswaExamController;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Exam;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Pattern
|--------------------------------------------------------------------------
*/

Route::pattern('guru', '[0-9]+');
Route::pattern('siswa', '[0-9]+');


/*
|--------------------------------------------------------------------------
| Halaman Welcome
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
|
| Admin  -> dashboard admin
| Guru   -> dashboard guru
| Siswa  -> dashboard siswa
|
*/

Route::get('/dashboard', function () {

    /*
    |--------------------------------------------------------------------------
    | Guru
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->isGuru()) {
        return redirect()->route('guru.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | Siswa
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->isSiswa()) {
        return redirect()->route('siswa.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Admin
    |--------------------------------------------------------------------------
    */

    // Master Data
    $totalSiswa = Siswa::count();
    $totalGuru = Guru::count();
    $totalKelas = Kelas::count();
    $totalMapel = MataPelajaran::count();

    // Statistik Ujian
    $totalUjian = Exam::count();

    $ujianAktif = Exam::where('status', 'aktif')->count();

    $ujianDraft = Exam::where(function ($query) {
        $query->where('status', 'draft')
              ->orWhereNull('status');
    })->count();

    $ujianSelesai = Exam::where('status', 'selesai')->count();

    // Ujian terbaru untuk dashboard
    $ujianTerbaru = Exam::with([
        'mataPelajaran',
        'kelasData'
    ])
    ->latest()
    ->take(5)
    ->get();

    return view('dashboard', compact(
        'totalSiswa',
        'totalGuru',
        'totalKelas',
        'totalMapel',
        'totalUjian',
        'ujianAktif',
        'ujianDraft',
        'ujianSelesai',
        'ujianTerbaru'
    ));

})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| SISWA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        Route::get('/dashboard', [SiswaExamController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/ujian', [SiswaExamController::class, 'index'])
            ->name('ujian.index');

        Route::get('/riwayat-ujian', [SiswaExamController::class, 'history'])
            ->name('riwayat');

        Route::get('/hasil-nilai', [SiswaExamController::class, 'results'])
            ->name('hasil');

        Route::get('/ujian/{ujian}', [SiswaExamController::class, 'show'])
            ->name('ujian.show');

        Route::get('/ujian/{ujian}/token', [SiswaExamController::class, 'token'])
            ->name('ujian.token');

        Route::post('/ujian/{ujian}/mulai', [SiswaExamController::class, 'start'])
            ->name('ujian.start');

        Route::get('/ujian/{ujian}/kerjakan', [SiswaExamController::class, 'work'])
            ->name('ujian.work');

        Route::post('/ujian/{ujian}/jawaban', [SiswaExamController::class, 'saveAnswers'])
            ->name('ujian.answers');

        Route::post('/ujian/{ujian}/keluar', [SiswaExamController::class, 'leave'])
            ->name('ujian.leave');

        Route::post('/ujian/{ujian}/kumpulkan', [SiswaExamController::class, 'submit'])
            ->name('ujian.submit');

        Route::get('/ujian/{ujian}/hasil', [SiswaExamController::class, 'result'])
            ->name('ujian.result');
    });


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Master Data
    |--------------------------------------------------------------------------
    */

    Route::resource('siswa', SiswaController::class);

    Route::resource('guru', GuruController::class);

    Route::resource('kelas', KelasController::class)
        ->parameters([
            'kelas' => 'kelas'
        ]);

    Route::resource('mata-pelajaran', MataPelajaranController::class);


    /*
    |--------------------------------------------------------------------------
    | Ujian
    |--------------------------------------------------------------------------
    */

    Route::resource('ujian', ExamController::class);


    /*
    |--------------------------------------------------------------------------
    | Hasil Ujian
    |--------------------------------------------------------------------------
    */

    Route::get('/hasil-ujian', [ResultController::class, 'index'])
        ->name('hasil.index');
});


/*
|--------------------------------------------------------------------------
| GURU
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard Guru
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', GuruDashboardController::class)
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Ujian
        |--------------------------------------------------------------------------
        */

        Route::get('/ujian', [GuruExamController::class, 'index'])
            ->name('ujian.index');

        Route::get('/ujian/{ujian}', [GuruExamController::class, 'show'])
            ->name('ujian.show');


        /*
        |--------------------------------------------------------------------------
        | Bank Soal
        |--------------------------------------------------------------------------
        */

        Route::get('/bank-soal', [QuestionBankController::class, 'index'])
            ->name('bank.index');

        Route::post('/bank-soal', [QuestionBankController::class, 'store'])
            ->name('bank.store');

        Route::get('/bank-soal/{bankSoal}', [QuestionBankController::class, 'show'])
            ->name('bank.show');

        Route::post('/bank-soal/{bankSoal}/soal', [QuestionBankController::class, 'storeQuestion'])
            ->name('bank.question.store');

        Route::delete('/bank-soal/{bankSoal}/soal/{bankQuestion}', [QuestionBankController::class, 'destroyQuestion'])
            ->name('bank.question.destroy');


        /*
        |--------------------------------------------------------------------------
        | Soal Ujian
        |--------------------------------------------------------------------------
        */

        Route::post('/ujian/{ujian}/soal/import-bank/{bankSoal}', [QuestionController::class, 'importBank'])
            ->name('soal.import-bank');

        Route::post('/ujian/{ujian}/terbitkan', [QuestionController::class, 'publish'])
            ->name('soal.publish');

        Route::post('/ujian/{ujian}/soal/import/{bankQuestion}', [QuestionController::class, 'import'])
            ->name('soal.import');

        Route::post('/ujian/{ujian}/soal/{soal}/duplicate', [QuestionController::class, 'duplicate'])
            ->name('soal.duplicate');

        Route::patch('/ujian/{ujian}/soal/reorder', [QuestionController::class, 'reorder'])
            ->name('soal.reorder');

        Route::resource('/ujian/{ujian}/soal', QuestionController::class)
            ->except(['show'])
            ->names('soal');


        /*
        |--------------------------------------------------------------------------
        | Token Ujian
        |--------------------------------------------------------------------------
        */

        Route::get('/token', [GuruTokenController::class, 'index'])
            ->name('token.index');

        Route::get('/ujian/{ujian}/token', [GuruTokenController::class, 'show'])
            ->name('token.show');

        Route::post('/ujian/{ujian}/token', [GuruTokenController::class, 'generate'])
            ->name('token.generate');

        Route::patch('/ujian/{ujian}/token', [GuruTokenController::class, 'toggle'])
            ->name('token.toggle');


        /*
        |--------------------------------------------------------------------------
        | Hasil Ujian
        |--------------------------------------------------------------------------
        */

        Route::get('/hasil-ujian', [ResultController::class, 'index'])
            ->name('hasil.index');


        /*
        |--------------------------------------------------------------------------
        | Monitoring
        |--------------------------------------------------------------------------
        */

        Route::get('/monitoring', [GuruMonitoringController::class, 'index'])
            ->name('monitoring.index');
    });


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';