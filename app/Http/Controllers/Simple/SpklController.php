<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai;
use App\Models\ManManagement\Role;
use App\Models\Simple\Lembur;
use App\Models\Simple\LemburPegawai;
use App\Models\Simple\Spkl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $query = LemburPegawai::from('sulutweb_simple.lembur_pegawai')
            ->where('status', 4)
            ->join('sulutweb_man_management.pegawai as sp', 'sp.id', 'lembur_pegawai.pegawai_id')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);
        $query->orderBy('sp.name', 'asc')->orderBy('tanggal', 'asc');

        $query->with(['pegawai', 'lembur.tim', 'lembur.spkl']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);
        if ($request->paginated)
            return response()->json($lembur);
        return Inertia::render('Simple/Spkl', [
            'lembur' => $lembur
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
        ], [
            'bulan.required' => 'Bulan wajib diisi',
            'tahun.required' => 'Tahun wajib diisi',
            'tahun_dipa.required' => 'Tahun DIPA wajib diisi',
            'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi',
            'nomor_spkl.required' => 'Nomor SPKL wajib diisi',
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
        $query->orderBy('sp.name', 'asc');
        $lembur = $query->get()->groupBy('pegawai_id');

        $kpaId = Role::where('roles', 'kaprov')->value('to_role_id');
        $kpa = Pegawai::findOrFail($kpaId);

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
        $table->addCell(3000)->addText('Maksud Lembur', ['bold' => true]);

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

            }
        }
        $template_processor->setValue('kpa', $kpa->name);
        $template_processor->setComplexBlock('table', $table);

        $filename = "SPKL_" . $request->bulan . "_" . $request->tahun . ".docx";
        if (ob_get_length())
            ob_end_clean();
        return response()->streamDownload(function () use ($template_processor) {
            $template_processor->saveAs('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
