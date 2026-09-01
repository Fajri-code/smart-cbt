<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->string('tahun_ajaran', 20)->nullable()->after('nama');
            $table->string('semester', 20)->nullable()->after('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->dropColumn(['tahun_ajaran', 'semester']);
        });
    }
};