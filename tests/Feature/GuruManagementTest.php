<?php

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createGuruAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function guruPayload(array $overrides = []): array
{
    return array_merge([
        'nip' => '198001012026011001',
        'kode_guru' => 'GR001',
        'nama' => 'Guru Uji',
        'email' => 'guru@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ], $overrides);
}

test('admin can manage guru data and delete the linked user', function () {
    $admin = createGuruAdmin();

    $this->actingAs($admin)
        ->get(route('guru.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('guru.store'), guruPayload())
        ->assertRedirect(route('guru.index'));

    $guru = Guru::with('user')->firstOrFail();
    expect($guru->user->role)->toBe('guru')
        ->and(Hash::check('Password123!', $guru->user->password))->toBeTrue();

    $this->actingAs($admin)
        ->put(route('guru.update', $guru), guruPayload([
            'nip' => '198001012026011002',
            'kode_guru' => 'GR002',
            'nama' => 'Guru Diperbarui',
            'email' => 'updated-guru@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertRedirect(route('guru.index'));

    expect($guru->fresh()->nama)->toBe('Guru Diperbarui');

    $userId = $guru->user_id;
    $this->actingAs($admin)
        ->delete(route('guru.destroy', $guru))
        ->assertRedirect(route('guru.index'));

    expect(Guru::find($guru->id))->toBeNull()
        ->and(User::find($userId))->toBeNull();
});

test('only admins can access guru management', function () {
    $user = User::factory()->create(['role' => 'siswa']);

    $this->actingAs($user)
        ->get(route('guru.index'))
        ->assertForbidden();
});

test('guru creation validates unique kode guru and email', function () {
    $admin = createGuruAdmin();
    $this->actingAs($admin)->post(route('guru.store'), guruPayload());

    $this->actingAs($admin)
        ->from(route('guru.create'))
        ->post(route('guru.store'), guruPayload([
            'nip' => '198001012026011002',
        ]))
        ->assertSessionHasErrors(['kode_guru', 'email']);
});
