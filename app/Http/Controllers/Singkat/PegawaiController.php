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
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        return Inertia::render('Singkat/Pegawai/index', [
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
        $currentHistories = Capaian::with(['jabatan', 'jenis_sk'])
            ->where('pegawai_id', $pegawai["id"])
            ->where('jabatan_id', $pegawai['jabatan_id'])
            // ->orderBy('tahun',)
            // ->orderBy('periode')
            ->get()->toArray();

        $akumulasi_ak = $pegawai["akumulasi_ak"];
        foreach ($currentHistories as $key => $history) {
            # code...
            $akumulasi_ak = $akumulasi_ak + $history["angka_kredit"];

            $currentHistories[$key]["akumulasi_ak"] = $akumulasi_ak;
        }
        $pegawai["akumulasi_ak"] = $akumulasi_ak;
        $pegawai['daftar_jabatan'] = $this->get_riwayat_jabatan($pegawai);


        return inertia('Singkat/Pegawai/Detail', [
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
        $histories = Capaian::with(['jabatan'])
            ->where('pegawai_id', $pegawai["id"])
            ->where('jabatan_id', $pegawai['jabatan_id'])
            ->get()->toArray();
        $akumulasi_ak = $pegawai["akumulasi_ak"];
        foreach ($histories as $key => $history) {
            # code...
            $akumulasi_ak = $akumulasi_ak + $history["angka_kredit"];

            $histories[$key]["akumulasi_ak"] = $akumulasi_ak;
        }
        $pegawai["akumulasi_ak"] = $akumulasi_ak;
        $pegawai['daftar_jabatan'] = $this->get_riwayat_jabatan($pegawai);


        return inertia('Singkat/Pegawai/Detail', [
            'pegawai' => $pegawai,
            'histories' => $histories,

        ]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nip_bps' => 'required|string|size:9|unique:pegawai,nip_bps',
                'nip' => 'required|string|size:18',
                'nama' => 'required|string|max:255',
                'jabatan_id' => 'required|numeric',
                'unit_kerja' => 'required|string|max:255',
                'pangkat_golongan_ruang' => 'required|string|max:255',
                'angka_kredit_dasar' => 'nullable|numeric',
                'angka_kredit_integrasi' => 'nullable|numeric',
                'angka_kredit_konvensional' => 'nullable|numeric',
                'predikat_id' => 'required|numeric',
                'akumulasi_ak' => 'required|numeric',
                'bulan_mulai' => 'required',
                'bulan_selesai' => 'required',
                'ijazah_terakhir' => 'nullable|string|max:255',
            ]);

            $validatedData["angka_kredit_konvensional"] = (string)$request->angka_kredit_konvensional;
            $validatedData["angka_kredit_integrasi"] = (string)$request->angka_kredit_integrasi;

            $validatedData["tanggal_lahir"] = $this->calculateTanggalLahir($validatedData['nip']);
            $validatedData['id'] = $validatedData['nip_bps'];
            $validatedData['bulan_mulai'] = Carbon::parse($validatedData['bulan_mulai']);
            $validatedData['bulan_selesai'] = Carbon::parse($validatedData['bulan_selesai']);

            DB::beginTransaction();
            Pegawai::create($validatedData);
            User::create([
                'name' => $validatedData['nama'],
                'pegawai_id' => $validatedData['id'],
                'username' => $validatedData['id'],
                'role' => 'viewer',
                'email' => $validatedData['id'] . '@monitoringbps.com',
                'password' => Hash::make('password')
            ]);
            DB::commit();
            return response()->json(["message" => "Pegawai baru berhasil ditambahkan!"]);
            //code...
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

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
            'angka_kredit_dasar' => 'nullable',
            'tambahan_ijazah' => 'nullable|string|max:255',
            'akumulasi_ak' => 'nullable',
            'ijazah_terakhir' => 'nullable|string|max:255',
            'bulan_mulai' => 'nullable',
            'bulan_selesai' => 'nullable',
            'predikat_id' => 'nullable|numeric',


        ]);


        // $tanggal_lahir = $this->calculateTanggalLahir($request->nip);
        $validatedData["tanggal_lahir"] = $this->calculateTanggalLahir($validatedData['nip']);
        if (isset($validatedData['bulan_mulai']) && isset($validatedData['bulan_selesai'])) {
            $validatedData['bulan_mulai'] = Carbon::parse($validatedData['bulan_mulai']);
            $validatedData['bulan_selesai'] = Carbon::parse($validatedData['bulan_selesai']);
        } else {
            $validatedData['bulan_mulai'] = null;
            $validatedData['bulan_selesai'] = null;
        }

        
        $pegawai->update($validatedData);
        return response()->json($validatedData);
    }

    public function destroy(Pegawai $pegawai)
    {
        try {
            //code...
            $user = User::where('pegawai_id', $pegawai->nip_bps)->first();
            DB::beginTransaction();
            $pegawai->delete();
            $user->delete();
            DB::commit();
            return response()->json(["message" => "Pegawai berhasil dihapus"], 200);
        } catch (Exception $exp) {
            DB::rollBack();
            return response()->json(["error" => $exp->getMessage()], 200);
            //throw $th;
        }
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
        if (count($histories) < 1) {
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
