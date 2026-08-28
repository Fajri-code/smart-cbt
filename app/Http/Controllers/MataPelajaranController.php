<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMataPelajaranRequest;
use App\Http\Requests\UpdateMataPelajaranRequest;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    public function index(): View
    {
        $mataPelajarans = MataPelajaran::orderBy('nama')->paginate(10);

        return view('mata-pelajaran.index', compact('mataPelajarans'));
    }

    public function create(): View
    {
        return view('mata-pelajaran.create');
    }

    public function store(StoreMataPelajaranRequest $request): RedirectResponse
    {
        MataPelajaran::create($request->validated());

        return to_route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(MataPelajaran $mataPelajaran): View
    {
        return view('mata-pelajaran.show', compact('mataPelajaran'));
    }

    public function edit(MataPelajaran $mataPelajaran): View
    {
        return view('mata-pelajaran.edit', compact('mataPelajaran'));
    }

    public function update(UpdateMataPelajaranRequest $request, MataPelajaran $mataPelajaran): RedirectResponse
    {
        $mataPelajaran->update($request->validated());

        return to_route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran): RedirectResponse
    {
        $mataPelajaran->delete();

        return to_route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
