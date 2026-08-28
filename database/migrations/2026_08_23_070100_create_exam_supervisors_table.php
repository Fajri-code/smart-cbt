<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_supervisors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->string('ruangan');
            $table->timestamps();
            $table->unique(['exam_id', 'guru_id', 'ruangan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_supervisors');
    }
};