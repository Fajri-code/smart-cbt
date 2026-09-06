<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_card_prints', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ujian');
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('ruangan');
            $table->integer('jumlah_kartu');
            $table->timestamps();

            // To avoid exact duplicates, maybe unique on nama_ujian + kelas_id?
            $table->unique(['nama_ujian', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_card_prints');
    }
};

