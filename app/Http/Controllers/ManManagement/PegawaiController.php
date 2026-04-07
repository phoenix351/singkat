<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai as ManManagementPegawai;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    //
    public function getCurrentPegawai()
    {
        $pegawai = Pegawai::pluck('nip_bps')->unique()->toArray();
        return response()->json($pegawai);
    }

    public function checkPegawai(String $nip)
    {
        $pegawai = ManManagementPegawai::where('nip_lama', $nip)->first();
        if (!$pegawai) return response()->json(['message' => 'not found']);
        return response()->json($pegawai);
    }

    public function uploadPegawai(Request $request)
    {
        $lc = app(LoginController::class);
        $result = $lc->ssoAPI($request->nip);
        $data = $result[0]['attributes'] ?? null;
        if (!$data) return response()->json(['message' => 'failed']);
        $payload = [
            'nip_lama'   => $data['attribute-nip-lama'][0] ?? null,
            'nip'        => $data['attribute-nip'][0] ?? null,
            'username'   => $data['attribute-username'][0] ?? null,
            'email'      => $data['attribute-email'][0] ?? null,
            'name'       => $data['attribute-nama'][0] ?? null,
            'golongan'   => $data['attribute-golongan'][0] ?? null,
            'jabatan'    => $data['attribute-jabatan'][0] ?? null,
            'organisasi' => $data['attribute-organisasi'][0] ?? null,
            'provinsi'   => $data['attribute-provinsi'][0] ?? null,
            'kabupaten'  => $data['attribute-kabupaten'][0] ?? null,
            'foto'       => $data['attribute-foto'][0] ?? null,
        ];
        if (
            !$payload['nip_lama'] ||
            !$payload['nip'] ||
            !$payload['username'] ||
            !$payload['email'] ||
            !$payload['name'] ||
            !$payload['organisasi'] ||
            !$payload['provinsi'] ||
            !$payload['kabupaten']
        ) {
            return response()->json(['message' => 'failed']);
        }
        if ($payload['provinsi'] !== 'Sulawesi Utara') {
            return response()->json(['message' => 'failed']);
        }
        ManManagementPegawai::updateOrCreate(
            ['nip_lama' => $payload['nip_lama']],
            $payload
        );

        return response()->json(['message' => 'success']);
    }
}
