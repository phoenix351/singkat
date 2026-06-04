<?php

namespace App\Http\Middleware;

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
        $pendingOutputs = [];
        
        if (Auth::check() && str_starts_with($route, 'simple.')) {
            $lemburData = LemburPegawai::with('lembur.tim')
                ->where('pegawai_id', Auth::id())
                ->whereNull('output')
                ->get();
            
            $pendingOutputCount = $lemburData->count();
            $pendingOutputs = $lemburData->map(function($lp) {
                return [
                    'id' => $lp->id,
                    'maksud_lembur' => $lp->lembur->maksud_lembur ?? '-',
                    'tim_kerja' => $lp->lembur->tim->label ?? '-',
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
