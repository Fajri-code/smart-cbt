<?php

use App\Models\Exam;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Question;
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

test('guru bank list includes questions already created for their exams', function () {
    [$user, , $exam] = makeGuruWithExam();

    $exam->questions()->create([
        'tipe' => 'pg',
        'pertanyaan' => 'Berapakah 2 + 2?',
        'opsi_a' => '3',
        'opsi_b' => '4',
        'opsi_c' => '5',
        'opsi_d' => '6',
        'opsi_e' => '7',
        'kunci' => 'B',
        'bobot' => 1,
        'urutan' => 1,
    ]);

    $response = $this->actingAs($user)->get(route('guru.bank.index'));
    $bank = $user->fresh()->guru->questionBanks()->first();

    expect($bank)->not->toBeNull()
        ->and($bank->questions()->count())->toBe(1);

    $response->assertOk()->assertSee($exam->nama);

    $this->actingAs($user)
        ->get(route('guru.bank.show', $bank))
        ->assertOk()
        ->assertSee('Berapakah 2 + 2?');
});