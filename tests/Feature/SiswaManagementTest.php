<?php

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function siswaPayload(array $overrides = []): array
{
    return array_merge([
        'nis' => '2026001',
        'nama' => 'Siswa Uji',
        'kelas' => 'X IPA 1',
        'email' => 'siswa@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ], $overrides);
}

test('admin can manage siswa data and delete the linked user', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->get(route('siswa.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('siswa.store'), siswaPayload())
        ->assertRedirect(route('siswa.index'));

    $siswa = Siswa::with('user')->firstOrFail();
    expect($siswa->user->role)->toBe('siswa')
        ->and(Hash::check('Password123!', $siswa->user->password))->toBeTrue();

    $this->actingAs($admin)
        ->put(route('siswa.update', $siswa), siswaPayload([
            'nis' => '2026002',
            'nama' => 'Siswa Diperbarui',
            'email' => 'updated@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertRedirect(route('siswa.index'));

    expect($siswa->fresh()->nama)->toBe('Siswa Diperbarui');

    $userId = $siswa->user_id;
    $this->actingAs($admin)
        ->delete(route('siswa.destroy', $siswa))
        ->assertRedirect(route('siswa.index'));

    expect(Siswa::find($siswa->id))->toBeNull()
        ->and(User::find($userId))->toBeNull();
});

test('only admins can access siswa management', function () {
    $user = User::factory()->create(['role' => 'guru']);

    $this->actingAs($user)
        ->get(route('siswa.index'))
        ->assertForbidden();
});

test('siswa creation validates unique nis and email', function () {
    $admin = createAdmin();
    $this->actingAs($admin)->post(route('siswa.store'), siswaPayload());

    $this->actingAs($admin)
        ->from(route('siswa.create'))
        ->post(route('siswa.store'), siswaPayload())
        ->assertSessionHasErrors(['nis', 'email']);
});