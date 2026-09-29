<?php

use App\Models\BankQuestion;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function setupExamForOptionImages(): array
{
    $user = User::factory()->create(['role' => 'guru']);
    $guru = Guru::create([
        'user_id' => $user->id,
        'nama' => $user->name,
        'kode_guru' => fake()->unique()->bothify('GURU-###'),
    ]);
    $subject = MataPelajaran::create(['kode' => fake()->unique()->bothify('MAP-###'), 'nama' => 'Biologi']);
    $class = Kelas::create(['nama_kelas' => fake()->unique()->bothify('XI-IPA-#'), 'tingkat' => '11', 'status' => 'aktif']);
    $exam = Exam::create([
        'guru_id' => $guru->id,
        'mata_pelajaran_id' => $subject->id,
        'nama' => 'Ujian Biologi Sel',
        'kelas_id' => $class->id,
        'kelas' => $class->nama_kelas,
        'ruangan' => 'Lab IPA',
        'kode_ujian' => fake()->unique()->bothify('EXAM-####'),
        'durasi_menit' => 60,
        'tanggal_mulai' => now()->subHour(),
        'tanggal_selesai' => now()->addHours(2),
        'status' => 'published',
        'komponen_soal' => ['pg'],
    ]);

    return [$user, $guru, $exam, $class];
}

test('guru can create a question with option images', function () {
    Storage::fake('public');
    [$user, , $exam] = setupExamForOptionImages();

    $response = $this->actingAs($user)->post(route('guru.soal.store', $exam), [
        'tipe' => 'pg',
        'pertanyaan' => 'Manakah organel sel yang berfungsi sebagai respirasi seluler?',
        'opsi_a' => 'Mitokondria',
        'opsi_a_image' => UploadedFile::fake()->image('mitokondria.png', 200, 200),
        'opsi_b' => 'Ribosom',
        'opsi_b_image' => UploadedFile::fake()->image('ribosom.jpg', 200, 200),
        'opsi_c' => 'Badan Golgi',
        'opsi_d' => 'Retikulum Endoplasma',
        'opsi_e' => 'Nukleus',
        'kunci' => 'A',
        'bobot' => 2,
    ]);

    $response->assertRedirect(route('guru.soal.index', $exam));

    $question = $exam->questions()->first();
    expect($question)->not->toBeNull()
        ->and($question->opsi_a_image)->not->toBeNull()
        ->and($question->opsi_b_image)->not->toBeNull()
        ->and($question->opsi_c_image)->toBeNull();

    Storage::disk('public')->assertExists($question->opsi_a_image);
    Storage::disk('public')->assertExists($question->opsi_b_image);
});

test('guru can update question to replace or remove an option image', function () {
    Storage::fake('public');
    [$user, , $exam] = setupExamForOptionImages();

    $fileA = UploadedFile::fake()->image('old_a.png')->store('questions/options', 'public');
    $fileB = UploadedFile::fake()->image('old_b.png')->store('questions/options', 'public');

    $question = $exam->questions()->create([
        'tipe' => 'pg',
        'pertanyaan' => 'Soal bergambar',
        'opsi_a' => 'Opsi A',
        'opsi_a_image' => $fileA,
        'opsi_b' => 'Opsi B',
        'opsi_b_image' => $fileB,
        'opsi_c' => 'Opsi C',
        'opsi_d' => 'Opsi D',
        'opsi_e' => 'Opsi E',
        'kunci' => 'A',
        'bobot' => 1,
        'urutan' => 1,
    ]);

    Storage::disk('public')->assertExists($fileA);
    Storage::disk('public')->assertExists($fileB);

    $newFileB = UploadedFile::fake()->image('new_b.png', 100, 100);

    $response = $this->actingAs($user)->put(route('guru.soal.update', [$exam, $question]), [
        'tipe' => 'pg',
        'pertanyaan' => 'Soal bergambar diupdate',
        'opsi_a' => 'Opsi A update',
        'remove_opsi_a_image' => '1',
        'opsi_b' => 'Opsi B update',
        'opsi_b_image' => $newFileB,
        'opsi_c' => 'Opsi C',
        'opsi_d' => 'Opsi D',
        'opsi_e' => 'Opsi E',
        'kunci' => 'A',
        'bobot' => 1,
    ]);

    $response->assertRedirect(route('guru.soal.index', $exam));

    $question->refresh();

    // Opsi A image was removed
    expect($question->opsi_a_image)->toBeNull();
    Storage::disk('public')->assertMissing($fileA);

    // Opsi B image was replaced
    expect($question->opsi_b_image)->not->toBe($fileB);
    Storage::disk('public')->assertMissing($fileB);
    Storage::disk('public')->assertExists($question->opsi_b_image);
});

test('duplicate question retains option images', function () {
    Storage::fake('public');
    [$user, , $exam] = setupExamForOptionImages();

    $fileA = UploadedFile::fake()->image('opsi_a.png')->store('questions/options', 'public');

    $question = $exam->questions()->create([
        'tipe' => 'pg',
        'pertanyaan' => 'Soal asli',
        'opsi_a' => 'Opsi A',
        'opsi_a_image' => $fileA,
        'opsi_b' => 'Opsi B',
        'opsi_c' => 'Opsi C',
        'opsi_d' => 'Opsi D',
        'opsi_e' => 'Opsi E',
        'kunci' => 'A',
        'bobot' => 1,
        'urutan' => 1,
    ]);

    $this->actingAs($user)->post(route('guru.soal.duplicate', [$exam, $question]))
        ->assertRedirect();

    expect($exam->questions()->count())->toBe(2);

    $duplicate = $exam->questions()->orderByDesc('id')->first();
    expect($duplicate->opsi_a_image)->toBe($fileA);
});

test('importing question from bank preserves option images', function () {
    Storage::fake('public');
    [$user, $guru, $exam] = setupExamForOptionImages();

    $bank = QuestionBank::create([
        'guru_id' => $guru->id,
        'nama' => 'Bank Soal Biologi',
        'mata_pelajaran_id' => $exam->mata_pelajaran_id,
        'kelas_id' => $exam->kelas_id,
    ]);

    $fileA = UploadedFile::fake()->image('bank_a.png')->store('questions/options', 'public');

    $bankQuestion = BankQuestion::create([
        'question_bank_id' => $bank->id,
        'tipe' => 'pg',
        'pertanyaan' => 'Soal dari bank',
        'opsi_a' => 'Opsi Bank A',
        'opsi_a_image' => $fileA,
        'opsi_b' => 'Opsi Bank B',
        'opsi_c' => 'Opsi Bank C',
        'opsi_d' => 'Opsi Bank D',
        'opsi_e' => 'Opsi Bank E',
        'kunci' => 'A',
        'bobot' => 2,
    ]);

    $this->actingAs($user)->post(route('guru.soal.import', [$exam, $bankQuestion]))
        ->assertRedirect();

    $imported = $exam->questions()->where('pertanyaan', 'Soal dari bank')->first();
    expect($imported)->not->toBeNull()
        ->and($imported->opsi_a_image)->toBe($fileA);
});

test('guru and student views properly render option images', function () {
    Storage::fake('public');
    [$user, , $exam, $class] = setupExamForOptionImages();

    $fileA = UploadedFile::fake()->image('opt_a.png')->store('questions/options', 'public');

    $question = $exam->questions()->create([
        'tipe' => 'pg',
        'pertanyaan' => 'Gambar apakah ini?',
        'opsi_a' => 'Kloroplas',
        'opsi_a_image' => $fileA,
        'opsi_b' => 'Mitokondria',
        'opsi_c' => 'Ribosom',
        'opsi_d' => 'Vakuola',
        'opsi_e' => 'Lisosom',
        'kunci' => 'A',
        'bobot' => 1,
        'urutan' => 1,
    ]);

    // Guru soal index view renders option image
    $guruResponse = $this->actingAs($user)->get(route('guru.soal.index', $exam));
    $guruResponse->assertOk()
        ->assertSee(Storage::url($fileA));

    // Siswa taking exam view renders option image and zoom action
    $siswaUser = User::factory()->create(['role' => 'siswa']);
    $siswa = Siswa::create([
        'user_id' => $siswaUser->id,
        'kelas_id' => $class->id,
        'kelas' => $class->nama_kelas,
        'nama' => 'Siswa Teladan',
        'nis' => '12345',
        'nisn' => fake()->unique()->numerify('00########'),
        'status_aktif' => true,
    ]);

    $exam->update([
        'token' => 'TOK123',
        'token_aktif' => true,
        'token_dibuat_at' => now(),
        'token_kedaluwarsa_at' => now()->addHour(),
        'status' => 'aktif',
    ]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'siswa_id' => $siswa->id,
        'started_at' => now()->subMinutes(5),
        'status' => 'in_progress',
        'token_used' => 'TOK123',
    ]);

    $siswaResponse = $this->actingAs($siswaUser)
        ->withSession(['siswa.exam_token_verified.' . $exam->id => $attempt->id])
        ->get(route('siswa.ujian.work', $exam));
    $siswaResponse->assertOk()
        ->assertSee(Storage::url($fileA))
        ->assertSee('zoomImage');
});

test('student answers for question with option images are saved and scored properly on server', function () {
    Storage::fake('public');
    [$user, , $exam, $class] = setupExamForOptionImages();

    $fileA = UploadedFile::fake()->image('opt_a.png')->store('questions/options', 'public');
    $fileB = UploadedFile::fake()->image('opt_b.png')->store('questions/options', 'public');

    $question = $exam->questions()->create([
        'tipe' => 'pg',
        'pertanyaan' => 'Pilihlah gambar yang sesuai:',
        'opsi_a' => 'Pilihan A',
        'opsi_a_image' => $fileA,
        'opsi_b' => 'Pilihan B',
        'opsi_b_image' => $fileB,
        'opsi_c' => 'Pilihan C',
        'opsi_d' => 'Pilihan D',
        'opsi_e' => 'Pilihan E',
        'kunci' => 'A',
        'bobot' => 5,
        'urutan' => 1,
    ]);

    $exam->update([
        'token' => 'TOK999',
        'token_aktif' => true,
        'token_dibuat_at' => now(),
        'token_kedaluwarsa_at' => now()->addHour(),
        'status' => 'aktif',
    ]);

    $siswaUser = User::factory()->create(['role' => 'siswa']);
    $siswa = Siswa::create([
        'user_id' => $siswaUser->id,
        'kelas_id' => $class->id,
        'kelas' => $class->nama_kelas,
        'nama' => 'Budi Santoso',
        'nis' => '67890',
        'nisn' => fake()->unique()->numerify('00########'),
        'status_aktif' => true,
    ]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'siswa_id' => $siswa->id,
        'started_at' => now()->subMinutes(5),
        'status' => 'in_progress',
        'token_used' => 'TOK999',
    ]);

    // 1. Auto-save endpoint test
    $sessionKey = 'siswa.exam_token_verified.' . $exam->id;
    $saveResponse = $this->actingAs($siswaUser)
        ->withSession([$sessionKey => $attempt->id])
        ->postJson(route('siswa.ujian.answers', $exam), [
            'answers' => [
                $question->id => 'A',
            ],
        ]);

    $saveResponse->assertOk()
        ->assertJson(['saved' => true]);

    $this->assertDatabaseHas('answers', [
        'exam_attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'jawaban' => 'A',
    ]);

    // 2. Final Submit endpoint test
    $submitResponse = $this->actingAs($siswaUser)
        ->withSession([$sessionKey => $attempt->id])
        ->post(route('siswa.ujian.submit', $exam), [
            'answers' => [
                $question->id => 'A',
            ],
        ]);

    $submitResponse->assertRedirect(route('siswa.ujian.result', $exam));

    $attempt->refresh();
    expect($attempt->status)->toBe('submitted')
        ->and((float) $attempt->nilai_akhir)->toBe(100.0);

    $this->assertDatabaseHas('answers', [
        'exam_attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'jawaban' => 'A',
        'is_correct' => true,
        'skor' => 5.0,
    ]);
});
