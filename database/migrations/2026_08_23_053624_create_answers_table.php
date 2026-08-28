<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            // Attempt siswa
            $table->foreignId('exam_attempt_id')
                ->constrained('exam_attempts')
                ->cascadeOnDelete();

            // Soal yang dijawab
            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();

            // Jawaban pilihan ganda: A/B/C/D
            // atau teks jawaban untuk essay
            $table->text('jawaban')->nullable();

            // Untuk PG: apakah jawaban benar
            $table->boolean('is_correct')->nullable();

            // Nilai untuk soal ini
            // PG bisa otomatis, essay diisi guru
            $table->decimal('skor', 5, 2)->nullable();

            // Penanda apakah essay sudah dinilai guru
            $table->boolean('sudah_dinilai')->default(false);

            // Catatan guru ketika menilai essay
            $table->text('catatan_guru')->nullable();

            $table->timestamps();

            // Satu attempt hanya punya satu jawaban untuk
            // setiap soal
            $table->unique(['exam_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};