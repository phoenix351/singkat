<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Pegawai;
use App\Models\ManManagement\Role;
use App\Models\ManManagement\TimKerja;
use App\Models\Simple\LaporanUpload;
use App\Models\Simple\Lembur;
use App\Models\Simple\LemburPegawai;
use App\Models\Simple\Spkl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\TemplateProcessor;

class SpklController extends Controller
{
    //
    public function index(Request $request)
    {
        $paginated = $request->paginated ?: 10;
        $currentPage = $request->currentPage ?: 1;

        $filters = $request->input('filters', []);
        $tahun = $filters['tahun'] ?? date('Y');
        $bulan = $filters['bulan'] ?? date('n');
        $pegawai = $filters['pegawai'] ?? null;

        $query = LemburPegawai::from('sulutweb_simple.lembur_pegawai')
            ->where('status', 4)
            ->join('sulutweb_man_management.pegawai as sp', 'sp.id', 'lembur_pegawai.pegawai_id')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);
        $query->orderBy('sp.name', 'asc')->orderBy('tanggal', 'asc');
        if ($pegawai) {
            $query->where('sp.name', 'like', '%' . $pegawai . '%');
        }

        $query->with(['pegawai', 'lembur.tim', 'lembur.spkl']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);
        $tim_kerja = TimKerja::orderBy('label')->select(['id as value', 'label'])->get()
            ->map(function ($item) {
                return ['value' => $item->value, 'label' => 'Ketua ' . $item->label];
            });
        if ($request->paginated)
            return response()->json($lembur);
        return Inertia::render('Simple/Spkl', [
            'lembur' => $lembur,
            'tim_kerja' => $tim_kerja
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
            'tahun_dipa' => 'required',
            'tanggal_pengajuan' => 'required',
            'nomor_spkl' => 'required',
            'ttd_rekap' => 'required',
        ], [
            'bulan.required' => 'Bulan wajib diisi',
            'tahun.required' => 'Tahun wajib diisi',
            'tahun_dipa.required' => 'Tahun DIPA wajib diisi',
            'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi',
            'nomor_spkl.required' => 'Nomor SPKL wajib diisi',
            'ttd_rekap.required' => 'Ttd di Rekap Presensi wajib diisi',
        ]);

        $validated['tanggal_pengajuan'] = Carbon::parse($validated['tanggal_pengajuan'])->setTimezone('Asia/Makassar')->format('Y-m-d');
        $lembur = LemburPegawai::where('status', 4)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->with(['pegawai', 'lembur'])
            ->get();
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $lembur_id = $lembur->pluck('lembur_id')->toArray();
            $spkl = Spkl::updateOrCreate(['nomor_spkl' => $validated['nomor_spkl']], $validated);
            $result = ['spkl_id' => $spkl->id];
            $lembur_to_update = Lembur::whereIn('id', $lembur_id);
            $lembur_to_update->update($result);
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.spkl')->with('success', 'Berhasil mencetak dokumen spkl');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollback();
            return redirect()->route('simple.spkl')->with('error', 'Ada kesalahan ketika mencetak dokumen, error : ' . $th->getMessage());
        }
    }

    public function patch(Request $request)
    {
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $data = $request->data;
            foreach ($data as $key => $value) {
                # code...
                $tgl = Carbon::parse($value['tanggal'])->setTimezone('Asia/Makassar')->format('Y-m-d');
                $pegawai = Pegawai::where('nip_lama', $value['nip'])->first();
                if ($pegawai) {
                    $result = [
                        'jam_berangkat' => $value['jam_masuk'],
                        'jam_pulang' => $value['jam_pulang']
                    ];
                    $lp_to_update = LemburPegawai::where('pegawai_id', $pegawai->id)
                        ->where('tanggal', $tgl);
                    $lp_to_update->update($result);
                }
            }
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.spkl')->with('success', 'Berhasil upload presensi');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollback();
            return redirect()->route('simple.spkl')->with('error', 'Ada kesalahan ketika upload presensi, error : ' . $th->getMessage());
        }
    }

    public function print(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
            'tahun_dipa' => 'required',
            'tanggal_pengajuan' => 'required',
            'nomor_spkl' => 'required',
        ], [
            'bulan.required' => 'Bulan wajib diisi',
            'tahun.required' => 'Tahun wajib diisi',
            'tahun_dipa.required' => 'Tahun DIPA wajib diisi',
            'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi',
            'nomor_spkl.required' => 'Nomor SPKL wajib diisi',
        ]);

        $query = LemburPegawai::from('sulutweb_simple.lembur_pegawai')
            ->where('status', 4)
            ->join('sulutweb_man_management.pegawai as sp', 'sp.id', 'lembur_pegawai.pegawai_id')
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->with(['pegawai', 'lembur']);
        $query->orderBy('sp.name', 'asc')->orderBy('tanggal', 'asc');
        $lembur = $query->get()->groupBy('pegawai_id');

        $kpaId = Role::where('roles', 'kaprov')->value('to_role_id');
        $kpa = $kpaId ? Pegawai::find($kpaId) : null;

        $template_path = public_path('document/template_spkl.docx');
        $template_processor = new TemplateProcessor($template_path);

        $template_processor->setValue('nomor_spkl', $request->nomor_spkl);
        $template_processor->setValue('year', $request->tahun_dipa);

        $tanggal = Carbon::parse($request->tanggal_pengajuan)->locale('id')
            ->translatedFormat('j F Y');
        $template_processor->setValue('tanggal', $tanggal);

        $table = new Table([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
        ]);
        $table->addRow();
        $table->addCell(500)->addText('No.', ['bold' => true]);
        $table->addCell(2000)->addText('Nama Pegawai', ['bold' => true]);
        $table->addCell(2500)->addText('Jabatan', ['bold' => true]);
        $table->addCell(2000)->addText('Tanggal', ['bold' => true]);
        $table->addCell(3000)->addText('Alasan Lembur', ['bold' => true]);

        $presensi = new Table([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
        ]);

        $presensi->addRow();
        $presensi->addCell(500)->addText('No.', ['bold' => true]);
        $presensi->addCell(2000)->addText('Nama Pegawai', ['bold' => true]);
        $presensi->addCell(3000)->addText('NIP', ['bold' => true]);
        $presensi->addCell(2000)->addText('Hari, Tanggal', ['bold' => true]);
        $presensi->addCell(2000)->addText('Presensi Masuk', ['bold' => true]);
        $presensi->addCell(2000)->addText('Presensi Pulang', ['bold' => true]);
        $presensi->addCell(2000)->addText('Lamanya Lembur', ['bold' => true]);

        $noPresensi = 1;
        $no = 1;
        foreach ($lembur as $pegawaiId => $items) {
            foreach ($items as $index => $i) {
                //spkl
                $table->addRow();
                if ($index == 0) {
                    $table->addCell(500, ['vMerge' => 'restart'])->addText($no++);
                    $table->addCell(2000, ['vMerge' => 'restart'])->addText($i->pegawai->name);
                    $table->addCell(2500, ['vMerge' => 'restart'])->addText($i->pegawai->jabatan);
                } else {
                    $table->addCell(500, ['vMerge' => 'continue']);
                    $table->addCell(2000, ['vMerge' => 'continue']);
                    $table->addCell(2500, ['vMerge' => 'continue']);
                }
                $table->addCell(2000)->addText(Carbon::parse($i->tanggal)->locale('id')->translatedFormat('j F Y'));
                $table->addCell(3000)->addText($i->lembur->maksud_lembur);

                //presensi
                $presensi->addRow();
                if ($index == 0) {
                    $presensi->addCell(500, ['vMerge' => 'restart'])->addText($noPresensi++);
                    $presensi->addCell(2000, ['vMerge' => 'restart'])->addText($i->pegawai->name);
                    $presensi->addCell(3000, ['vMerge' => 'restart'])
                        ->addText($i->pegawai->nip_lama . '/' . $i->pegawai->nip);
                } else {
                    $presensi->addCell(500, ['vMerge' => 'continue']);
                    $presensi->addCell(2000, ['vMerge' => 'continue']);
                    $presensi->addCell(3000, ['vMerge' => 'continue']);
                }
                $tgl = Carbon::parse($i->tanggal)->locale('id');
                $presensi->addCell(2000)->addText($tgl->translatedFormat('l, j F Y'));

                $jam_berangkat = $i->jam_berangkat;
                $jam_pulang = $i->jam_pulang;

                $dayOfWeek = $tgl->dayOfWeekIso;

                if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
                    $jam_berangkat = '16:00:00';
                } elseif ($dayOfWeek == 5) {
                    $jam_berangkat = '16:30:00';
                }
                $lamanya = 0;
                if ($jam_berangkat && $jam_pulang) {
                    $masuk = Carbon::parse($jam_berangkat);
                    $pulang = Carbon::parse($jam_pulang);

                    if ($pulang->lessThan($masuk)) {
                        $pulang->addDay();
                    }

                    $lamanya = round($pulang->diffInMinutes($masuk) / 60, 2);
                }

                $tampil_berangkat = $jam_berangkat ? Carbon::parse($jam_berangkat)->format('H:i') : '-';
                $tampil_pulang = $jam_pulang ? Carbon::parse($jam_pulang)->format('H:i') : '-';
                $presensi->addCell(2000)->addText($tampil_berangkat);
                $presensi->addCell(2000)->addText($tampil_pulang);
                $presensi->addCell(2000)->addText(str_replace('.', ',', (string) abs($lamanya)));
            }
        }
        $template_processor->setValue('kpa', $kpa ? $kpa->name : '-');
        $template_processor->setComplexBlock('table', $table);
        $template_processor->setValue('presensiMonth', $request->bulan);
        $template_processor->setValue('presensiYear', $request->tahun);
        $template_processor->setComplexBlock('presensiData', $presensi);

        //ttd rekap
        $ttd_rekap = TimKerja::where('id', $request->ttd_rekap)->value('label');
        $ketua_ttd_rekap = AnggotaTimKerja::where('tim_id', $request->ttd_rekap)->where('keanggotaan', 'ketua')->with(['pegawai'])->first();
        $template_processor->setValue('ttd_rekap', $ttd_rekap ?? '-');
        $template_processor->setValue('ketua_ttd_rekap', $ketua_ttd_rekap->pegawai->name ?? '-');

        $filename = "SPKL_" . $request->bulan . "_" . $request->tahun . ".docx";
        if (ob_get_length())
            ob_end_clean();
        return response()->streamDownload(function () use ($template_processor) {
            $template_processor->saveAs('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function laporan(Request $request)
    {
        $filters = $request->input('filters', []);
        $tahun = $filters['tahun'] ?? date('Y');
        $bulan = $filters['bulan'] ?? date('n');

        $myTeam = AnggotaTimKerja::where('pegawai_id', Auth::id())->pluck('tim_id')->toArray();
        $query = LemburPegawai::query();
        $query->where('status', 4);
        $query->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);
        $role = Role::currentRole();
        $isOpen = ['admin', 'operator'];
        if (!in_array($role, $isOpen)) {
            $query->whereHas('lembur', function ($q) use ($myTeam) {
                $q->whereIn('tim_id', $myTeam)
                  ->orWhereIn('tim_penanggung_jawab_id', $myTeam);
            });
        }

        $query->with(['lembur.tim', 'lembur.timPenanggungJawab', 'lembur.spkl']);
        $lp = $query->get()->groupBy(function ($item) {
                return $item->lembur->tim->label
                    ?? $item->lembur->timPenanggungJawab->label
                    ?? 'Tidak Ada Tim';
            })
            ->map(function ($group, $key) {
                $first_item = $group->first();
                $upload_status = LaporanUpload::where('lembur_id', $first_item->lembur_id)->first();
                $tim_id = $first_item->lembur->tim_id ?? $first_item->lembur->tim_penanggung_jawab_id;
                $result = [
                    'tim' => $key,
                    'tim_id' => $tim_id,
                    'jumlah' => $group->count(),
                    'lembur_id' => $first_item->lembur_id,
                    'maksud_lembur' => $first_item->lembur->maksud_lembur ?? null,
                    'link_dokumentasi' => $first_item->lembur->link_dokumentasi ?? null,
                    'no_spkl' => $first_item->lembur->spkl->nomor_spkl ?? null,
                    'upload_status' => $upload_status ? $upload_status->updated_at : null,
                    'file_path' => $upload_status->file_path ?? null,
                ];
                return $result;
            })->values();
        if ($request->filters)
            return response()->json($lp);
        return Inertia::render('Simple/Laporan', [
            'lembur' => $lp
        ]);
    }

    public function uploadLaporan(Request $request)
    {
        $validated = $request->validate([
            'lembur_id' => ['integer', 'required'],
            'file' => ['required', 'file', 'mimes:docx,pdf', 'max:3000']
        ]);
        $file = $request->file('file');
        $new_path = null;
        $current_upload = LaporanUpload::where('lembur_id', $validated['lembur_id'])->first();
        $old_path = $current_upload ? $current_upload->file_path : null;
        try {
            DB::connection('sulutweb_simple')->beginTransaction();
            if ($file) {
                $ext = $file->getClientOriginalExtension();
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($originalName) . '-' . time() . '.' . $ext;
                $disk = 'public';
                $folder = 'file/simple/laporan_lembur';
                $new_path = $file->storeAs($folder, $filename, $disk);
                $validated['file_path'] = $new_path;
            }
            unset($validated['file']);
            if ($current_upload) {
                $current_upload->update($validated);
                Storage::disk('public')->delete($old_path);
            } else
                LaporanUpload::create($validated);
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.laporan-lembur')->with('success', 'Berhasil kirim/upload file');
        } catch (\Throwable $th) {
            DB::connection('sulutweb_simple')->rollBack();
            if ($new_path && Storage::disk('public')->exists($new_path)) {
                Storage::disk('public')->delete($new_path);
            }
            return redirect()->route('simple.laporan-lembur')->with('error', 'Gagal kirim/upload file, error: ' . $th->getMessage());
        }
    }

    public function printLaporan(Request $request)
    {
        $lembur_id = $request->input('lembur_id') ?? null;
        if ($lembur_id) {
            $lembur = Lembur::where('id', $lembur_id)->with(['tim', 'timPenanggungJawab'])->first();
            $tim_ref = $lembur->tim ?? $lembur->timPenanggungJawab;
            $nama_tim = $tim_ref ? $tim_ref->label : 'Lintas Tim Kerja';
            $tahun = $request->input('tahun');
            $bulan = $request->input('bulan');

            $ketua_tim_id = AnggotaTimKerja::where('tim_id', $tim_ref->id)->where('keanggotaan', 'ketua')->first();
            $anggota_tim = AnggotaTimKerja::where('tim_id', $tim_ref->id)->pluck('pegawai_id')->toArray();
            $ketua_tim = Pegawai::findOrFail($ketua_tim_id->pegawai_id);

            $query = LemburPegawai::from('sulutweb_simple.lembur_pegawai')
                ->join('sulutweb_man_management.pegawai as sp', 'sp.id', 'lembur_pegawai.pegawai_id')
                ->where('status', 4)
                ->where(function ($q) use ($tim_ref, $anggota_tim) {
                    // Kondisi 1: Lembur biasa (tim_id cocok) DAN pegawai adalah anggota tim
                    $q->where(function ($q1) use ($tim_ref, $anggota_tim) {
                        $q1->whereIn('pegawai_id', $anggota_tim)
                           ->whereHas('lembur', function ($q_lembur) use ($tim_ref) {
                               $q_lembur->where('tim_id', $tim_ref->id);
                           });
                    })
                    // Kondisi 2: Lembur lintas tim (tim_penanggung_jawab_id cocok) -> SEMUA pegawai di pengajuan masuk
                    ->orWhereHas('lembur', function ($q_lembur) use ($tim_ref) {
                        $q_lembur->where('tim_penanggung_jawab_id', $tim_ref->id);
                    });
                })
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)->with(['pegawai', 'lembur'])
                ->orderBy('sp.name', 'asc');
            $lembur = $query->get()->groupBy('pegawai_id');

            $template_path = public_path('document/template_laporan lembur.docx');
            $template_processor = new TemplateProcessor($template_path);

            $template_processor->setValue('nama_tim', $nama_tim);
            $template_processor->setValue('bulan', Carbon::create(null, $bulan)->locale('id')->monthName);
            $template_processor->setValue('year', $tahun);
            $tanggal = Carbon::parse($request->tanggal_pengajuan)->locale('id')
                ->translatedFormat('j F Y');
            $template_processor->setValue('tanggal', $tanggal);
            $template_processor->setValue('nama_ketua_tim', $ketua_tim->name);

            $table = new Table([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
                'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
            ]);
            $table->addRow();
            $table->addCell(500)->addText('No.', ['bold' => true]);
            $table->addCell(2000)->addText('Nama Pegawai', ['bold' => true]);
            $table->addCell(2500)->addText('Jabatan', ['bold' => true]);
            $table->addCell(2000)->addText('Tanggal', ['bold' => true]);
            $table->addCell(3000)->addText('Alasan Lembur', ['bold' => true]);
            $table->addCell(3000)->addText('Output', ['bold' => true]);

            $no = 1;
            foreach ($lembur as $pegawaiId => $items) {
                foreach ($items as $index => $i) {
                    $table->addRow();
                    if ($index == 0) {
                        $table->addCell(500, ['vMerge' => 'restart'])->addText($no++);
                        $table->addCell(2000, ['vMerge' => 'restart'])->addText($i->pegawai->name);
                        $table->addCell(2500, ['vMerge' => 'restart'])->addText($i->pegawai->jabatan);
                    } else {
                        $table->addCell(500, ['vMerge' => 'continue']);
                        $table->addCell(2000, ['vMerge' => 'continue']);
                        $table->addCell(2500, ['vMerge' => 'continue']);
                    }
                    $table->addCell(2000)->addText(Carbon::parse($i->tanggal)->locale('id')->translatedFormat('j F Y'));
                    $table->addCell(3000)->addText($i->lembur->maksud_lembur);
                    $table->addCell(3000)->addText($i->output);
                }
            }
            $template_processor->setComplexBlock('table', $table);
            $filename = 'Laporan Lembur ' . $nama_tim . '_' . $bulan . '_' . $tahun . '.docx';
            if (ob_get_length())
                ob_end_clean();
            return response()->streamDownload(function () use ($template_processor) {
                $template_processor->saveAs('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
        }
    }

    public function downloadLaporan($lembur_id)
    {
        $file = LaporanUpload::where('lembur_id', $lembur_id)->first();
        if (!$file || !$file->file_path) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }
        $fullPath = Storage::disk('public')->path($file->file_path);
        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'File fisik tidak ditemukan');
        }
        return response()->download($fullPath);
    }

    public function printKeuangan(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ], [
            'bulan.required' => 'Bulan wajib diisi',
            'tahun.required' => 'Tahun wajib diisi',
        ]);

        $query = LemburPegawai::from('sulutweb_simple.lembur_pegawai')
            ->where('status', 4)
            ->whereNotNull('jam_berangkat')
            ->whereNotNull('jam_pulang')
            ->join('sulutweb_man_management.pegawai as sp', 'sp.id', 'lembur_pegawai.pegawai_id')
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->with(['pegawai', 'lembur']);

        $lembur = $query->get();

        $file1 = $this->buildRekapUang($lembur, $request->bulan, $request->tahun);
        $file2 = $this->buildLemburBos($lembur, $request->bulan, $request->tahun);
        $file3 = $this->buildLemburWebGaji($lembur, $request->bulan, $request->tahun);

        $zip = new \ZipArchive();
        $zipFileName = "Rekap_Lembur_{$request->bulan}_{$request->tahun}.zip";
        $zipFilePath = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            if ($file1)
                $zip->addFile($file1, "Rekap_Uang_{$request->bulan}_{$request->tahun}.xlsx");
            if ($file2)
                $zip->addFile($file2, "Lembur_Bos_{$request->bulan}_{$request->tahun}.xlsx");
            if ($file3)
                $zip->addFile($file3, "Lembur_Web_Gaji_{$request->bulan}_{$request->tahun}.xlsx");
            $zip->close();
            foreach ([$file1, $file2, $file3] as $file) {
                if ($file && file_exists($file)) {
                    unlink($file);
                }
            }

            return response()->download($zipFilePath, $zipFileName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with('error', 'Gagal membuat file arsip ZIP');
        }
    }

    private function buildRekapUang($lembur, $bulan, $tahun)
    {
        $pegawaiMap = [];
        $rekap = [];
        foreach ($lembur as $l) {
            $nip_lama = $l->pegawai->nip_lama;
            $pegawaiMap[$nip_lama] = $l->pegawai->name;
            if (!isset($rekap[$nip_lama])) {
                $rekap[$nip_lama] = [];
            }

            $tgl = Carbon::parse($l->tanggal);
            $dayOfWeek = $tgl->dayOfWeekIso;

            $durasi = $l->jumlah_jam;
            $durasi_final = 0;
            if ($l->jam_berangkat && $l->jam_pulang) {
                $masuk = Carbon::parse($l->jam_berangkat);
                $pulang = Carbon::parse($l->jam_pulang);

                if ($pulang->lessThan($masuk)) {
                    $pulang->addDay();
                }

                if ($dayOfWeek <= 5) {
                    $batas_pulang_str = ($dayOfWeek <= 4) ? '16:00:00' : '16:30:00';
                    $batas_pulang = Carbon::parse($batas_pulang_str);

                    $mulai_lembur = $masuk->greaterThan($batas_pulang) ? $masuk : $batas_pulang;

                    if ($pulang->greaterThan($mulai_lembur)) {
                        $selisih = floor($mulai_lembur->diffInMinutes($pulang) / 60);
                    } else {
                        $selisih = 0;
                    }
                } else {
                    $selisih = floor($masuk->diffInMinutes($pulang) / 60);
                }

                $durasi_final = ($selisih > $durasi) ? $durasi : $selisih;
            }
            if ($durasi_final <= 0)
                continue;

            $tipe = ($dayOfWeek >= 6) ? 'HL' : 'HB';
            $kolom = $tipe . $durasi_final;
            if (!isset($rekap[$nip_lama][$kolom])) {
                $rekap[$nip_lama][$kolom] = 0;
            }
            $rekap[$nip_lama][$kolom]++;
        }

        $template_path = public_path('document/template_rekap utk uang.xlsx');
        if (!file_exists($template_path))
            return null;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($template_path);
        $worksheet = $spreadsheet->getActiveSheet();

        $highestColumn = $worksheet->getHighestColumn();
        $headers = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false)[0];

        $headerMap = [];
        foreach ($headers as $index => $header) {
            if ($header) {
                $headerMap[trim($header)] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            }
        }

        $nipCol = $headerMap['NIP Lama'] ?? 'B';
        $namaCol = $headerMap['Nama Pegawai'] ?? 'A';
        $row = 2;

        foreach ($rekap as $nip_lama => $counts) {
            if (isset($headerMap['Nama Pegawai']) && isset($pegawaiMap[$nip_lama])) {
                $worksheet->setCellValue($namaCol . $row, $pegawaiMap[$nip_lama]);
            }
            $worksheet->setCellValueExplicit($nipCol . $row, $nip_lama, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            foreach ($counts as $kolom => $jumlah) {
                if (isset($headerMap[$kolom])) {
                    $colStr = $headerMap[$kolom];
                    $worksheet->setCellValue($colStr . $row, $jumlah);
                }
            }
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'xls') . '.xlsx';
        $writer->save($tempPath);
        return $tempPath;
    }

    private function buildLemburBos($lembur, $bulan, $tahun)
    {
        $rekap = [];
        foreach ($lembur as $l) {
            $nip_lama = $l->pegawai->nip_lama;
            if (!isset($rekap[$nip_lama])) {
                $rekap[$nip_lama] = [
                    'nama' => $l->pegawai->name,
                    'tanggal' => [],
                    'kegiatan_dates' => []
                ];
            }

            $hari = Carbon::parse($l->tanggal)->format('j');
            if (!in_array($hari, $rekap[$nip_lama]['tanggal'])) {
                $rekap[$nip_lama]['tanggal'][] = $hari;
            }

            $maksud = trim($l->lembur->maksud_lembur);
            if ($maksud) {
                if (!isset($rekap[$nip_lama]['kegiatan_dates'][$maksud])) {
                    $rekap[$nip_lama]['kegiatan_dates'][$maksud] = [];
                }
                if (!in_array($hari, $rekap[$nip_lama]['kegiatan_dates'][$maksud])) {
                    $rekap[$nip_lama]['kegiatan_dates'][$maksud][] = $hari;
                }
            }
        }

        $template_path = public_path('document/template_lembur_bos.xlsx');
        if (!file_exists($template_path))
            return null;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($template_path);
        $worksheet = $spreadsheet->getActiveSheet();

        $row = 2;
        foreach ($rekap as $nip_lama => $data) {
            $kegiatanFormatted = [];
            foreach ($data['kegiatan_dates'] as $kegiatan => $dates) {
                sort($dates, SORT_NUMERIC);
                $kegiatanFormatted[] = $kegiatan . '(' . implode(',', $dates) . ')';
            }

            $worksheet->setCellValueExplicit('A' . $row, $nip_lama, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $worksheet->setCellValue('B' . $row, $data['nama']);
            $worksheet->setCellValueExplicit('C' . $row, implode(',', $data['tanggal']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $worksheet->setCellValueExplicit('D' . $row, implode(', ', $kegiatanFormatted), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'xls') . '.xlsx';
        $writer->save($tempPath);
        return $tempPath;
    }

    private function buildLemburWebGaji($lembur, $bulan, $tahun)
    {
        $rekap = [];
        foreach ($lembur as $l) {
            $nip = $l->pegawai->nip;
            if (!isset($rekap[$nip])) {
                $rekap[$nip] = [
                    'nama' => $l->pegawai->name,
                    'durasi' => []
                ];
            }

            $tgl = Carbon::parse($l->tanggal);
            $hari = $tgl->format('j');
            $dayOfWeek = $tgl->dayOfWeekIso;

            $durasi = $l->jumlah_jam;
            $durasi_final = 0;
            if ($l->jam_berangkat && $l->jam_pulang) {
                $masuk = Carbon::parse($l->jam_berangkat);
                $pulang = Carbon::parse($l->jam_pulang);

                if ($pulang->lessThan($masuk)) {
                    $pulang->addDay();
                }

                if ($dayOfWeek <= 5) {
                    $batas_pulang_str = ($dayOfWeek <= 4) ? '16:00:00' : '16:30:00';
                    $batas_pulang = Carbon::parse($batas_pulang_str);

                    $mulai_lembur = $masuk->greaterThan($batas_pulang) ? $masuk : $batas_pulang;

                    if ($pulang->greaterThan($mulai_lembur)) {
                        $selisih = floor($mulai_lembur->diffInMinutes($pulang) / 60);
                    } else {
                        $selisih = 0;
                    }
                } else {
                    $selisih = floor($masuk->diffInMinutes($pulang) / 60);
                }

                $durasi_final = ($selisih > $durasi) ? $durasi : $selisih;
            }
            if ($durasi_final > 0) {
                if (!isset($rekap[$nip]['durasi'][$hari])) {
                    $rekap[$nip]['durasi'][$hari] = 0;
                }
                $rekap[$nip]['durasi'][$hari] += $durasi_final;
            }
        }

        $template_path = public_path('document/template_lembur_web_gaji.xlsx');
        if (!file_exists($template_path))
            return null;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($template_path);
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValue('C1', $tahun);
        $worksheet->setCellValue('C2', $bulan);

        $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $i);
            $worksheet->setCellValue($colLetter . '3', $i);
        }

        $row = 4;
        $no = 1;
        foreach ($rekap as $nip => $data) {
            $worksheet->setCellValue('A' . $row, $no++);
            $worksheet->setCellValueExplicit('B' . $row, $nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $worksheet->setCellValue('C' . $row, $data['nama']);

            foreach ($data['durasi'] as $hari => $durasi_final) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + (int) $hari);
                $worksheet->setCellValue($colLetter . $row, $durasi_final);
            }
            $row++;
        }

        foreach ($spreadsheet->getDefinedNames() as $definedName) {
            $spreadsheet->removeDefinedName($definedName->getName());
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'xls') . '.xlsx';
        $writer->save($tempPath);
        return $tempPath;
    }
}
