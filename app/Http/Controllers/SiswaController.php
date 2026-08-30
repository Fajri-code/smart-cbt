<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $kelasOptions = Kelas::where('status', 'aktif')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasCounts = Siswa::query()->select('kelas_id')->selectRaw('count(*) as total')->groupBy('kelas_id')->pluck('total', 'kelas_id');
        $query = Siswa::with(['user', 'kelasData'])->orderBy('nama');

        if ($request->filled('kelas')) {
            $query->where('kelas_id', $request->integer('kelas'));
        }

        $siswas = $query->paginate(10)->withQueryString();

        return view('siswa.index', compact('siswas', 'kelasOptions', 'kelasCounts'));
    }

    public function create(): View
    {
        return view('siswa.create', ['kelass' => Kelas::where('status', 'aktif')->orderBy('tingkat')->get()]);
    }

    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $user = User::create([
                'name' => $request->string('nama')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => Hash::make($request->string('password')->toString()),
                'role' => 'siswa',
            ]);

            $kelas = $request->filled('kelas_id') ? Kelas::findOrFail($request->integer('kelas_id')) : null;

            Siswa::create([
                'user_id' => $user->id,
                'nis' => $request->string('nis')->toString() ?: $request->string('nisn')->toString(),
                'nisn' => $request->string('nisn')->toString() ?: $request->string('nis')->toString(),
                'nama' => $request->string('nama')->toString(),
                'kelas' => $kelas?->nama_kelas,
                'kelas_id' => $kelas?->id,
                'program_tahasus' => $request->boolean('program_tahasus'),
                'tahun_ajaran' => $request->string('tahun_ajaran')->toString() ?: '2026/2027',
                'status_aktif' => $request->boolean('status_aktif', true),
            ]);
        });

        return to_route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load(['user', 'kelasData']);

        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa): View
    {
        $siswa->load('user');

        return view('siswa.edit', ['siswa' => $siswa, 'kelass' => Kelas::where('status', 'aktif')->orderBy('tingkat')->get()]);
    }

    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        DB::transaction(function () use ($request, $siswa): void {
            $kelas = $request->filled('kelas_id') ? Kelas::findOrFail($request->integer('kelas_id')) : null;
            $siswa->update(array_merge($request->only(['nis', 'nama', 'program_tahasus', 'tahun_ajaran', 'status_aktif']), [
                'nis' => $request->string('nis')->toString() ?: $request->string('nisn')->toString(),
                'nisn' => $request->string('nisn')->toString() ?: $request->string('nis')->toString(),
                'kelas' => $kelas?->nama_kelas,
                'kelas_id' => $kelas?->id,
            ]));

            $userData = [
                'name' => $request->string('nama')->toString(),
                'email' => $request->string('email')->toString(),
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->string('password')->toString());
            }

            $siswa->user->update($userData);
        });

        return to_route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        DB::transaction(function () use ($siswa): void {
            $siswa->user()->delete();
        });

        return to_route('siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }
}
