<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nisn')->nullable()->unique()->after('nis');
            $table->foreignId('kelas_id')->nullable()->after('kelas')->constrained('kelas')->nullOnDelete();
            $table->boolean('program_tahasus')->default(false)->after('kelas_id');
            $table->string('tahun_ajaran', 20)->default('2026/2027')->after('program_tahasus');
            $table->boolean('status_aktif')->default(true)->after('tahun_ajaran');
        });

        DB::table('siswas')->whereNull('nisn')->update(['nisn' => DB::raw('nis')]);

        $classes = DB::table('kelas')->pluck('id', 'nama_kelas');

        foreach ($classes as $name => $id) {
            DB::table('siswas')->where('kelas', $name)->update(['kelas_id' => $id]);
        }

        foreach ([
            'VII - Tahasus' => 'VII - Ibnu Taimiyah',
            'VIII - Tahasus' => 'VIII - Ibnu Khaldun',
            'IX - Tahasus' => 'IX - Ibnu Hisyam',
        ] as $legacyClass => $schoolClass) {
            DB::table('siswas')->where('kelas', $legacyClass)->update([
                'kelas' => $schoolClass,
                'kelas_id' => $classes[$schoolClass] ?? null,
                'program_tahasus' => true,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn(['nisn', 'kelas_id', 'program_tahasus', 'tahun_ajaran', 'status_aktif']);
        });
    }
};