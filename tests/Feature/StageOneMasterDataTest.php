<?php

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pengajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function stageOneAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function stageOneGuru(): Guru
{
    return Guru::create([
        'nip' => '198001012026011001',
        'nama' => 'Guru Stage One',
        'kode_guru' => 'STG001',
        'user_id' => User::factory()->create(['role' => 'guru'])->id,
    ]);
}

test('admin can manage kelas data', function () {
    $admin = stageOneAdmin();

    $this->actingAs($admin)
        ->post(route('kelas.store'), [
            'nama_kelas' => 'X-A',
            'tingkat' => 'X',
            'status' => 'aktif',
        ])
        ->assertRedirect(route('kelas.index'));

    $kelas = Kelas::firstOrFail();

    $this->actingAs($admin)
        ->put(route('kelas.update', $kelas), [
            'nama_kelas' => 'X-B',
            'tingkat' => 'X',
            'status' => 'nonaktif',
        ])
        ->assertRedirect(route('kelas.index'));

    expect($kelas->fresh()->nama_kelas)->toBe('X-B');

    $this->actingAs($admin)->delete(route('kelas.destroy', $kelas))->assertRedirect(route('kelas.index'));
    expect(Kelas::find($kelas->id))->toBeNull();
});

test('admin can manage pengajaran and duplicate combinations are rejected', function () {
    $admin = stageOneAdmin();
    $guru = stageOneGuru();
    $mataPelajaran = MataPelajaran::create(['kode' => 'MAT001', 'nama' => 'Matematika']);
    $kelas = Kelas::create(['nama_kelas' => 'X-A', 'tingkat' => 'X', 'status' => 'aktif']);
    $payload = [
        'guru_id' => $guru->id,
        'mata_pelajaran_id' => $mataPelajaran->id,
        'kelas_id' => $kelas->id,
        'status' => 'aktif',
    ];

    $this->actingAs($admin)->post(route('pengajaran.store'), $payload)->assertRedirect(route('pengajaran.index'));
    $pengajaran = Pengajaran::firstOrFail();

    $this->actingAs($admin)
        ->from(route('pengajaran.create'))
        ->post(route('pengajaran.store'), $payload)
        ->assertSessionHasErrors('guru_id');

    $this->actingAs($admin)->get(route('pengajaran.show', $pengajaran))->assertOk();
    $this->actingAs($admin)->delete(route('pengajaran.destroy', $pengajaran))->assertRedirect(route('pengajaran.index'));
    expect(Pengajaran::find($pengajaran->id))->toBeNull();
});

test('only admins can access stage one admin management', function () {
    $user = User::factory()->create(['role' => 'guru']);

    $this->actingAs($user)->get(route('kelas.index'))->assertForbidden();
    $this->actingAs($user)->get(route('pengajaran.index'))->assertForbidden();
});
