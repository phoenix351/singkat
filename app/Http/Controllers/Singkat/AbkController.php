<?php

namespace App\Http\Controllers\Singkat;

use App\Models\Abk;
use Inertia\Inertia;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Exports\AbkExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AbkController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->input('search');

        // Join dengan unitkerja dan jabatan 
        $abk = Abk::select('abk.*', 'unit_kerja.nama as unit_kerja', 'jabatan.nama as jabatan')
            ->join('unit_kerja', 'abk.unit_kerja_id', '=', 'unit_kerja.id')
            ->join('jabatan', 'abk.jabatan_id', '=', 'jabatan.id')
            ->where('jabatan_id', 'like', '%' . $search . '%')
            ->orWhere('unit_kerja_id', 'like', '%' . $search . '%')
            ->orWhere('abk', 'like', '%' . $search . '%')
            ->orWhere('eksisting', 'like', '%' . $search . '%')
            ->orWhere('kebutuhan_pegawai', 'like', '%' . $search . '%')
            ->orderByRaw("CASE WHEN unit_kerja.nama = 'BPS Prov. Sulawesi Utara' THEN 0 ELSE 1 END, unit_kerja.nama")
            ->paginate(20);

        return Inertia::render('ABK/KelolaABK', [
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

        return redirect()->route('abk.index')->with('success', 'Data ABK berhasil ditambahkan.');
    }

    public function update(Request $request, Abk $abk)
    {
        $validatedData = $request->validate([
            'jabatan_id' => 'required',
            'unit_kerja_id' => 'required',
            'abk' => 'required',
            'eksisting' => 'required',
        ]);

        $validatedData['kebutuhan_pegawai'] = $validatedData['abk'] - $validatedData['eksisting'];

        $abk->update($validatedData);

        return redirect()->route('abk.index')->with('success', 'Data ABK berhasil diubah.');
    }

    public function destroy(Abk $abk)
    {
        $abk->delete();

        return redirect()->route('abk.index')->with('success', 'Data ABK berhasil dihapus.');
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
}
