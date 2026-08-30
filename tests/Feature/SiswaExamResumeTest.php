<?php

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Question;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('siswa dapat melanjutkan ujian yang sama setelah keluar dan token berubah', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Guru::create([
        'user_id' => $teacherUser->id,
        'nama' => $teacherUser->name,
        'kode_guru' => 'GURU-001',
    ]);

    $class = Kelas::create([
        'nama_kelas' => 'VII-A',
        'tingkat' => 'VII',
        'status' => 'aktif',
    ]);

    $subject = MataPelajaran::create([
        'kode' => 'IPA-001',
        'nama' => 'IPA',
    ]);

    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Siswa::create([
        'user_id' => $studentUser->id,
        'nis' => 'NIS-001',
        'nama' => $studentUser->name,
        'kelas' => $class->nama_kelas,
        'nisn' => '1234567890',
        'kelas_id' => $class->id,
        'status_aktif' => true,
    ]);

    $exam = Exam::create([
        'guru_id' => $teacher->id,
        'mata_pelajaran_id' => $subject->id,
        'nama' => 'Ujian IPA',
        'kelas_id' => $class->id,
        'kelas' => $class->nama_kelas,
        'ruangan' => 'Lab 1',
        'kode_ujian' => 'EXAM-1001',
        'token' => 'OLD123',
        'token_aktif' => true,
        'token_dibuat_at' => now(),
        'token_kedaluwarsa_at' => now()->addMinutes(10),
        'durasi_menit' => 30,
        'tanggal_mulai' => now()->subMinute(),
        'tanggal_selesai' => now()->addHour(),
        'status' => 'aktif',
        'komponen_soal' => ['pg'],
    ]);

    Question::create([
        'exam_id' => $exam->id,
        'urutan' => 1,
        'tipe' => 'pg',
        'pertanyaan' => 'Siapakah ibukota Indonesia?',
        'opsi_a' => 'Jakarta',
        'opsi_b' => 'Bandung',
        'opsi_c' => 'Surabaya',
        'opsi_d' => 'Medan',
        'opsi_e' => 'Bali',
        'kunci' => 'A',
        'bobot' => 1,
    ]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'siswa_id' => $student->id,
        'started_at' => now()->subMinutes(5),
        'status' => 'in_progress',
        'token_used' => 'OLD123',
    ]);

    $this->actingAs($studentUser)
        ->withSession(['siswa.exam_token_verified.' . $exam->id => $attempt->id])
        ->post(route('siswa.ujian.leave', $exam))
        ->assertOk();

    $exam->update([
        'token' => 'NEW456',
        'token_aktif' => true,
        'token_dibuat_at' => now(),
        'token_kedaluwarsa_at' => now()->addMinutes(10),
    ]);

    $response = $this->actingAs($studentUser)
        ->withSession([])
        ->post(route('siswa.ujian.start', $exam), ['token' => 'NEW456']);

    $response->assertRedirect(route('siswa.ujian.work', $exam));
    $this->assertDatabaseCount('exam_attempts', 1);
    $this->assertDatabaseHas('exam_attempts', [
        'id' => $attempt->id,
        'exam_id' => $exam->id,
        'siswa_id' => $student->id,
        'status' => 'in_progress',
        'token_used' => 'NEW456',
    ]);
});

test('siswa tidak bisa pakai token yang sama setelah keluar', function () {
    $teacherUser = User::factory()->create(['role' => 'guru']);
    $teacher = Guru::create([
        'user_id' => $teacherUser->id,
        'nama' => $teacherUser->name,
        'kode_guru' => 'GURU-001',
    ]);

    $class = Kelas::create([
        'nama_kelas' => 'VII-A',
        'tingkat' => 'VII',
        'status' => 'aktif',
    ]);

    $subject = MataPelajaran::create([
        'kode' => 'IPA-001',
        'nama' => 'IPA',
    ]);

    $studentUser = User::factory()->create(['role' => 'siswa']);
    $student = Siswa::create([
        'user_id' => $studentUser->id,
        'nis' => 'NIS-002',
        'nama' => $studentUser->name,
        'kelas' => $class->nama_kelas,
        'nisn' => '1234567891',
        'kelas_id' => $class->id,
        'status_aktif' => true,
    ]);

    $exam = Exam::create([
        'guru_id' => $teacher->id,
        'mata_pelajaran_id' => $subject->id,
        'nama' => 'Ujian IPA',
        'kelas_id' => $class->id,
        'kelas' => $class->nama_kelas,
        'ruangan' => 'Lab 1',
        'kode_ujian' => 'EXAM-1002',
        'token' => 'SAME123',
        'token_aktif' => true,
        'token_dibuat_at' => now(),
        'token_kedaluwarsa_at' => now()->addMinutes(10),
        'durasi_menit' => 30,
        'tanggal_mulai' => now()->subMinute(),
        'tanggal_selesai' => now()->addHour(),
        'status' => 'aktif',
        'komponen_soal' => ['pg'],
    ]);

    Question::create([
        'exam_id' => $exam->id,
        'urutan' => 1,
        'tipe' => 'pg',
        'pertanyaan' => 'Siapakah ibukota Indonesia?',
        'opsi_a' => 'Jakarta',
        'opsi_b' => 'Bandung',
        'opsi_c' => 'Surabaya',
        'opsi_d' => 'Medan',
        'opsi_e' => 'Bali',
        'kunci' => 'A',
        'bobot' => 1,
    ]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'siswa_id' => $student->id,
        'started_at' => now()->subMinutes(5),
        'status' => 'in_progress',
        'token_used' => 'SAME123',
    ]);

    $this->actingAs($studentUser)
        ->withSession(['siswa.exam_token_verified.' . $exam->id => $attempt->id])
        ->post(route('siswa.ujian.leave', $exam))
        ->assertOk();

    $response = $this->actingAs($studentUser)
        ->withSession([])
        ->post(route('siswa.ujian.start', $exam), ['token' => 'SAME123']);

    $response->assertRedirect();
    $response->assertSessionHasErrors('token');
});
