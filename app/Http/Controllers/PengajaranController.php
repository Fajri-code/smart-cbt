<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajaranRequest;
use App\Http\Requests\UpdatePengajaranRequest;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pengajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengajaranController extends Controller
{
    public function index(): View
    {
        $pengajarans = Pengajaran::with(['guru', 'mataPelajaran', 'kelas'])->latest()->paginate(10);

        return view('pengajaran.index', compact('pengajarans'));
    }

    public function create(): View
    {
        return view('pengajaran.create', [
            'gurus' => Guru::with('user')->orderBy('nama')->get(),
            'mataPelajarans' => MataPelajaran::orderBy('nama')->get(),
            'kelass' => Kelas::where('status', 'aktif')->orderBy('tingkat')->orderBy('nama_kelas')->get(),
        ]);
    }

    public function store(StorePengajaranRequest $request): RedirectResponse
    {
        Pengajaran::create($request->validated());

        return to_route('pengajaran.index')->with('success', 'Pengajaran berhasil ditambahkan.');
    }

    public function show(Pengajaran $pengajaran): View
    {
        $pengajaran->load(['guru', 'mataPelajaran', 'kelas']);

        return view('pengajaran.show', compact('pengajaran'));
    }

    public function edit(Pengajaran $pengajaran): View
    {
        $pengajaran->load(['guru', 'mataPelajaran', 'kelas']);

        return view('pengajaran.edit', [
            'pengajaran' => $pengajaran,
            'gurus' => Guru::with('user')->orderBy('nama')->get(),
            'mataPelajarans' => MataPelajaran::orderBy('nama')->get(),
            'kelass' => Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(),
        ]);
    }

    public function update(UpdatePengajaranRequest $request, Pengajaran $pengajaran): RedirectResponse
    {
        $pengajaran->update($request->validated());

        return to_route('pengajaran.index')->with('success', 'Data pengajaran berhasil diperbarui.');
    }

    public function destroy(Pengajaran $pengajaran): RedirectResponse
    {
        $pengajaran->delete();

        return to_route('pengajaran.index')->with('success', 'Pengajaran berhasil dihapus.');
    }
}
