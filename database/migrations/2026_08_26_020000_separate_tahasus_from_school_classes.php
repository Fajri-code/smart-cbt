<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE siswas ALTER COLUMN kelas DROP NOT NULL');

        DB::table('siswas')->where('program_tahasus', true)->update([
            'kelas' => null,
            'kelas_id' => null,
        ]);
    }

    public function down(): void
    {
        // Tahasus students intentionally have no school class.
    }
};