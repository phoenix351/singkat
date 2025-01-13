<?php

namespace App\Http\Controllers\Singkat;

use App\Models\Abk;
use Inertia\Inertia;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Exports\AbkExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AbkController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->input('search');
        // dd($search);

        // Join dengan unitkerja dan jabatan 
        $abk = Jabatan::select('unit_kerja.nama as unit_kerja', 'unit_kerja.id as unit_kerja_id', 'jabatan.nama as jabatan', 'jabatan.id as jabatan_id', 'abk.id as id', 'abk.abk', 'abk.eksisting', 'abk.kebutuhan_pegawai')
            ->crossJoin('unit_kerja')
            ->leftJoin('abk', function ($join) {
                $join->on('abk.unit_kerja_id', 'unit_kerja.id')
                    ->on('abk.jabatan_id', 'jabatan.id');
            })
            ->where('jabatan.nama', 'like', '%' . $search . '%')
            ->orWhere('unit_kerja.nama', 'like', '%' . $search . '%')
            ->orWhere(DB::raw("CAST(abk.abk AS CHAR)"), '=', $search)
            ->orWhere(DB::raw("CAST(abk.eksisting AS CHAR)"), '=', $search)
            ->orWhere(DB::raw("CAST(abk.kebutuhan_pegawai AS CHAR)"), '=', $search)
            ->orderByRaw("CASE WHEN unit_kerja.nama = 'BPS Prov. Sulawesi Utara' THEN 0 ELSE 1 END, unit_kerja.nama")
            ->paginate();

        return Inertia::render('Singkat/ABK/KelolaABK', [
            'abk' => $abk,
            'search' => $search,
            "jabatan" => Jabatan::all(),
            "unitKerja" => UnitKerja::all(),
        ]);
    }

    public function getAvailableJabatan(Request $request)
    {
        $unitKerjaId = $request->input('unit_kerja_id');
        $existingJabatanIds = Abk::where('unit_kerja_id', $unitKerjaId)->pluck('jabatan_id')->toArray();
        $jabatanList = Jabatan::whereNotIn('id', $existingJabatanIds)->get();
        return response()->json($jabatanList);
    }

    public function getAvailableJabatanEdit(Request $request)
    {
        $unitKerjaId = $request->input('unit_kerja_id');
        $abkId = $request->input('abk_id');
        $existingJabatanIds = Abk::where('unit_kerja_id', $unitKerjaId)
            ->where('id', '!=', $abkId)
            ->pluck('jabatan_id')
            ->toArray();
        $jabatanList = Jabatan::whereNotIn('id', $existingJabatanIds)->get();
        return response()->json($jabatanList);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jabatan_id' => 'required',
            'unit_kerja_id' => 'required',
            'abk' => 'required',
            'eksisting' => 'required',
        ], [
            'jabatan_id.required' => 'Jabatan harus diisi',
            'unit_kerja_id.required' => 'Unit Kerja harus diisi',
            'abk.required' => 'ABK harus diisi',
            'eksisting.required' => 'Eksisting harus diisi',
        ]);
        $validatedData['kebutuhan_pegawai'] = $validatedData['abk'] - $validatedData['eksisting'];
        Abk::create($validatedData);
        return response()->json(['message' => 'Data ABK berhasil ditambahkan.'], 200);
    }

    public function update(Request $request, Abk $abk)
    {
        try {
            //code...
            $validatedData = $request->validate([
                'jabatan_id' => 'required',
                'unit_kerja_id' => 'required',
                'abk' => 'required',
                'eksisting' => 'required',
            ]);

            $validatedData['kebutuhan_pegawai'] = $validatedData['abk'] - $validatedData['eksisting'];

            $abk->update($validatedData);
            return response()->json(['message' => 'data berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy(Abk $abk)
    {
        $abk->delete();

        return response()->json(['message' => 'Data ABK berhasil dihapus.'], 200);
    }

    public function getAbkSummary()
    {

        // Ambil data dari tabel abk dan gabungkan dengan tabel unit_kerja
        $summary = Abk::join('unit_kerja', 'abk.unit_kerja_id', '=', 'unit_kerja.id')
            ->selectRaw('unit_kerja.nama as unit_kerja_nama, 
                SUM(abk) as total_abk, 
                SUM(eksisting) as total_eksisting, 
                SUM(kebutuhan_pegawai) as total_kebutuhan_pegawai')
            ->groupBy('unit_kerja.nama')
            ->get();

        $result = [];

        foreach ($summary as $row) {
            // Hitung total untuk setiap baris
            $total = $row->total_abk + $row->total_eksisting + $row->total_kebutuhan_pegawai;

            $result[] = [
                'unit_kerja_nama' => $row->unit_kerja_nama,
                'total_abk' => $row->total_abk,
                'total_eksisting' => $row->total_eksisting,
                'total_kebutuhan_pegawai' => $row->total_kebutuhan_pegawai,
                'total' => $total,
            ];
        }



        return response()->json($result);
    }


    public function export(Request $request)
    {
        $columns = $request->input('columns');

        $showAllJabatan = $request->input('showAllJabatan', 'false') === 'true';

        // Validasi parameter unit kerja
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu unit kerja untuk diekspor.');
        }

        return Excel::download(new AbkExport($columns, $showAllJabatan), 'abk.xlsx');
    }
    public function fetch(Request $request)
    {
        $search = $request->input('search');
        // dd($search);

        // Join dengan unitkerja dan jabatan 
        $abk = Jabatan::select('unit_kerja.kode as unit_kerja_id', 'nomor_urut_kepka', 'jabatan.nama as jabatan', 'unit_kerja.nama as unit_kerja', 'abk.abk', 'abk.eksisting', 'abk.kebutuhan_pegawai')
            ->crossJoin('unit_kerja')
            ->leftJoin('abk', function ($join) {
                $join->on('abk.unit_kerja_id', 'unit_kerja.id')
                    ->on('abk.jabatan_id', 'jabatan.id');
            })
            ->where('jabatan.nama', 'like', '%' . $search . '%')
            ->orWhere('unit_kerja.nama', 'like', '%' . $search . '%')
            ->orWhere(DB::raw("CAST(abk.abk AS CHAR)"), '=', $search)
            ->orWhere(DB::raw("CAST(abk.eksisting AS CHAR)"), '=', $search)
            ->orWhere(DB::raw("CAST(abk.kebutuhan_pegawai AS CHAR)"), '=', $search)
            // ->orWhere('abk.eksisting', '=',   $search)
            // ->orWhere('abk.kebutuhan_pegawai','=',  $search)
            ->orderBy('nomor_urut_kepka')
            ->get();

        return response()->json($abk, 200);
    }
}
