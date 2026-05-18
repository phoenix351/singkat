<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Role;
use App\Models\Simple\LemburPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $filters = $request->input('filters', null);
        $tahun = null;
        $bulan = null;

        $query = LemburPegawai::query();
        if ($filters) {
            $tahun = $filters['tahun'] ?? null;
            $bulan = $filters['bulan'] ?? null;
            if ($bulan != 0)
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun);
            else
                $query->whereYear('tanggal', $tahun);
        } else
            $query->whereYear('tanggal', date('Y'));

        $myTeam = AnggotaTimKerja::where('pegawai_id', Auth::id())->pluck('tim_id')->toArray();
        $query->whereHas('lembur', function ($q) use ($myTeam) {
            $q->whereIn('tim_id', $myTeam);
        });

        $fivePegawaiMost = $query->with('pegawai')->get()->groupBy('pegawai.name')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortByDesc(fn($count) => $count)
            ->take(5);
        $fiveTeamMost = $query->with('lembur.tim')->get()->groupBy('lembur.tim.label')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortByDesc(fn($count) => $count)
            ->take(5);
        $lp = $query->get();
        $pending = $lp->where('status', 1)->count();
        $setuju_katim = $lp->where('status', 2)->count();
        $tolak_katim = $lp->where('status', 3)->count();
        $setuju_kabag = $lp->where('status', 4)->count();
        $tolak_kabag = $lp->where('status', 5)->count();

        $result = [
            'pending' => $pending,
            'setuju_katim' => $setuju_katim,
            'tolak_katim' => $tolak_katim,
            'setuju_kabag' => $setuju_kabag,
            'tolak_kabag' => $tolak_kabag
        ];
        if ($filters) {
            return response()->json([
                'result' => $result,
                'pegawais' => $fivePegawaiMost,
                'tims' => $fiveTeamMost
            ]);
        }

        return Inertia::render('Simple/Home', [
            'result' => $result,
            'pegawais' => $fivePegawaiMost,
            'tims' => $fiveTeamMost
        ]);
    }
}
