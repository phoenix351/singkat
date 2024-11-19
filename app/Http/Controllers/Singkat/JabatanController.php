<?php

namespace App\Http\Controllers\Singkat;

use App\Http\Requests\UpdateJabatanRequest;
use Inertia\Inertia;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $jabatan = Jabatan::query()
            ->where('nama', 'like', "%{$search}%")
            ->paginate(20)
            ->appends(['search' => $search]);

        return Inertia::render('Jabatan/KelolaJabatan', [
            'jabatan' => $jabatan,
            'search' => $search,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_urut_kepka'=>'required|integer',
            'nama' => 'required|string|max:255',
            'angka_kredit' => 'required|string|max:255',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil ditambahkan');
    }



    public function update(UpdateJabatanRequest $request, Jabatan $jabatan)
    {


        $newJabatan = $request->validated();

        $updatedJabatan = $jabatan->update($newJabatan);

        return response()->json($jabatan, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil dihapus');
    }
    public function fetch(Jabatan $jabatan)
    {
        if($jabatan)
        {
            // return $jabatan
            return response()->json($jabatan);
        }
        $jabatans = Jabatan::get();
        return response()->json($jabatans);
    }
}
