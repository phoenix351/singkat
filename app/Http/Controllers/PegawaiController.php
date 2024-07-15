<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use App\Exports\PegawaiExport;
use App\Models\AngkaKreditHistory;
use App\Models\Capaian;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
  
    
    public function kelola_pak(Request $request)
    {
        $search = $request->input('search');
        $keyword = "%$search%";
    
        // Subquery to calculate the total angka_kredit for each pegawai
        $subQuery = DB::table('capaians')
            ->select('pegawai_id', DB::raw('SUM(angka_kredit) as total_angka_kredit'))
            ->groupBy('pegawai_id');
    
        // Main query with subquery join
        $pegawais = Pegawai::join('jabatan', 'jabatan.id', '=', 'pegawai.jabatan_id')
            ->leftJoinSub($subQuery, 'capaian_sum', function($join) {
                $join->on('pegawai.id', '=', 'capaian_sum.pegawai_id');
            })
            ->select(
                'pegawai.*',
                'jabatan.nama as nama_jabatan',
                DB::raw('COALESCE(capaian_sum.total_angka_kredit, 0) + pegawai.akumulasi_ak as angka_kredit_akumulasi')
            )
            ->where(function($query) use ($keyword) {
                $query->where('pegawai.nama', 'like', $keyword)
                      ->orWhere('jabatan.nama', 'like', $keyword)
                      ->orWhere('pegawai.unit_kerja', 'like', $keyword);
            })
            ->paginate(20);
    
        return Inertia::render('PAK/KelolaPak', [
            'pegawai' => $pegawais,
            'search' => $search,
            'jabatan' => Jabatan::all(),
            'unitKerja' => UnitKerja::all(),
        ]);
    }
    
    public function show(Pegawai $pegawai)
    {
        $pegawai = $pegawai->join('jabatan', 'jabatan.id', 'pegawai.jabatan_id')->where('pegawai.id', $pegawai->id)->select('pegawai.*', 'jabatan.nama as jabatan')->first()->toArray();
        // $histories = AngkaKreditHistory::where('pegawai_id', $pegawai->id)->with(['capaian' => function ($query) {
        //     $query->with('tambahan');
        // }])->get();
        $histories = Capaian::where('pegawai_id', $pegawai["id"])
            ->orderBy('tahun',)
            ->orderBy('periode')
            ->get()->toArray();
        $akumulasi_ak = $pegawai["akumulasi_ak"];
        foreach ($histories as $key => $history) {
            # code...
            $akumulasi_ak = $akumulasi_ak + $history["angka_kredit"];

            $histories[$key]["akumulasi_ak"] = $akumulasi_ak;
        }
        $pegawai["akumulasi_ak"] = $akumulasi_ak;
        // $histories->created_at = Carbon::parse($histories->created_at)->translatedFormat('j F Y');


        return inertia('PAK/DetailPak', [
            'pegawai' => $pegawai,
            'histories' => $histories,

        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip_bps' => 'required',
            'nip' => 'required',
            'nama' => 'required|string|max:255',
            'jabatan_id' => 'required',
            'unit_kerja' => 'required|string|max:255',
            'pangkat_golongan_ruang' => 'required|string|max:255',
            'angka_kredit_konvensional' => 'nullable',
            'angka_kredit_integrasi' => 'nullable',
            'predikat_kinerja' => 'nullable|string|max:255',
            'tambahan_ijazah' => 'nullable|string|max:255',
            'akumulasi_ak' => 'nullable|string|max:255',
            'ijazah_terakhir' => 'nullable|string|max:255',
        ]);

        $validatedData["angka_kredit_konvensional"] = (string)$request->angka_kredit_konvensional;
        $validatedData["angka_kredit_integrasi"] = (string)$request->angka_kredit_integrasi;

        $usia_per_3_januari = $this->calculateAgeFromNIP($request->nip);
        $tmt_pensiun = $this->calculateTmtPensiun($request->nip, $request->jabatan);

        $validatedData["usia"] = floor($usia_per_3_januari);
        $validatedData["tmt_pensiun"] = $tmt_pensiun;


        Pegawai::create($validatedData);

        return redirect()->route('kelola-pak')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function update(Request $request, Pegawai $pegawai)
    {

        // if (Auth::user()->role !== 'admin' || Auth::user()->role !== 'super admin') {
        //     abort(403, 'Unauthorized');
        // }

        $validatedData = $request->validate([
            'nip_bps' => 'required',
            'nip' => 'required',
            'nama' => 'required|string|max:255',
            'jabatan_id' => 'required',
            'unit_kerja' => 'required|string|max:255',
            'pangkat_golongan_ruang' => 'required|string|max:255',
            'angka_kredit_konvensional' => 'nullable',
            'angka_kredit_integrasi' => 'nullable',
            'predikat_kinerja' => 'nullable|string|max:255',
            'tambahan_ijazah' => 'nullable|string|max:255',
            'akumulasi_ak' => 'nullable|string|max:255',
            'ijazah_terakhir' => 'nullable|string|max:255',

        ]);


        $usia_per_3_januari = $this->calculateAgeFromNIP($request->nip);
        $tmt_pensiun = $this->calculateTmtPensiun($request->nip, $request->jabatan);


        $validatedData["usia"] = floor($usia_per_3_januari);
        $validatedData["tmt_pensiun"] = $tmt_pensiun;

        // Cek apakah akumulasi ak berubah
        if ($pegawai->akumulasi_ak !== $request->akumuasi_ak) {
            // Simpan history perubahan
            AngkaKreditHistory::create([
                'pegawai_id' => $pegawai->id,
                'akumulasi_ak' => $pegawai->akumulasi_ak,
            ]);
        }

        $pegawai->update($validatedData);

        return redirect()->route('kelola-pak')->with('success', 'Pegawai berhasil diupdate.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return redirect()->back()->with('success', 'Pegawai berhasil dihapus.');
    }


    public function export(Request $request)
    {

        $columns = $request->input('columns');

        return Excel::download(new PegawaiExport($columns), 'pegawai.xlsx');
    }

    private function calculateAgeFromNIP($nip)
    {
        $year = substr($nip, 0, 4);
        $month = substr($nip, 4, 2);
        $day = substr($nip, 6, 2);
        $birthDate = Carbon::createFromDate($year, $month, $day);
        $date = Carbon::createFromDate(date('Y'), 1, 3);  // 3 Januari tahun ini
        return $birthDate->diffInYears($date);
    }

    private function calculateTmtPensiun($nip, $jabatan)
    {
        $year = substr($nip, 0, 4);
        $month = substr($nip, 4, 2);
        $day = substr($nip, 6, 2);
        $birthDate = Carbon::createFromDate($year, $month, $day);

        // Menentukan usia pensiun berdasarkan jabatan
        $retirementAge = (stripos($jabatan, 'madya') !== false) ? 60 : 58;
        $pensiunDate = $birthDate->addYears($retirementAge);

        // Tambahkan satu bulan dan set tanggal ke hari pertama bulan tersebut
        $pensiunDate = $pensiunDate->addMonth()->startOfMonth();

        // Format tanggal ke format "01 Oktober 2034"
        return $pensiunDate->translatedFormat('d F Y');
    }
    public function fetch()
    {
        $pegawais = Pegawai::select('pegawai.id', 'pegawai.nama')->get();
        return response()->json($pegawais);
    }
}
