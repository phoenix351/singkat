<?php

namespace App\Http\Middleware;

use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Role;
use App\Models\Simple\LemburPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $route = Route::currentRouteName();
        $pendingOutputCount = 0;
        $lemburPending = 0;
        $lemburToVerify = 0;
        $pendingOutputs = [];
        $lemburPendingDetail = [];
        $lemburToVerifyDetail = [];

        if (Auth::check() && str_starts_with($route, 'simple.')) {
            $lemburData = LemburPegawai::with('lembur.tim', 'lembur.timPenanggungJawab')
                ->where('pegawai_id', Auth::id())
                ->whereNull('output')
                ->get();

            if (Role::currentRole() == 'admin') {
                // Admin melihat semua lembur pending dari semua tim
                $pendingRows = LemburPegawai::with('lembur.tim', 'lembur.timPenanggungJawab')
                    ->where('status', 1)
                    ->get();

                $lemburPending = $pendingRows->count();
                $lemburPendingDetail = $pendingRows
                    ->groupBy(fn($lp) => $lp->lembur->tim->label ?? $lp->lembur->timPenanggungJawab->label ?? '-')
                    ->map(fn($group, $timLabel) => [
                        'tim_kerja'     => $timLabel,
                        'jumlah'        => $group->count(),
                        'is_lintas_tim' => $group->first()?->lembur?->tim_id === null,
                        'tim_pj'        => $group->first()?->lembur?->timPenanggungJawab?->label ?? null,
                    ])
                    ->values();

            } elseif (Role::statusKeanggotaan() == 'ketua') {
                // Ketua Tim hanya melihat lembur dari tim yang ia ketuai
                // Ambil tim_id yang diketuai oleh user yang login
                $timDiketuai = AnggotaTimKerja::where('pegawai_id', Auth::id())
                    ->where('keanggotaan', 'ketua')
                    ->pluck('tim_id')
                    ->toArray();

                // Ambil lembur dengan status 1 yang:
                // - tim_id-nya adalah tim yang diketuai (lembur reguler), ATAU
                // - tim_penanggung_jawab_id-nya adalah tim yang diketuai (lintas tim)
                $pendingRows = LemburPegawai::with('lembur.tim', 'lembur.timPenanggungJawab')
                    ->where('status', 1)
                    ->whereHas('lembur', function ($q) use ($timDiketuai) {
                        $q->where(function ($inner) use ($timDiketuai) {
                            $inner->whereIn('tim_id', $timDiketuai)
                                  ->orWhereIn('tim_penanggung_jawab_id', $timDiketuai);
                        });
                    })
                    ->get();

                $lemburPending = $pendingRows->count();

                // Kelompokkan: kunci = "LINTAS::{tim_asal}" atau "{tim_biasa}"
                $lemburPendingDetail = $pendingRows
                    ->groupBy(function ($lp) {
                        $isLintas = $lp->lembur->tim_id === null;
                        $timLabel = $lp->lembur->tim->label
                            ?? $lp->lembur->timPenanggungJawab->label
                            ?? '-';
                        return $isLintas ? 'LINTAS::' . $timLabel : $timLabel;
                    })
                    ->map(function ($group, $key) {
                        $isLintas = str_starts_with($key, 'LINTAS::');
                        $timLabel = $isLintas ? substr($key, 8) : $key;
                        return [
                            'tim_kerja'     => $timLabel,
                            'jumlah'        => $group->count(),
                            'is_lintas_tim' => $isLintas,
                            'tim_pj'        => $isLintas
                                ? ($group->first()?->lembur?->timPenanggungJawab?->label ?? null)
                                : null,
                        ];
                    })
                    ->values();
            }

            if (Role::currentRole() == 'admin' || Role::currentRole() == 'validator') {
                $verifyRows = LemburPegawai::with('lembur.tim')->where('status', 2)->get();
                $lemburToVerify = $verifyRows->count();

                // Kelompokkan per tim, hitung jumlah per tim
                $lemburToVerifyDetail = $verifyRows
                    ->groupBy(fn($lp) => $lp->lembur->tim->label ?? '-')
                    ->map(fn($group, $timLabel) => [
                        'tim_kerja' => $timLabel,
                        'jumlah'    => $group->count(),
                    ])
                    ->values();
            }

            $pendingOutputCount = $lemburData->count();
            $pendingOutputs = $lemburData->map(function ($lp) {
                $isLintas = $lp->lembur->tim_id === null;
                $timLabel = $isLintas
                    ? 'Lintas Tim (PJ: ' . ($lp->lembur->timPenanggungJawab->label ?? '-') . ')'
                    : ($lp->lembur->tim->label ?? '-');
                return [
                    'id'            => $lp->id,
                    'maksud_lembur' => $lp->lembur->maksud_lembur ?? '-',
                    'tim_kerja'     => $timLabel,
                ];
            });
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => Auth::user(),
                'role' => Role::currentRole(),
                'keanggotaan' => Role::statusKeanggotaan()
            ],
            'route' => $route,
            'pendingOutputCount' => $pendingOutputCount,
            'pendingOutputs' => $pendingOutputs,
            'lemburPending' => $lemburPending,
            'lemburPendingDetail' => $lemburPendingDetail,
            'lemburToVerify' => $lemburToVerify,
            'lemburToVerifyDetail' => $lemburToVerifyDetail,
            "flash" => [
                "success" => fn() => $request->session()->get("success"),
                "error" => fn() => $request->session()->get("error"),
                "warning" => fn() => $request->session()->get("warning"),
                "info" => fn() => $request->session()->get("info"),
                'notification' => fn() => $request->session()->get("notification")
            ],

        ];
    }
}
