<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $unitKerja = UnitKerja::query()
            ->where('nama', 'like', "%{$search}%")
            ->paginate(20)
            ->appends(['search' => $search]);

        return Inertia::render('UnitKerja/KelolaUnitKerja', [
            'unitKerja' => $unitKerja,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        UnitKerja::create($request->all());

        return redirect()->route('unit-kerja.index')->with('success', 'Unit Kerja berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnitKerja $unitKerja)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $unitKerja->update($request->all());

        return redirect()->route('unit-kerja.index')->with('success', 'Unit Kerja berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitKerja $unitKerja)
    {
        $unitKerja->delete();

        return redirect()->route('unit-kerja.index')->with('success', 'Unit Kerja berhasil dihapus');
    }
}
