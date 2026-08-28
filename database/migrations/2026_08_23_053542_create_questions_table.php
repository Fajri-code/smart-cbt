<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            // pg / essay_1 / essay_2
            $table->string('tipe');

            // Nomor urut soal
            $table->unsignedInteger('urutan');

            // Pertanyaan
            $table->text('pertanyaan');

            // Khusus pilihan ganda
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();

            // Kunci jawaban untuk PG
            $table->string('kunci')->nullable();

            // Bobot nilai soal
            $table->decimal('bobot', 5, 2)->default(0);

            $table->timestamps();

            $table->unique(['exam_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};