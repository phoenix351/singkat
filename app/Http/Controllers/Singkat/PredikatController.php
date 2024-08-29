<?php

namespace App\Http\Controllers\Singkat;

use App\Http\Requests\StorePredikatRequest;
use App\Http\Requests\UpdatePredikatRequest;
use App\Models\Predikat;

class PredikatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StorePredikatRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Predikat $predikat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Predikat $predikat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePredikatRequest $request, Predikat $predikat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Predikat $predikat)
    {
        //
    }
    public function fetch()
    {
        $predikats = Predikat::get();
        return response()->json($predikats);
    }
}
