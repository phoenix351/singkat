<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCapaianRequest;
use App\Http\Requests\UpdateCapaianRequest;
use App\Models\Capaian;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CapaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $search = $request->input('search');

        $capaian = Capaian::join('pegawai', 'pegawai.id', 'capaians.pegawai_id')
            ->join('jabatan', 'jabatan.id', 'pegawai.jabatan_id')
            ->join('predikats', 'capaians.predikat', 'predikats.id')
            ->select(
                'pegawai.nama',
                'pegawai.nip',
                'jabatan.nama as jabatan',
                'capaians.*',
                'predikats.nama as nama_predikat',
                'predikats.nilai as nilai_predikat',
                'jabatan.angka_kredit'
            )
            ->get();


        // return Inertia::render('PAK/KelolaPak', [
        //     'pegawai' => $pegawai,
        //     'search' => $search,
        //     "jabatan" => Jabatan::all(),
        //     "unitKerja" => UnitKerja::all(),
        // ]);
        return Inertia::render('Capaian/index', ['capaian' => $capaian]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCapaianRequest $request)
    {
        $validatedData = $request->validated();
        try {
            //code...
            DB::beginTransaction();
            $capaian = Capaian::create($validatedData);
            DB::commit();
            return response()->json($capaian, 201);
        } catch (\Throwable $th) {
            // DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Capaian $capaian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Capaian $capaian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCapaianRequest $request, Capaian $capaian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Capaian $capaian)
    {
        //
    }
}
