<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->foreignId('guru_pengawas_id')->nullable()->after('guru_id')->constrained('gurus')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->dropForeign(['guru_pengawas_id']);
            $table->dropColumn('guru_pengawas_id');
        });
    }
};