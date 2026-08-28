<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(): View
    {
        $gurus = Guru::with('user')->orderBy('nama')->paginate(10);

        return view('guru.index', compact('gurus'));
    }

    public function create(): View
    {
        return view('guru.create');
    }

    public function store(StoreGuruRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $user = User::create([
                'name' => $request->string('nama')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => Hash::make($request->string('password')->toString()),
                'role' => 'guru',
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => $request->input('nip') ?: null,
                'nama' => $request->string('nama')->toString(),
                'kode_guru' => $request->string('kode_guru')->toString(),
            ]);
        });

        return to_route('guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): View
    {
        $guru->load('user');

        return view('guru.show', compact('guru'));
    }

    public function edit(Guru $guru): View
    {
        $guru->load('user');

        return view('guru.edit', compact('guru'));
    }

    public function update(UpdateGuruRequest $request, Guru $guru): RedirectResponse
    {
        DB::transaction(function () use ($request, $guru): void {
            $guru->update([
                'nip' => $request->input('nip') ?: null,
                'nama' => $request->string('nama')->toString(),
                'kode_guru' => $request->string('kode_guru')->toString(),
            ]);

            $userData = [
                'name' => $request->string('nama')->toString(),
                'email' => $request->string('email')->toString(),
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->string('password')->toString());
            }

            $guru->user->update($userData);
        });

        return to_route('guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        DB::transaction(function () use ($guru): void {
            $guru->user()->delete();
        });

        return to_route('guru.index')->with('success', 'Guru berhasil dihapus.');
    }
}
