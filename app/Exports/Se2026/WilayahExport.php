<?php

namespace App\Exports\Se2026;

use App\Models\Se2026\DataFasih;
use App\Models\Se2026\MasterDesa;
use App\Models\Se2026\MasterKabkot;
use App\Models\Se2026\MasterKec;
use App\Models\Se2026\MasterSubSls;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WilayahExport implements WithMultipleSheets
{
    use Exportable;

    protected $kabkot;

    public function __construct($kabkot)
    {
        $this->kabkot = $kabkot;
    }

    public function sheets(): array
    {
        $fasih = DataFasih::query()
            ->where('subsls_code', 'like', $this->kabkot . '%')
            ->get();

        // Ambil kode unik per level dari subsls_code
        $kecCodes  = $fasih->map(fn($f) => Str::substr((string) $f->subsls_code, 0, 7))->unique()->values()->toArray();
        $desaCodes = $fasih->map(fn($f) => Str::substr((string) $f->subsls_code, 0, 10))->unique()->values()->toArray();
        $slsCodes  = $fasih->pluck('subsls_code')->unique()->values()->toArray();
        $kabCodes  = [$this->kabkot];

        // Query master table sekali, buat lookup map [code => label]
        $kabkotLabels = MasterKabkot::whereIn('code', $kabCodes)->pluck('label', 'code');
        $kecLabels    = MasterKec::whereIn('code', $kecCodes)->pluck('label', 'code');
        $desaLabels   = MasterDesa::whereIn('code', $desaCodes)->pluck('label', 'code');
        $slsLabels    = MasterSubSls::whereIn('code', $slsCodes)->pluck('label', 'code');

        return [
            // KecSheet butuh: kabkotLabels, kecLabels
            new WilayahKecSheet($fasih, $this->kabkot, $kabkotLabels, $kecLabels),
            // DesaSheet butuh: kabkotLabels, kecLabels, desaLabels
            new WilayahDesaSheet($fasih, $this->kabkot, $kabkotLabels, $kecLabels, $desaLabels),
            // SlsSheet butuh: kabkotLabels, kecLabels, desaLabels, slsLabels
            new WilayahSlsSheet($fasih, $this->kabkot, $kabkotLabels, $kecLabels, $desaLabels, $slsLabels),
        ];
    }
}
