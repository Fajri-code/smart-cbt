<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelass = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->paginate(10);

        return view('kelas.index', compact('kelass'));
    }

    public function create(): View
    {
        return view('kelas.create');
    }

    public function store(StoreKelasRequest $request): RedirectResponse
    {
        Kelas::create($request->validated());

        return to_route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas): View
    {
        return view('kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas): View
    {
        return view('kelas.edit', compact('kelas'));
    }

    public function update(UpdateKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return to_route('kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $kelas->delete();

        return to_route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
