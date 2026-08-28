<?php

use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createMataPelajaranAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function mataPelajaranPayload(array $overrides = []): array
{
    return array_merge([
        'kode' => 'MAT001',
        'nama' => 'Matematika',
        'deskripsi' => 'Materi matematika dasar.',
    ], $overrides);
}

test('admin can manage mata pelajaran data', function () {
    $admin = createMataPelajaranAdmin();

    $this->actingAs($admin)
        ->get(route('mata-pelajaran.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('mata-pelajaran.store'), mataPelajaranPayload())
        ->assertRedirect(route('mata-pelajaran.index'));

    $mataPelajaran = MataPelajaran::firstOrFail();
    expect($mataPelajaran->kode)->toBe('MAT001');

    $this->actingAs($admin)
        ->put(route('mata-pelajaran.update', $mataPelajaran), mataPelajaranPayload([
            'kode' => 'MAT002',
            'nama' => 'Matematika Lanjutan',
            'deskripsi' => '',
        ]))
        ->assertRedirect(route('mata-pelajaran.index'));

    expect($mataPelajaran->fresh()->nama)->toBe('Matematika Lanjutan');

    $this->actingAs($admin)
        ->get(route('mata-pelajaran.show', $mataPelajaran))
        ->assertOk()
        ->assertSee('Matematika Lanjutan');

    $this->actingAs($admin)
        ->delete(route('mata-pelajaran.destroy', $mataPelajaran))
        ->assertRedirect(route('mata-pelajaran.index'));

    expect(MataPelajaran::find($mataPelajaran->id))->toBeNull();
});

test('only admins can access mata pelajaran management', function () {
    $user = User::factory()->create(['role' => 'guru']);

    $this->actingAs($user)
        ->get(route('mata-pelajaran.index'))
        ->assertForbidden();
});

test('mata pelajaran creation validates unique kode', function () {
    $admin = createMataPelajaranAdmin();
    $this->actingAs($admin)->post(route('mata-pelajaran.store'), mataPelajaranPayload());

    $this->actingAs($admin)
        ->from(route('mata-pelajaran.create'))
        ->post(route('mata-pelajaran.store'), mataPelajaranPayload())
        ->assertSessionHasErrors(['kode']);
});
