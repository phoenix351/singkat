<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkRequest;
use App\Http\Requests\UpdateSkRequest;
use App\Models\JenisSK;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SKController extends Controller
{
    public function index()
    {
        return Inertia::render('Singkat/KelolaJenisSK/index');
    }
    public function fetchAll()
    {
        try {
            $daftar_jenis = JenisSK::all();
            return response()->json($daftar_jenis, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'server tidak dapat merespon permintaan anda'], 200);
        }
    }
    public function fetchOne(JenisSK $sk)
    {
        try {

            return response()->json($sk, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'server tidak dapat merespon permintaan anda'], 200);
        }
    }
    public function store(StoreSkRequest $request)
    {
        $validatedSk = $request->validated();
        $newId = JenisSK::orderBy('id', 'DESC')->first()->id + 1;

        try {
            //code...
            JenisSK::create([
                'id' => isset($validatedSk['new_id']) ? $validatedSk['new_id'] : $newId,
                'nama' => $validatedSk['nama']
            ]);
            return response()->json(['message' => 'Succesfully saved the data']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to save the data']);
            //throw $th;
        }
    }
    public function update(UpdateSkRequest $request)
    {
        $validatedSk = $request->validated();
        try {
            //code...
            $jenis_sk = JenisSK::find($validatedSk['id']);
            $jenis_sk->update([
                'nama' => $validatedSk['nama'],
                'id' => isset($validatedSk['new_id']) ? $validatedSk['new_id'] : $validatedSk['id'],
            ]);
            $jenis_sk->save();
            return response()->json(['message' => 'Berhasil menyimpan perubahan']);
        } catch (\Throwable $ex) {
            if ($ex->getCode() == "23000") {
                return response()->json(['error' => 'ID sudah terpakai!']);
            }
            throw $ex;
            return response()->json(['error' => 'Gagal mengupdate data']);
        }
    }
    public function destroy(JenisSK $sk)
    {
        try {
            //code...
            $sk->delete();
            return response()->json(['message' => 'Succesfully deleted data']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to delete the changes']);
            //throw $th;
        }
    }
}
