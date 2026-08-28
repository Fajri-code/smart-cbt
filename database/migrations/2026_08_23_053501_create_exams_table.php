<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            // Guru yang membuat/mengelola ujian
            $table->foreignId('guru_id')
                ->constrained('gurus')
                ->cascadeOnDelete();

            // Mata pelajaran
            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->restrictOnDelete();

            $table->string('nama');

            // Contoh: UAS, UTS, Ulangan Harian
            $table->string('jenis')->nullable();

            // Kelas yang mengikuti ujian
            $table->string('kelas');

            // Kode yang diberikan guru kepada siswa
            $table->string('kode_ujian')->unique();

            // Durasi ujian dalam menit
            $table->unsignedInteger('durasi_menit');

            // Jadwal ujian
            $table->timestamp('tanggal_mulai')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();

            // draft / aktif / selesai
            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};