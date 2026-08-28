<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->restrictOnDelete();
            $table->string('nama');
            $table->timestamps();
        });
        Schema::create('bank_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_bank_id')->constrained()->cascadeOnDelete();
            $table->string('tipe');
            $table->text('pertanyaan');
            $table->text('petunjuk_jawaban')->nullable();
            foreach (['a', 'b', 'c', 'd', 'e'] as $option) {
                $table->text('opsi_'.$option)->nullable();
            }
            $table->string('kunci')->nullable();
            $table->decimal('bobot', 8, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_questions');
        Schema::dropIfExists('question_banks');
    }
};