<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();

            // Ujian yang dikerjakan
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            // Siswa yang mengerjakan
            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            // Waktu mulai mengerjakan
            $table->timestamp('started_at')->nullable();

            // Waktu selesai mengerjakan
            $table->timestamp('submitted_at')->nullable();

            // in_progress / submitted / expired
            $table->string('status')->default('in_progress');

            // Nilai akhir setelah semua penilaian selesai
            $table->decimal('nilai_akhir', 5, 2)->nullable();

            // Nomor percobaan, untuk jaga-jaga kalau nanti
            // sistem mengizinkan siswa mengulang ujian
            $table->unsignedInteger('attempt_number')->default(1);

            $table->timestamps();

            // Satu siswa tidak boleh punya dua attempt
            // untuk ujian yang sama pada sistem kita saat ini.
            $table->unique(['exam_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};