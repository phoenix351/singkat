<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class SsoController extends Controller
{
    //
    /**
     * Dapatkan URL dasar OIDC realm Keycloak SSO BPS.
     */
    protected function getBaseRealmUrl(): string
    {
        $baseUrl = rtrim(config('services.sso.base_url', 'https://accounts.bps.go.id'), '/');
        $realm = config('services.sso.realm', 'pegawai');
        return "{$baseUrl}/realms/{$realm}/protocol/openid-connect";
    }

    protected function getAuthEndpoint(): string
    {
        return $this->getBaseRealmUrl() . '/auth';
    }

    protected function getTokenEndpoint(): string
    {
        return $this->getBaseRealmUrl() . '/token';
    }

    protected function getUserinfoEndpoint(): string
    {
        return $this->getBaseRealmUrl() . '/userinfo';
    }

    protected function getLogoutEndpoint(): string
    {
        return $this->getBaseRealmUrl() . '/logout';
    }

    protected function getApiEndpoint(): string
    {
        $baseUrl = rtrim(config('services.sso.base_url', 'https://accounts.bps.go.id'), '/');
        $realm = config('services.sso.realm', 'pegawai');
        return "{$baseUrl}/realms/{$realm}/api";
    }

    /**
     * Step 1: Inisiasi Login SSO (Redirect ke Keycloak Login Page).
     */
    public function ssoRedirect(Request $request)
    {
        // Support jika dipanggil dengan parameter ?action=logout seperti pada template
        if ($request->query('action') === 'logout') {
            return $this->ssoLogout($request);
        }

        $from = $request->query('from') ?? $request->query('source');
        $ssoSource = ($from === 'nuxt' || $from === 'v2') ? 'nuxt' : 'web';
        $state = ($ssoSource === 'nuxt' ? 'nuxt_' : '') . bin2hex(random_bytes(16));

        $request->session()->put('oauth_state', $state);
        $request->session()->put('state', $state); // fallback kompatibilitas
        $request->session()->put('sso_source', $ssoSource);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.sso.client_id'),
            'redirect_uri' => config('services.sso.redirect_uri'),
            'scope' => 'openid profile email',
            'state' => $state,
        ]);

        return redirect($this->getAuthEndpoint() . '?' . $query);
    }

    /**
     * Return the SSO authorization URL as JSON for Nuxt SPA.
     * Note: state parameter dikelola di sisi Nuxt via cookie & session backend.
     */
    public function getSsoUrl(Request $request): JsonResponse
    {
        $state = 'nuxt_' . bin2hex(random_bytes(16));

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.sso.client_id'),
            'redirect_uri' => config('services.sso.redirect_uri'),
            'scope' => 'openid profile email',
            'state' => $state,
        ]);

        $url = $this->getAuthEndpoint() . '?' . $query;

        return response()->json([
            'url' => $url,
            'state' => $state,
        ]);
    }

    /**
     * Step 2: Menerima Authorization Code & Tukar dengan Access Token lalu ambil UserInfo.
     */
    public function ssoCallback(Request $request)
    {
        $sessionState = $request->session()->pull('oauth_state') ?? $request->session()->pull('state');
        $ssoSource = $request->session()->pull('sso_source');

        // Deteksi apakah request SSO berasal dari Nuxt (v2) atau Karlota Biasa (Web)
        $isNuxt = ($ssoSource === 'nuxt') || Str::startsWith($request->state ?? '', 'nuxt_');
        $nuxtUrl = config('services.nuxt_url', 'http://localhost:8000/v2');

        // Validasi state (mencegah login CSRF) untuk Web session
        if (!$isNuxt) {
            $expectedState = (string) $sessionState;
            $receivedState = (string) $request->input('state', '');

            if ($expectedState === '' || !hash_equals($expectedState, $receivedState)) {
                return redirect()->route('login')->with('error', 'State tidak valid. Silakan ulangi login.');
            }
        }

        if (!$request->has('code')) {
            return redirect()->route('login')->with('error', 'Kode otorisasi tidak ditemukan dari SSO.');
        }

        // Body parameter untuk token request (Authorization Code Flow)
        $tokenParams = [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.sso.client_id'),
            'client_secret' => config('services.sso.client_secret'),
            'code' => $request->code,
            'redirect_uri' => config('services.sso.redirect_uri'),
        ];

        $response = Http::asForm()->post($this->getTokenEndpoint(), $tokenParams);

        if ($response->failed()) {
            return redirect()->route('login')->with('error', 'Gagal melakukan login SSO (token exchange failed).');
        }

        $tokens = $response->json();
        $accessToken = $tokens['access_token'] ?? null;
        $idToken = $tokens['id_token'] ?? null;

        if (!$accessToken) {
            return redirect()->route('login')->with('error', 'Gagal melakukan login SSO (no access token).');
        }

        // Simpan token di session jika dari Karlota biasa
        if (!$isNuxt) {
            session([
                'access_token' => $accessToken,
                'id_token' => $idToken,
            ]);
            if (isset($tokens['refresh_token'])) {
                session(['refresh_token' => $tokens['refresh_token']]);
            }
        }

        // Ambil UserInfo dari Keycloak
        $userInfoResponse = Http::withToken($accessToken)
            ->get($this->getUserinfoEndpoint());

        if ($userInfoResponse->failed()) {
            if ($isNuxt) {
                return redirect($nuxtUrl . '/login?sso_error=userinfo_failed');
            }
            return redirect()->route('login')->with('error', 'Gagal mengambil data user dari SSO.');
        }

        $userInfo = $userInfoResponse->json();
        // dd($userInfo);
        if (!$isNuxt) {
            session(['user' => $userInfo]);
        }

        // Ekstraksi data pengguna dari UserInfo
        $nipLama = $userInfo['nip-lama']
            ?? $userInfo['nip_lama']
            ?? $userInfo['niplama']
            ?? ($userInfo['attributes']['attribute-nip-lama'][0] ?? null)
            ?? ($userInfo['attributes']['nip-lama'][0] ?? null)
            ?? null;

        $email = $userInfo['email'] ?? null;
        $username = $userInfo['preferred_username'] ?? $userInfo['username'] ?? null;

        // Pencarian user lokal: prioritas nip_lama, lalu email, lalu username
        $current_user = null;
        if ($nipLama) {
            $current_user = Pegawai::where('nip_lama', $nipLama)->first();
        }
        if (!$current_user && $email) {
            $current_user = Pegawai::where('email', $email)->first();
        }
        if (!$current_user && $username) {
            $current_user = Pegawai::where('name', $username)->first();
        }

        if (!$current_user) {
            if ($isNuxt) {
                return redirect($nuxtUrl . '/login?sso_error=user_not_registered');
            }
            return redirect()->route('login')->with('error', 'Akun SSO mu belum terdaftar, tambahkan NIP lama di profilmu');
        }

        // Update nip_lama jika di profil lokal masih kosong
        if (empty($current_user->nip_lama) && $nipLama) {
            $current_user->nip_lama = $nipLama;
            $current_user->save();
        }

        // Flow untuk Karlota Biasa (Web session): Login user dan redirect ke dashboard
        Auth::login($current_user);
        $request->session()->regenerate();
        return redirect()->intended(route('index', absolute: false));
    }

    /**
     * Logout: OIDC RP-Initiated Logout
     */
    public function ssoLogout(Request $request)
    {
        $idToken = session('id_token') ?? $request->query('id_token_hint');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $postLogoutRedirectUri = config('services.sso.redirect_uri', url('/'));
        // Jika redirect_uri mengarah ke /sso-callback, arahkan kembali ke root web (/)
        if (str_contains($postLogoutRedirectUri, 'sso-callback')) {
            $postLogoutRedirectUri = url('/');
        }

        $logoutUrl = $this->getLogoutEndpoint() . '?post_logout_redirect_uri=' . urlencode($postLogoutRedirectUri);
        if ($idToken) {
            $logoutUrl .= '&id_token_hint=' . urlencode($idToken);
        }

        return redirect()->away($logoutUrl);
    }

    public function getTokenAPI()
    {
        $client_id = config('services.sso.client_id');
        $client_secret = config('services.sso.client_secret');
        $url_token = $this->getTokenEndpoint();

        $response = Http::asForm()
            ->withBasicAuth($client_id, $client_secret)
            ->post($url_token, [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            $json = $response->json();
            return $json['access_token'] ?? null;
        }

        throw new \Exception('Gagal mendapatkan access token: ' . $response->body());
    }

    public function ssoAPI(Request $request)
    {
        try {
            // Tentukan query endpoint berdasarkan kriteria:
            // /username/{val}, /email/{val}, /nip/{val}, /nipbaru/{val}, /unit/{val}
            $path = null;
            if ($request->filled('username')) {
                $path = '/username/' . urlencode($request->input('username'));
            } elseif ($request->filled('email')) {
                $path = '/email/' . urlencode($request->input('email'));
            } elseif ($request->filled('nip') || $request->filled('nip_lama')) {
                $path = '/nip/' . urlencode($request->input('nip') ?? $request->input('nip_lama'));
            } elseif ($request->filled('nipbaru') || $request->filled('nip_baru')) {
                $path = '/nipbaru/' . urlencode($request->input('nipbaru') ?? $request->input('nip_baru'));
            } elseif ($request->filled('unit')) {
                $path = '/unit/' . urlencode($request->input('unit'));
            } elseif ($request->filled('search')) {
                $search = trim($request->input('search'));
                if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
                    $path = '/email/' . urlencode($search);
                } elseif (preg_match('/^\d{18}$/', $search)) {
                    $path = '/nipbaru/' . urlencode($search);
                } elseif (preg_match('/^\d{9}$/', $search)) {
                    $path = '/nip/' . urlencode($search);
                } else {
                    $path = '/username/' . urlencode($search);
                }
            }

            if (!$path) {
                return response()->json(['error' => 'Parameter pencarian (username, email, nip, nipbaru, unit) wajib diisi.'], 422);
            }

            // 1. Panggil endpoint REST API SSO baru ({url_base}/api/{criteria})
            $token = $this->getTokenAPI();
            $apiUrl = $this->getApiEndpoint() . $path;

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($apiUrl);

            // 2. Fallback ke legacy SSO jika server baru mengembalikan 404 dan parameter adalah username
            if ($response->failed() && $request->filled('username')) {
                $legacyClientId = config('services.sso.legacy_client_id');
                $legacyClientSecret = config('services.sso.legacy_client_secret');
                if ($legacyClientId && $legacyClientSecret) {
                    $legacyTokenResponse = Http::asForm()
                        ->withBasicAuth($legacyClientId, $legacyClientSecret)
                        ->post('https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/token', [
                            'grant_type' => 'client_credentials',
                        ]);
                    if ($legacyTokenResponse->successful()) {
                        $legacyToken = $legacyTokenResponse->json()['access_token'] ?? null;
                        if ($legacyToken) {
                            $legacyUrl = 'https://sso.bps.go.id/auth/realms/pegawai-bps/api-pegawai/username/' . urlencode($request->input('username'));
                            $legacyRes = Http::withToken($legacyToken)->acceptJson()->get($legacyUrl);
                            if ($legacyRes->successful()) {
                                return response()->json($legacyRes->json());
                            }
                        }
                    }
                }

                return response()->json([], 404);
            }

            if ($response->failed()) {
                return response()->json([], $response->status());
            }

            $results = $response->json();
            if (!is_array($results) || isset($results['error'])) {
                return response()->json([], 404);
            }

            // Normalisasi data agar kompatibel dengan frontend yang mengakses attribute-nip-lama & top-level email
            foreach ($results as &$user) {
                $attrs = $user['attributes'] ?? [];

                // Pastikan top-level email ada untuk kompatibilitas frontend
                if (!isset($user['email'])) {
                    $user['email'] = $attrs['email'][0] ?? null;
                }

                // Pastikan attribute-nip-lama ada untuk kompatibilitas frontend (dari employeeID/nip)
                if (!isset($user['attributes']['attribute-nip-lama'])) {
                    $nipLama = $attrs['employeeID'][0] ?? $attrs['nip'][0] ?? null;
                    if ($nipLama) {
                        $user['attributes']['attribute-nip-lama'] = [$nipLama];
                    }
                }
            }

            return response()->json($results);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
