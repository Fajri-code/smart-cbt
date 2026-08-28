<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->foreignId('kelas_id')->nullable()->after('kelas')->constrained('kelas')->nullOnDelete();
            $table->string('ruangan')->nullable()->after('kelas_id');
            $table->string('token', 6)->nullable()->unique()->after('kode_ujian');
            $table->boolean('token_aktif')->default(false)->after('token');
            $table->timestamp('token_dibuat_at')->nullable()->after('token_aktif');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->dropForeign(['kelas_id']);
            $table->dropUnique(['token']);
            $table->dropColumn(['kelas_id', 'ruangan', 'token', 'token_aktif', 'token_dibuat_at']);
        });
    }
};