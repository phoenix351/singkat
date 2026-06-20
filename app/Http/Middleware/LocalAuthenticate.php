<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;

class LocalAuthenticate extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Cek jika di lokal dan belum login
        // dd(app()->environment());
        if (app()->environment('local') && !Auth::check()) {
            Auth::loginUsingId(267); // Ganti dengan ID User target
        }

        // Jalankan fungsi handle bawaan (parent)
        return parent::handle($request, $next, ...$guards);
    }

    protected function redirectTo($request)
    {
        return $request->expectsJson() ? null : route('login');
    }
}
