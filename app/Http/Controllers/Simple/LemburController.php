<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Role;
use App\Models\Simple\Lembur;
use App\Models\Simple\LemburPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LemburController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = Lembur::query()->from('sulutweb_simple.lembur as sl');
        $query->join('sulutweb_man_management.timkerja as st', 'st.id', 'sl.tim_id');
        $query->with(['pegawai.pegawai']);
        $query->select(['sl.*', 'st.label as tim_kerja']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);

        $myTeam = AnggotaTimKerja::from('sulutweb_man_management.keanggotaan_timkerja as mkt')
            ->join('sulutweb_man_management.timkerja as ttk', 'mkt.tim_id', 'ttk.id')
            ->select(['mkt.*', 'ttk.label as tim_kerja'])
            ->where('mkt.pegawai_id', Auth::user()->id)->get();
        $keanggotaan = $myTeam->pluck('keanggotaan')->toArray();
        return Inertia::render('Simple/Lembur', [
            'lembur' => $lembur,
            'tim' => $myTeam,
            'keanggotaan' => $keanggotaan
        ]);
    }

    public function store(Request $request)
    {
        if ($request->isMethod('patch')) {
            if ($request->input('add_pegawai') == true) {
                $validated = $request->validate([
                    'id' => ['required', 'integer'],
                    'anggotalembur' => ['required', 'array'],
                    'pegawai' => ['required', 'array']
                ]);
            } else {
                $pegawai = $request->input('pegawai') ?? [];
                $validated = $request->validate([
                    'id' => ['required', 'integer'],
                    'pegawai.0.tanggal' => ['required', 'date'],
                    'pegawai.0.jam_mulai' => ['required', 'date_format:H:i,H:i:s'],
                    'pegawai.0.jam_selesai' => ['required', 'date_format:H:i,H:i:s'],
                    'maksud_lembur' => ['required', 'string'],
                ], [
                    'pegawai.0.tanggal.required' => 'Tanggal harus diisi',
                    'pegawai.0.jam_mulai.required' => 'Jam mulai harus diisi',
                    'pegawai.0.jam_selesai.required' => 'Jam selesai harus diisi',
                    'maksud_lembur.required' => 'Maksud lembur harus diisi',
                ]);
            }
            try {
                //code...
                DB::connection('sulutweb_simple')->beginTransaction();
                if ($request->input('add_pegawai') == true) {
                    $anggota = $validated['anggotalembur'];
                    $pegawai = $request->input('pegawai')[0];
                    foreach ($anggota as $key => $p) {
                        # code...
                        LemburPegawai::firstOrCreate(
                            [
                                'lembur_id' => $validated['id'],
                                'pegawai_id' => $p,
                            ],
                            [
                                'lembur_id' => $validated['id'],
                                'pegawai_id' => $p,
                                'tanggal' => $pegawai['tanggal'],
                                'jam_mulai' => $pegawai['jam_mulai'],
                                'jam_selesai' => $pegawai['jam_selesai'],
                            ]
                        );
                    }
                } else {
                    $lembur_to_update = Lembur::findOrFail($validated['id']);
                    $lembur_to_update->update($validated);

                    if (sizeof($pegawai) > 0) {
                        $lp = [
                            'tanggal' => \Carbon\Carbon::parse($validated['pegawai'][0]['tanggal'])->setTimezone('Asia/Makassar')->format('Y-m-d'),
                            'jam_mulai' => $validated['pegawai'][0]['jam_mulai'],
                            'jam_selesai' => $validated['pegawai'][0]['jam_selesai'],
                        ];
                        foreach ($pegawai as $key => $p) {
                            # code...
                            $lp_to_update = LemburPegawai::findOrFail($p['id']);
                            $lp_to_update->update($lp);
                        }
                    }
                }
                DB::connection('sulutweb_simple')->commit();
                return redirect()->route('simple.lembur')->with('success', 'Berhasil mengedit data lembur');
            } catch (\Throwable $th) {
                //throw $th;
                DB::connection('sulutweb_simple')->rollback();
                return redirect()->route('simple.lembur')->with('error', 'Terdapat error : ' . $th->getMessage());
            }
        }
        $validated = $request->validate([
            'tim_id' => ['required', 'integer'],
            'anggotalembur' => ['required', 'array'],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i,H:i:s'],
            'jam_selesai' => ['required', 'date_format:H:i,H:i:s'],
            'maksud_lembur' => ['required', 'string'],
        ]);
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $validated['tanggal'] = \Carbon\Carbon::parse($validated['tanggal'])->setTimezone('Asia/Makassar')->format('Y-m-d');
            $lembur = Lembur::create($validated);
            foreach ($validated['anggotalembur'] as $key => $anggota) {
                # code...
                $validated['pegawai_id'] = $anggota;
                $validated['lembur_id'] = $lembur->id;
                LemburPegawai::firstOrCreate(
                    [
                        'lembur_id' => $lembur->id,
                        'pegawai_id' => $anggota,
                    ],
                    $validated
                );
            }
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur')->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur')->with('error', 'Gagal menambahkan data, error: ' . $th->getMessage());
        }
    }

    public function destroyPegawai($id)
    {
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $lp_to_delete = LemburPegawai::findOrFail($id);
            if ($lp_to_delete)
                $lp_to_delete->delete();
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur')->with('success', 'Berhasil hapus data');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur')->with('error', 'Gagal hapus data, error : ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $lembur_to_delete = Lembur::findOrFail($id);
            if ($lembur_to_delete)
                $lembur_to_delete->delete();
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur')->with('success', 'Berhasil hapus data');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur')->with('error', 'Gagal hapus data, error : ' . $th->getMessage());
        }

    }

    public function verify(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;
        $my_role = Role::currentRole();

        $query_myTeam = AnggotaTimKerja::query();
        $query_myTeam->from('sulutweb_man_management.keanggotaan_timkerja as mkt')
            ->join('sulutweb_man_management.timkerja as ttk', 'mkt.tim_id', 'ttk.id');
        if ($my_role != 'admin') {
            $query_myTeam->where('mkt.keanggotaan', 'ketua');
        }

        $myTeam = $query_myTeam->select(['mkt.*', 'ttk.label as tim_kerja'])
            ->where('mkt.pegawai_id', Auth::user()->id)->get();
        $keanggotaan = $myTeam->pluck('keanggotaan')->toArray();

        $query = Lembur::query()->from('sulutweb_simple.lembur as sl');
        $query->join('sulutweb_man_management.timkerja as st', 'st.id', 'sl.tim_id');
        if ($my_role != 'admin') {
            $query->whereIn('sl.tim_id', $myTeam->pluck('tim_id')->toArray());
        }
        $query->with(['pegawai.pegawai']);
        $query->select(['sl.*', 'st.label as tim_kerja']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);

        return Inertia::render('Simple/Verify', [
            'lembur' => $lembur,
            'tim' => $myTeam,
            'keanggotaan' => $keanggotaan
        ]);
    }

    public function verifyPatch(Request $request)
    {
        $validated = $request->validate([
            'individual' => ['required', 'boolean'],
            'status' => ['required', 'string', 'in:setuju,ditolak'],
            'catatan' => ['required_if:status,ditolak', 'nullable', 'string'],
            'lembur_id' => ['required_if:individual,false', 'integer'],
            'lembur_pegawai' => ['required_if:individual,true', 'nullable', 'array']
        ]);

        try {
            DB::connection('sulutweb_simple')->beginTransaction();
            $updateData = [
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? null
            ];
            if ($validated['individual'] == true)
                LemburPegawai::whereIn('id', $validated['lembur_pegawai'])->update($updateData);
            else
                LemburPegawai::where('lembur_id', $validated['lembur_id'])->update($updateData);
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur.verify')->with('success', 'Berhasil mengubah status lembur');

        } catch (\Throwable $th) {
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur.verify')->with('error', 'Gagal mengubah status lembur, error: ' . $th->getMessage());
        }
    }


    public function fetchMaksud($tim_id)
    {
        $maksudLembur = Lembur::where('tim_id', $tim_id)->pluck('maksud_lembur')->toArray();
        return response()->json($maksudLembur);
    }
}
