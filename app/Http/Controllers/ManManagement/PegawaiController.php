<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai as ManManagementPegawai;
use App\Models\Pegawai;
use App\Models\TimKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    //
    public function getCurrentPegawai()
    {
        $pegawai = Pegawai::pluck('nip_bps')->unique()->toArray();
        return response()->json($pegawai);
    }

    public function checkPegawai()
    {
        $pegawai = ManManagementPegawai::pluck('nip_lama')->unique()->toArray();
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

    public function tkStore(Request $request)
    {
        if ($request->input('fileUpload')) {
            $fileData = $request->input('fileUpload');
            if ($fileData[0][0] != 'tahun' && $fileData[0][0] != 'label') {
                return redirect()->route('man-management.tim-kerja.index')->with('error', 'File yang diupload tidak sesuai template');
            }
            $notification = [];
            $total = count($fileData) - 1;
            foreach ($fileData as $key => $value) {
                # code...
                if ($key === 0) continue;
                if (!empty($value) && count($value) > 0) {
                    $result = [
                        'tahun' => $value[0] ?? null,
                        'label' => trim($value[1] ?? '') ?? null,
                    ];
                    $validator = Validator::make($result, [
                        'tahun' => ['required', 'date_format:Y'],
                        'label' => ['required', 'string'],
                    ], [
                        'tahun.required' => "Baris ke-" . ($key + 1) . " kolom tahun wajib diisi.",
                        'tahun.date_format' => "Baris ke-" . ($key + 1) . " tahun harus format YYYY.",
                        'label.required' => "Baris ke-" . ($key + 1) . " kolom label wajib diisi.",
                        'label.string' => "Baris ke-" . ($key + 1) . " label harus berupa teks.",
                    ]);
                    if ($validator->fails()) {
                        return redirect()
                            ->route('man-management.tim-kerja.index')
                            ->with('error', $validator->errors()->first());
                    }
                    $check = TimKerja::where('tahun', $result['tahun'])
                        ->where('label', $result['label'])->first();
                    if ($check) {
                        $notification[] = ['type' => 'error', 'message' => 'Tim Kerja baris ke-' . $key + 1 . ' sudah ada di database'];
                    } else
                        TimKerja::create($result);
                }
            }
            if (count($notification) < $total) $notification[] = ['type' => 'success', 'message' => 'Tim Kerja berhasil di upload'];
            return redirect()->route('man-management.tim-kerja.index')->with('notification', $notification);
        }
        $validated = $request->validate([
            'id' => ['sometimes', 'nullable'],
            'tahun' => ['required', 'date_format:Y'],
            'label' => ['required', 'string']
        ]);
        $id = $validated['id'] ?? null;
        $check = TimKerja::where('tahun', $validated['tahun'])
            ->when($id, function ($query) use ($id) {
                $query->where('id', '!=', $id);
            })
            ->where('label', $validated['label'])
            ->first();
        if ($check) return redirect()->route('man-management.tim-kerja.index')->with('error', 'Tim Kerja sudah ada');

        if ($request->isMethod('patch')) {
            try {
                //code...
                DB::beginTransaction();
                $timkerja_to_updated = TimKerja::find($validated['id']);
                if ($timkerja_to_updated) $timkerja_to_updated->update($validated);
                DB::commit();
                return redirect()->route('man-management.tim-kerja.index')->with('success', 'Tim Kerja berhasil diedit');
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                return redirect()->route('man-management.tim-kerja.index')->with('error', 'Terjadi kesalahan dalam data');
            }
        }
        try {
            //code...
            DB::beginTransaction();
            TimKerja::create($validated);
            DB::commit();
            return redirect()->route('man-management.tim-kerja.index')->with('success', 'Tim Kerja berhasil ditambahkan');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->route('man-management.tim-kerja.index')->with('error', 'Terjadi kesalahan dalam data');
        }
    }

    public function tkDestroy($id)
    {
        try {
            //code...
            DB::beginTransaction();
            $tim_kerja_to_destroy = TimKerja::findOrFail($id);
            $tim_kerja_to_destroy->delete();
            DB::commit();
            return redirect()->route('man-management.tim-kerja.index')
                ->with('success', 'Tim Kerja berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->route('man-management.tim-kerja.index')
                ->with('error', 'Tim Kerja gagal dihapus');
        }
    }
}
