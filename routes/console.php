<?php

use App\Models\Exam;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Exam::query()
        ->where('token_aktif', true)
        ->whereNotNull('token_kedaluwarsa_at')
        ->where('token_kedaluwarsa_at', '<=', now())
        ->get()
        ->each(fn (Exam $exam) => $exam->activateToken());
})->everyMinute();
