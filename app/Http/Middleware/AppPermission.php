<?php

namespace App\Http\Middleware;

use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\AppManagement;
use App\Models\ManManagement\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class AppPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $prefix = Route::current()->getPrefix();
        $user = Auth::user();
        $app = AppManagement::where('prefix', $prefix)->first();
        if (!$app) abort(403, 'Aplikasi tidak ditemukan');
        $roles_on_app = Role::where('app_id', $app->id)->get();
        $isPermissable = false;

        $all = $roles_on_app->where('type', 'all')->first();
        if ($all) $isPermissable = true;

        $keanggotaan = AnggotaTimKerja::where('pegawai_id', $user->id)->pluck('tim_id')->toArray();
        $tim = $roles_on_app->where('type', 'tim')->whereIn('to_role_id', $keanggotaan);
        if ($tim->isNotEmpty()) $isPermissable = true;

        $unit = $roles_on_app->where('type', 'unit')->where('to_role_id', $user->id)->first();
        if ($unit) $isPermissable = true;

        if ($isPermissable)
            return $next($request);

        abort(403, 'Anda tidak memiliki hak akses aplikasi ini');
    }
}
