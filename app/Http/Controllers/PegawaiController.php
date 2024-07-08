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
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PegawaiController extends Controller
{
    public function kelola_pak(Request $request)
    {
        $search = $request->input('search');

        // // Urutkan berdasarkan unit kerja tapi bps provinsi di paling atas
        // $pegawai = Pegawai::query()
        //     ->where('nama', 'like', "%{$search}%")
        //     ->orWhere('nip', 'like', "%{$search}%")
        //     ->orWhere('jabatan', 'like', "%{$search}%")
        //     ->orWhere('unit_kerja', 'like', "%{$search}%")
        //     // ->orderByRaw("FIELD(unit_kerja, 'BPS Provinsi') DESC")
        //     ->paginate(20)
        //     ->appends(['search' => $search]);

        $pegawai = Pegawai::query()
            ->where('nama', 'like', "%{$search}%")
            ->orWhere('nip', 'like', "%{$search}%")
            ->orWhere('jabatan', 'like', "%{$search}%")
            ->orWhere('unit_kerja', 'like', "%{$search}%")
            ->orderByRaw('CASE 
            WHEN unit_kerja = "BPS Prov. Sulawesi Utara" THEN 1 
            ELSE 2 
          END')
            ->orderBy('unit_kerja', 'asc')
            ->orderByRaw('CASE 
            WHEN unit_kerja = "BPS Prov. Sulawesi Utara" AND jabatan = "Kepala BPS Provinsi" THEN 1
            WHEN unit_kerja = "BPS Prov. Sulawesi Utara" AND jabatan = "Kepala Bagian Umum" THEN 2
            WHEN jabatan = "Kepala BPS Kabupaten/Kota" THEN 3
            WHEN jabatan = "Kepala Subbagian Umum" THEN 4
            ELSE 5
          END')
            ->paginate(20)
            ->appends(['search' => $search]);;



        return Inertia::render('PAK/KelolaPak', [
            'pegawai' => $pegawai,
            'search' => $search,
            "jabatan" => Jabatan::all(),
            "unitKerja" => UnitKerja::all(),
        ]);
    }

    public function show(Pegawai $pegawai)
    {
        $histories = AngkaKreditHistory::where('pegawai_id', $pegawai->id)->orderBy('created_at', 'desc')->get();
        // $histories->created_at = Carbon::parse($histories->created_at)->translatedFormat('j F Y');


        return inertia('PAK/DetailPak', [
            'pegawai' => $pegawai,
            'histories' => $histories
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip_bps' => 'required',
            'nip' => 'required',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
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
            'jabatan' => 'required|string|max:255',
            'unit_kerja' => 'required|string|max:255',
            'pangkat_golongan_ruang' => 'required|string|max:255',
            'angka_kredit_konvensional' => 'nullable|string|max:255',
            'angka_kredit_integrasi' => 'nullable|string|max:255',
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
