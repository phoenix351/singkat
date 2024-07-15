<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCapaianRequest;
use App\Http\Requests\UpdateCapaianRequest;
use App\Models\AngkaKreditHistory;
use App\Models\Capaian;
use App\Models\Pegawai;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Throwable;

class CapaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $preparedSearch = "%$search%";
        $capaian = Capaian::join('pegawai', 'pegawai.id', 'capaians.pegawai_id')
            ->join('jabatan', 'jabatan.id', 'pegawai.jabatan_id')
            ->join('predikats', 'capaians.predikat_id', 'predikats.id')
            ->where(DB::raw('CONCAT(capaians.periode, " ", capaians.tahun)'), 'like', $preparedSearch)
            // ->where('capaians.periode','like',$preparedSearch)
            // ->orWhere('capaians.tahun','like',$preparedSearch)
            ->select(
                'pegawai.nama',
                'pegawai.nip',
                'jabatan.nama as jabatan',
                'capaians.*',
                'predikats.nama as nama_predikat',
            )
            ->orderBy('tahun')
            ->orderBy('periode')
            ->get();


        return Inertia::render('Capaian/index', ['capaian' => $capaian, 'search' => $search]);
    }

    /**mys
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
            $capaian->save();
            // $pegawai = Pegawai::find($validatedData['pegawai_id']);
            // $current_ak = Pegawai::find($validatedData['pegawai_id'])->value('akumulasi_ak');
            // $addition_ak = $this->calculate_addition_ak($capaian);
            // $akumulasi_ak = (float) $pegawai->akumulasi_ak + (float) $addition_ak;
            // return response()->json($, 201);
            // $capaian->update(['angka_kredit' => $addition_ak]);

            // $history = [
            //     'pegawai_id' => $validatedData['pegawai_id'],
            //     'akumulasi_ak' => $akumulasi_ak,
            //     'capaian_id' => $capaian->id
            // ];

            // $ak_history = AngkaKreditHistory::create($history);
            // $updatePegawai = $pegawai->update(['akumulasi_ak' => $akumulasi_ak]);
            DB::commit();
            return response()->json($capaian, 201);
        } catch (Throwable $th) {
            // } catch (QueryException $exp) {
            DB::rollBack();
            throw $th;
            return response()->json(['error' => 'Kesalahan saat menyimpan, periksa kembali isian !'], 422);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response()->json(['error' => 'Kesalahan server ! silahkan hubungi admin'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Capaian $capaian)
    {
        $pegawai = Pegawai::join('jabatan', 'jabatan.id', 'pegawai.jabatan_id')->where('pegawai.id', $capaian->pegawai_id)->select('pegawai.*', 'jabatan.nama as jabatan')->first();

        $capaian = $capaian::where('id', $capaian->id)->with('predikat')->first();
        return inertia('Capaian/Detail', [
            'pegawai' => $pegawai,
            'capaian' => $capaian,

        ]);
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
        try {
            //code...
            DB::beginTransaction();
            $validatedData = $request->validated();
            $capaian->update($validatedData);
            $capaian->save();
            DB::commit();
            return response()->json(['message' => 'data berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Capaian $capaian)
    {
        try {
            //code...
            DB::beginTransaction();
            $capaian->delete();
            DB::commit();
            return response()->json(['message' => 'capaian berhasil dihapus'], 204);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    private function calculate_addition_ak(Capaian $capaian)
    {
        $data = Capaian::join('predikats', 'predikats.id', 'capaians.predikat')
            ->join('pegawai', 'pegawai.id', 'capaians.pegawai_id')
            ->join('jabatan', 'jabatan.id', 'pegawai.jabatan_id')
            ->where('capaians.id', $capaian->id)->select('jabatan.angka_kredit', 'predikats.nilai')->first();
        $addition_ak = $data['nilai'] * $data['angka_kredit'];
        if (str_contains($capaian->periode, 'Semester')) {
            $addition_ak = $addition_ak / 2;
        }
        return $addition_ak;
    }
}
