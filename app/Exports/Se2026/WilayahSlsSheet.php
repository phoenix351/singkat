<?php

namespace App\Exports\Se2026;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class WilayahSlsSheet extends DefaultValueBinder implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithColumnFormatting,
    WithCustomValueBinder
{
    protected $fasih;
    protected $kabkot;
    protected $kabkotLabels; // [kab_code => label]
    protected $kecLabels;    // [kec_code => label]
    protected $desaLabels;   // [desa_code => label]
    protected $slsLabels;    // [subsls_code => label]

    public function __construct(Collection $fasih, $kabkot, $kabkotLabels, $kecLabels, $desaLabels, $slsLabels)
    {
        $this->fasih        = $fasih;
        $this->kabkot       = $kabkot;
        $this->kabkotLabels = $kabkotLabels;
        $this->kecLabels    = $kecLabels;
        $this->desaLabels   = $desaLabels;
        $this->slsLabels    = $slsLabels;
    }

    public function title(): string
    {
        return 'SLS';
    }

    public function headings(): array
    {
        return [
            'Kode Kab/Kota',
            'Nama Kab/Kota',
            'Kode Kecamatan',
            'Nama Kecamatan',
            'Kode Desa',
            'Nama Desa',
            'Kode SubSLS (16 digit)',
            'Nama SLS',
            'OPEN',
            'DRAFT',
            'SUBMITTED BY PENCACAH',
            'SUBMITTED RESPONDENT',
            'APPROVED BY PENGAWAS',
            'REJECTED BY PENGAWAS',
            'REVOKED BY PENGAWAS',
            'COMPLETED BY ADMIN KABUPATEN',
        ];
    }

    public function collection(): Collection
    {
        // Grup berdasarkan subsls_code penuh (16 digit)
        $grouped = $this->fasih->groupBy(function ($item) {
            return (string) $item->subsls_code;
        });

        $rows = $grouped->map(function ($group, $slsCode) {
            $kabCode  = Str::substr($slsCode, 0, 4);
            $kecCode  = Str::substr($slsCode, 0, 7);
            $desaCode = Str::substr($slsCode, 0, 10);
            return [
                $kabCode,
                $this->kabkotLabels[$kabCode] ?? '-',
                $kecCode,
                $this->kecLabels[$kecCode] ?? '-',
                $desaCode,
                $this->desaLabels[$desaCode] ?? '-',
                $slsCode,
                $this->slsLabels[$slsCode] ?? '-',
                $group->sum('open'),
                $group->sum('draft'),
                $group->sum('submitted_p'),
                $group->sum('submitted_r'),
                $group->sum('approved'),
                $group->sum('rejected'),
                $group->sum('revoked'),
                $group->sum('completed'),
            ];
        })->sortBy(fn($row) => $row[6])->values();

        return $rows;
    }

    // Kolom A (kab), C (kec), E (desa), G (sls) harus string
    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['A', 'C', 'E', 'G'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
