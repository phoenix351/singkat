<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Cek apakah request berasal dari lingkungan lokal / development.
     */
    protected function isLocalEnvironment(Request $request): bool
    {
        $host = $request->getHost();

        return in_array($host, ['localhost', '127.0.0.1', '::1'])
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local');
    }

    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'isLocal' => $this->isLocalEnvironment($request),
        ]);
    }

    /**
     * Ambil daftar pegawai untuk pilihan Login Lokal (hanya diizinkan di localhost/development).
     */
    public function localUsers(Request $request): JsonResponse
    {
        if (!$this->isLocalEnvironment($request)) {
            return response()->json([
                'message' => 'Akses ditolak. Fitur ini hanya tersedia di lingkungan lokal / development.',
            ], 403);
        }

        $users = Pegawai::select('id', 'name', 'username', 'email', 'nip_lama', 'nip', 'jabatan', 'organisasi')
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                $org = $u->organisasi ?: '-';
                $jabatan = $u->jabatan ?: '-';
                $usernamePart = $u->username ? " ({$u->username})" : '';

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'username' => $u->username ?: '-',
                    'email' => $u->email ?: '-',
                    'nip_lama' => $u->nip_lama ?: '-',
                    'nip' => $u->nip ?: '-',
                    'jabatan' => $jabatan,
                    'organisasi' => $org,
                    'label' => "{$u->name}{$usernamePart} - {$jabatan}",
                ];
            });

        return response()->json($users);
    }

    /**
     * Login lokal dengan pegawai yang dipilih (hanya diizinkan di localhost/development).
     */
    public function localLogin(Request $request): RedirectResponse
    {
        if (!$this->isLocalEnvironment($request)) {
            abort(403, 'Akses ditolak. Fitur ini hanya tersedia di lingkungan lokal / development.');
        }

        $validated = $request->validate([
            'user_id' => 'required',
        ], [
            'user_id.required' => 'Silakan pilih user terlebih dahulu.',
        ]);

        $user = Pegawai::find($validated['user_id']);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/home');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate(
            [
                'login' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'login.required' => 'Email / username tidak boleh kosong.',
                'password.required' => 'Password tidak boleh kosong.'

            ]
        );


        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$login_type => $request->login, 'password' => $request->password])) {
            throw ValidationException::withMessages([
                'login' => [trans('auth.failed')],
            ], [
                'login' => 'Email / username atau password salah.'

            ]);
        }

        $request->session()->regenerate();

        return redirect('/home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Inertia::location('/login');
        // return redirect('/login');
    }
}