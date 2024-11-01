<?php

namespace App\Http\Controllers\Singkat;

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

        $pegawais = Pegawai::with(['jabatan', 'capaian'])

            ->where(function ($query) use ($keyword) {
                $query->where('pegawai.nama', 'like', '%' . $keyword . '%')
                    ->orWhere('pegawai.nip', 'like', '%' . $keyword . '%')
                    ->orWhere('pegawai.pangkat_golongan_ruang', 'like', '%' . $keyword . '%')
                    ->orWhereHas('jabatan', function ($query) use ($keyword) {
                        $query->where('nama', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere('pegawai.unit_kerja', 'like', '%' . $keyword . '%');
            })
            ->paginate(20);
        // dd([$pegawais]);

        return Inertia::render('Singkat/PAK/KelolaPak', [
            'pegawai' => $pegawais,
            'search' => $search,
            'jabatan' => Jabatan::all(),
            'unitKerja' => UnitKerja::all(),
        ]);
    }

    public function show(Pegawai $pegawai)
    {
        $user = Auth::user();
        // dd([$user->pegawai_id, $pegawai->id]);
        if ($user->pegawai_id != $pegawai->id) {
            if ($user->role != 'admin' && $user->role != 'operator') {

                return redirect('/kelola-pak/' . $user->pegawai_id);
            }
        }
        $pegawai = $pegawai->with(['jabatan', 'capaian'])->find($pegawai->id);
        // $histories = AngkaKreditHistory::where('pegawai_id', $pegawai->id)->with(['capaian' => function ($query) {
        //     $query->with('tambahan');
        // }])->get();
        // dd($pegawai);
        $currentHistories = Capaian::with(['jabatan'])
            ->where('pegawai_id', $pegawai["id"])
            ->where('jabatan_id', $pegawai['jabatan_id'])
            ->orderBy('tahun',)
            ->orderBy('periode')
            ->get()->toArray();
        
        $akumulasi_ak = $pegawai["akumulasi_ak"];
        foreach ($currentHistories as $key => $history) {
            # code...
            $akumulasi_ak = $akumulasi_ak + $history["angka_kredit"];

            $currentHistories[$key]["akumulasi_ak"] = $akumulasi_ak;
        }
        $pegawai["akumulasi_ak"] = $akumulasi_ak;
        $pegawai['daftar_jabatan'] = $this->get_riwayat_jabatan($pegawai);


        return inertia('Singkat/PAK/DetailPak', [
            'pegawai' => $pegawai,
            'histories' => $currentHistories,

        ]);
    }
    public function capaian_ku()
    {
        $user = Auth::user();
        // dd([$user->pegawai_id, $pegawai->id]);
        $pegawai = Pegawai::with(['jabatan', 'capaian'])->find($user->pegawai_id);
        // $histories = AngkaKreditHistory::where('pegawai_id', $pegawai->id)->with(['capaian' => function ($query) {
        //     $query->with('tambahan');
        // }])->get();
        // dd($pegawai);
        $histories = Capaian::where('pegawai_id', $pegawai["id"])
            ->where('jabatan_id', $pegawai['jabatan_id'])
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
        $pegawai['daftar_jabatan'] = $this->get_riwayat_jabatan($pegawai);


        return inertia('Singkat/PAK/DetailPak', [
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


        $validatedData["tanggal_lahir"] = $this->calculateTanggalLahir($validatedData['nip']);
        $validatedData['id'] = $validatedData['nip_bps'];


        Pegawai::create($validatedData);
        return response()->json($validatedData);

        // return redirect()->route('kelola-pak')->with('success', 'Pegawai berhasil ditambahkan.');
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
            'akumulasi_ak' => 'nullable',
            'ijazah_terakhir' => 'nullable|string|max:255',

        ]);


        // $tanggal_lahir = $this->calculateTanggalLahir($request->nip);
        $validatedData["tanggal_lahir"] = $this->calculateTanggalLahir($validatedData['nip']);

        if ($validatedData['jabatan_id'] != $pegawai->jabatan_id) {
            $validatedData['akumulasi_ak'] = 0;
            // $newCapaian = [
            //     'pegawai_id'=>$validatedData['nip_bps'],
            //     'periode'=>'-',
            //     'tahun'=> date("Y"),
            //     'predikat_id'=>1,
            //     'angka_kredit'=>0,
            //     'angka_kredit_dasar'=>0,
            //     'jabatan_id'=>$validatedData['jabatan_id']
            // ];
            // Capaian::create($newCapaian);

        }
        $pegawai->update($validatedData);
        return response()->json($validatedData);
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



    private function calculateTanggalLahir($nip)
    {
        $year = substr($nip, 0, 4);
        $month = substr($nip, 4, 2);
        $day = substr($nip, 6, 2);
        $birthDate = Carbon::createFromDate($year, $month, $day);


        return $birthDate;
    }
    public function fetch(Request $request)
    {
        $pegawais = Pegawai::select('pegawai.id', 'pegawai.nama')->get();
        return response()->json($pegawais);
    }
    private function get_riwayat_jabatan(Pegawai $pegawai)
    {
        $daftar_jabatan = [];
        array_push($daftar_jabatan, $pegawai->jabatan->id);
        $capaians = Capaian::where('pegawai_id', $pegawai->id)->get();
        foreach ($capaians as $capaian) {
            # code...
            if (!in_array($capaian->jabatan_id, $daftar_jabatan)) {
                array_push($daftar_jabatan, $capaian->jabatan_id);
            }
        }
        $jabatans = Jabatan::whereIn('id', $daftar_jabatan)->get();
        // dd($jabatans);
        return $jabatans;
    }
    public function get_histories(Request $request)
    {
        $req = $request->all();
        $pegawai_id = $req['pegawai_id'];
        $jabatan_id = $req['jabatan_id'];
        // $pegawai = Pegawai::with(['jabatan', 'capaian'])->find($pegawai_id);
        // $histories = AngkaKreditHistory::where('pegawai_id', $pegawai->id)->with(['capaian' => function ($query) {
        //     $query->with('tambahan');
        // }])->get();
        // dd($pegawai);
        $histories = Capaian::where('pegawai_id', $pegawai_id)
            ->where('jabatan_id', $jabatan_id)
            ->orderBy('tahun',)
            ->orderBy('periode')
            ->get()->toArray();
            // dd([$histories,$pegawai_id,$jabatan_id]);
        if(count($histories)<1)
        {
            return $histories;
        }
        $akumulasi_ak = $histories[0]["angka_kredit_dasar"];
        foreach ($histories as $key => $history) {
            # code...
            $akumulasi_ak = $akumulasi_ak + $history["angka_kredit"];

            $histories[$key]["akumulasi_ak"] = $akumulasi_ak;
        }
        // $pegawai["akumulasi_ak"] = $akumulasi_ak;
        return $histories;
    }
}
