<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
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

        $query = Lembur::query();
        $query->with(['tim']);
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
        $validated = $request->validate([
            'tim_id' => ['required', 'integer'],
            'anggotalembur' => ['required', 'array'],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'maksud_lembur' => ['required', 'string'],
        ]);
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $validated['tanggal'] = \Carbon\Carbon::parse($validated['tanggal'])->format('Y-m-d');
            $lembur = Lembur::create($validated);
            foreach ($validated['anggotalembur'] as $key => $anggota) {
                # code...
                $validated['pegawai_id'] = $anggota;
                $validated['lembur_id'] = $lembur->id;
                LemburPegawai::create($validated);
            }
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur')->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur')->with('error', 'Gagal menambahkan data, error: ' . $th->getMessage());
        }
    }

    public function fetchMaksud($tim_id)
    {
        $maksudLembur = Lembur::where('tim_id', $tim_id)->pluck('maksud_lembur')->toArray();
        return response()->json($maksudLembur);
    }
}
