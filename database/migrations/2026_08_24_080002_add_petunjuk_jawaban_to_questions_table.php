<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', fn (Blueprint $table) => $table->text('petunjuk_jawaban')->nullable()->after('pertanyaan'));
    }

    public function down(): void
    {
        Schema::table('questions', fn (Blueprint $table) => $table->dropColumn('petunjuk_jawaban'));
    }
};