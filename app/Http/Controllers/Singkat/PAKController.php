<?php

namespace App\Http\Controllers\Singkat;

use App\Http\Requests\StorePAKRequest;
use App\Http\Requests\UpdatePAKRequest;
use App\Models\AngkaKreditHistory;
use App\Models\Capaian;
use App\Models\Pegawai;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class PAKController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $preparedSearch = "%$search%";
        $capaian = Capaian::join('pegawai', 'pegawai.id', 'capaians.pegawai_id')
            ->join('jabatan', 'jabatan.id', 'capaians.jabatan_id')
            ->join('predikats', 'capaians.predikat_id', 'predikats.id')
            ->Where('pegawai.nama', 'like', $preparedSearch)
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
    public function store(StorePAKRequest $request)
    {
        $validatedData = $request->validated();
        try {
            // cek if any existing months

            // begin saving the data
            DB::beginTransaction();
            $capaian = Capaian::create($validatedData);
            if ($request->file('file')->isValid()) {
                // ...
                $path = $request->file('file')->storeAs('filePak', $validatedData['pegawai_id'] . $capaian->id . ".pdf");
                $validatedData['path'] = $path;
                unset($validatedData['file']);
                $capaian->path = $path;
            }
            // dd($capaian);
            $capaian->jabatan_id = Pegawai::find($validatedData['pegawai_id'])->jabatan_id;
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
    public function update(UpdatePAKRequest $request, Capaian $capaian)
    {
        try {
            //code...

            DB::beginTransaction();
            $validatedData = $request->all();
            // dd($validatedData);
            if ($request->file('file')) {
                // ...
                $path = $request->file('file')->storeAs('filePak', $validatedData['pegawai_id'] . $validatedData['id'] . ".pdf");
                $validatedData['path'] = $path;
                unset($validatedData['file']);
            }
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
    public function read_pak(Capaian $capaian)
    {
        if ($capaian->path) {

            $is_file_exists = Storage::exists($capaian->path);
            if ($is_file_exists) {
                return response()->file(Storage::path($capaian->path));
            }
        }

        throw new NotFoundHttpException("Dokumen Tidak ditemukan");
    }
}
