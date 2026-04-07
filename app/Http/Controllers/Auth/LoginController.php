<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Singkat\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\Exceptions\InvalidArgumentException;

class LoginController extends Controller
{

    public function sso_redirect(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));
        $query = http_build_query([
            'client_id' => config("services.sso.client_id"),
            'client_secret' => config("services.sso.client_secret"),
            // 'realm' => 'pegawai-bps',
            'scope' => 'profile-pegawai email',
            'redirect_uri' => config("services.sso.redirect_uri"),
            'response_type' => 'code',
            'state' => $state,
            'approval_prompt' => 'auto',

            // 'prompt' => '', // "none", "consent", or "login"
        ]);
        // $client_secret = env("SSO_CLIENT_SECRET");

        // dd($client_secret);

        return redirect('https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/auth?' . $query);
    }

    public function sso_callback(Request $request)
    {

        $state = $request->session()->pull('state');
        $codeVerifier = $request->session()->pull('code_verifier');
        // dd([$request->session()]);
        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );

        $response = Http::asForm()->post('https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config("services.sso.client_id"),
            'client_secret' => config("services.sso.client_secret"),
            'redirect_uri' => config("services.sso.redirect_uri"),
            'code_verifier' => $codeVerifier,
            'code' => $request->code,
        ]);
        $tokens = $response->json();

        // Store the tokens in the session or database
        session(['access_token' => $tokens['access_token']]);
        if (isset($tokens['refresh_token'])) {
            session(['refresh_token' => $tokens['refresh_token']]);
        }
        $accessToken = session('access_token');

        $userInfoResponse = Http::withToken($accessToken)->get('https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/userinfo');

        $userInfo = $userInfoResponse->json();
        // dd($userInfo);

        $user = User::where('pegawai_id', $userInfo['nip-lama'])->first();
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended(route('index', absolute: false));

            // return redirect('/');
        }
        return redirect('/login')->with('errors', 'User belum terdaftar!');
    }

    public function getTokenAPI()
    {
        $client_id     = config('services.sso.client_id');
        $client_secret = config('services.sso.client_secret');
        $url_token     = 'https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/token';

        $response = Http::asForm()
            ->withBasicAuth($client_id, $client_secret)
            ->post($url_token, ['grant_type' => 'client_credentials']);
        if ($response->failed()) {
            throw new \Exception('Gagal mendapatkan access token: ' . $response->body());
        }
        $json = $response->json();
        return $json['access_token'] ?? null;
    }

    public function ssoAPI(String $nip_lama)
    {
        $token = $this->getTokenAPI();
        $url_api = 'https://sso.bps.go.id/auth/realms/pegawai-bps/api-pegawai/nip/' . $nip_lama;
        $response = Http::withToken($token)
            ->acceptJson()
            ->get($url_api);
        if ($response->failed()) {
            throw new \Exception('Gagal request API: ' . $response->body());
        }
        $json = $response->json();
        // dd($json);
        return $response->json();
    }
    
}
