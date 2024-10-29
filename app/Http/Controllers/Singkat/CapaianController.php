<?php

namespace App\Http\Controllers\Singkat;

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
            ->orWhere('pegawai.nama', 'like', $preparedSearch)
            ->orWhere('pegawai.nip', 'like', $preparedSearch)
            ->orWhere('jabatan.nama', 'like', $preparedSearch)
            ->orWhere('predikats.nama', 'like', $preparedSearch)
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
            ->paginate(10);


        return Inertia::render('Singkat/Capaian/index', ['capaian' => $capaian, 'search' => $search]);
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

            $duplikat_tahun = Capaian::where('pegawai_id', $validatedData["pegawai_id"])
                ->where('periode','Tahunan')
                ->where('tahun', $validatedData["tahun"])->first();
            if ($duplikat_tahun) {

                return response()->json(['message' => 'Capaian dengan tahun yang sama sudah ada'], 422);
            }
            if(str_contains($validatedData['periode'],"Tahunan")){
                // any in year
                $duplikat = Capaian::where('pegawai_id',$validatedData['pegawai_id'])
                ->where('tahun',$validatedData['tahun'])
                ->first();

                if($duplikat)
                {
                    return response()->json(['message'=>"Capaian dengan tahun yang sama sudah ada"], 422);
                }

            }
            // cek untuk data semester
            if (str_contains($validatedData["periode"], "Semester")) {
                $duplikat = Capaian::where('pegawai_id', $validatedData["pegawai_id"])
                    ->where('tahun', $validatedData["tahun"])
                    ->where('periode', $validatedData["periode"])
                    ->first();
                if ($duplikat) {

                    return response()->json(['message' => 'Capaian dengan tahun dan semester yang sama sudah ada'], 422);
                }
            }
            // cek duplikasi untuk bulanan
            if (str_contains($validatedData["periode"], "Semester 1")) {
                $duplikat = Capaian::where('pegawai_id', $validatedData["pegawai_id"])
                    ->where('tahun', $validatedData["tahun"])
                    ->where('periode', "Bulanan")
                    ->where('bulan','<=',6)
                    ->first();
                if ($duplikat) {

                    return response()->json(['message' => 'Capaian Bulanan untuk '.$validatedData["periode"].' sudah ada'], 422);
                }
            }
            if (str_contains($validatedData["periode"], "Semester 2")) {
                $duplikat = Capaian::where('pegawai_id', $validatedData["pegawai_id"])
                    ->where('tahun', $validatedData["tahun"])
                    ->where('periode', "Bulanan")
                    ->where('bulan','>',6)
                    ->first();
                if ($duplikat) {

                    return response()->json(['message' => 'Capaian Bulanan untuk '.$validatedData["periode"].' sudah ada'], 422);
                }
            }
            // cek untuk data bulanan
            if ($validatedData["periode"] == "Bulanan") {
                if (is_null($validatedData["bulan"])) {
                    return response()->json(['message' => 'Isian Bulan Kosong'], 422);
                }
                // duplikat tahun
                $duplikat = Capaian::where('pegawai_id', $validatedData["pegawai_id"])
                    ->where('periode','Tahunan')
                    ->where('tahun', $validatedData["tahun"])
                    ->first();
                if ($duplikat) {

                    return response()->json(['message' => 'Capaian Tahunan sudah ada, tidak bisa menambahkan Capaian Bulanan'], 422);
                }
                // duplikat semester
                if ($validatedData["bulan"] > 6) {
                    $duplikat = Capaian::where('pegawai_id', $validatedData["pegawai_id"])
                        ->where('tahun', $validatedData["tahun"])
                        ->where('periode', "Semester 2")
                        ->first();
                    if ($duplikat) {
                        return response()->json(['message' => 'Capaian Semester 2 sudah ada, tidak bisa menambahkan Capaian Bulanan' . $validatedData["bulan"]], 422);
                    }
                }
                if ($validatedData["bulan"] <= 6) {
                    $duplikat = Capaian::where('pegawai_id', $validatedData["pegawai_id"])
                        ->where('tahun', $validatedData["tahun"])
                        ->where('periode', "Semester 1")
                        ->first();
                    if ($duplikat) {
                        return response()->json(['message' => 'Capaian Semester 1 sudah ada, tidak bisa menambahkan Capaian Bulanan' . $validatedData["bulan"]], 422);
                    }
                }
            }
            DB::beginTransaction();
            $capaian = Capaian::create($validatedData);
            $capaian->save();

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
            // cek untuk data tahunan
            
            $capaian->update($validatedData);
            $capaian->save();
            // dd($validatedData);
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
    public function fetch(Request $request)
    {
        $search = $request->input('search');
        $preparedSearch = "%$search%";
        $capaian = Capaian::join('pegawai', 'pegawai.id', 'capaians.pegawai_id')
            ->join('jabatan', 'jabatan.id', 'pegawai.jabatan_id')
            ->join('predikats', 'capaians.predikat_id', 'predikats.id')
            ->where(DB::raw('CONCAT(capaians.periode, " ", capaians.tahun)'), 'like', $preparedSearch)
            ->orWhere('pegawai.nama', 'like', $preparedSearch)
            ->orWhere('pegawai.nip', 'like', $preparedSearch)
            ->orWhere('jabatan.nama', 'like', $preparedSearch)
            ->orWhere('predikats.nama', 'like', $preparedSearch)
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
        return response()->json($capaian, 200);
    }
    public function upload(Request $request)
    {
        dd($request->all());
    }
}
