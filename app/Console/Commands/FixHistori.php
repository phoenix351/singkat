<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Se2026\HistoryPpl;
use App\Models\Se2026\HistoryPml;
use App\Models\Se2026\HistoryWilayah;
use App\Models\Se2026\DataFasih;
use App\Models\Se2026\DataFasihPml;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FixHistori extends Command
{
    protected $signature = 'fix:histori';
    protected $description = 'Recalculate history for entries that were duplicated.';

    public function handle()
    {
        $this->processSqlFile(public_path('data_fasih_double.sql'), false);
        $this->processSqlFile(public_path('data_fasih_pml_double.sql'), true);
        $this->info('Fix histori complete.');
    }

    private function processSqlFile($filePath, $isPml)
    {
        if (!File::exists($filePath)) {
            $this->warn("File $filePath not found.");
            return;
        }

        $content = File::get($filePath);
        // Match INSERT INTO `data_fasih` (...) VALUES\n(..., 'email', 'subsls_code', ...)
        preg_match_all("/\(\d+,\s*'([^']+)',\s*'([^']+)',/i", $content, $matches);

        $emails = array_unique($matches[1]);
        $subsls_codes = array_unique($matches[2]);

        $this->info("Found " . count($emails) . " unique emails and " . count($subsls_codes) . " unique subsls_codes in " . basename($filePath));

        $emailModel = $isPml ? HistoryPml::class : HistoryPpl::class;
        $dataModel = $isPml ? DataFasihPml::class : DataFasih::class;

        $fields = ['open', 'draft', 'submitted_p', 'approved', 'rejected', 'submitted_r', 'revoked', 'completed', 'edited_a', 'rejected_a'];
        $sqlSum = implode(', ', array_map(fn($col) => "SUM(COALESCE($col, 0)) as $col", $fields));

        // Fix History Wilayah
        foreach ($subsls_codes as $subsls) {
            $latest = HistoryWilayah::where('subsls_code', $subsls)->orderBy('updated_at', 'desc')->first();
            if ($latest) {
                $actualData = $dataModel::where('subsls_code', $subsls)
                    ->selectRaw($sqlSum)
                    ->first();
                if ($actualData) {
                    foreach ($fields as $f) {
                        $latest->$f = $actualData->$f ?? 0;
                    }
                    $latest->save();
                }
            }
        }

        // Fix History Email (Ppl/Pml)
        foreach ($emails as $email) {
            $latest = $emailModel::where('email', $email)->orderBy('updated_at', 'desc')->first();
            if ($latest) {
                $actualData = $dataModel::where('email', $email)
                    ->selectRaw($sqlSum)
                    ->first();
                if ($actualData) {
                    foreach ($fields as $f) {
                        $latest->$f = $actualData->$f ?? 0;
                    }
                    $latest->save();
                }
            }
        }
    }
}
