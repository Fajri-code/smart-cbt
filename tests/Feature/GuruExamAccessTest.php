<?php

use App\Models\Exam;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeGuruWithExam(array $examOverrides = []): array
{
    $user = User::factory()->create(['role' => 'guru']);
    $guru = Guru::create([
        'user_id' => $user->id,
        'nama' => $user->name,
        'kode_guru' => fake()->unique()->bothify('GURU-###'),
    ]);
    $subject = MataPelajaran::create(['kode' => fake()->unique()->bothify('MAP-###'), 'nama' => 'Matematika']);
    $class = Kelas::create(['nama_kelas' => fake()->unique()->bothify('X-#'), 'tingkat' => '10', 'status' => 'aktif']);
    $exam = Exam::create(array_merge([
        'guru_id' => $guru->id,
        'mata_pelajaran_id' => $subject->id,
        'nama' => 'Ujian Matematika',
        'kelas_id' => $class->id,
        'kelas' => $class->nama_kelas,
        'ruangan' => 'Lab 1',
        'kode_ujian' => fake()->unique()->bothify('EXAM-####'),
        'durasi_menit' => 90,
        'tanggal_mulai' => now()->addDay(),
        'tanggal_selesai' => now()->addDay()->addMinutes(90),
        'status' => 'draft',
        'komponen_soal' => ['pg'],
    ], $examOverrides));

    return [$user, $guru, $exam];
}

test('guru sees only assigned exams', function () {
    [$user, $guru, $ownedExam] = makeGuruWithExam();
    [, , $otherExam] = makeGuruWithExam(['nama' => 'Ujian Guru Lain']);

    $response = $this->actingAs($user)->get(route('guru.ujian.index'));

    $response->assertOk()
        ->assertSee($ownedExam->nama)
        ->assertDontSee($otherExam->nama);
});

test('guru cannot open another gurus exam detail', function () {
    [$user] = makeGuruWithExam();
    [, , $otherExam] = makeGuruWithExam();

    $this->actingAs($user)
        ->get(route('guru.ujian.show', $otherExam))
        ->assertForbidden();
});

test('guru dashboard counts only assigned exams', function () {
    [$user] = makeGuruWithExam(['status' => 'aktif']);
    makeGuruWithExam(['status' => 'selesai']);

    $this->actingAs($user)
        ->get(route('guru.dashboard'))
        ->assertOk()
        ->assertViewHas('stats', fn (array $stats): bool => $stats['total'] === 1 && $stats['aktif'] === 1 && $stats['selesai'] === 0);
});