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

class WilayahKecSheet extends DefaultValueBinder implements
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

    public function __construct(Collection $fasih, $kabkot, $kabkotLabels, $kecLabels)
    {
        $this->fasih        = $fasih;
        $this->kabkot       = $kabkot;
        $this->kabkotLabels = $kabkotLabels;
        $this->kecLabels    = $kecLabels;
    }

    public function title(): string
    {
        return 'Kecamatan';
    }

    public function headings(): array
    {
        return [
            'Kode Kab/Kota',
            'Nama Kab/Kota',
            'Kode Kecamatan',
            'Nama Kecamatan',
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
        // Grup berdasarkan 7 digit pertama (kode kec)
        $grouped = $this->fasih->groupBy(function ($item) {
            return Str::substr((string) $item->subsls_code, 0, 7);
        });

        $rows = $grouped->map(function ($group, $kecCode) {
            $kabCode = Str::substr($kecCode, 0, 4);
            return [
                $kabCode,
                $this->kabkotLabels[$kabCode] ?? '-',
                $kecCode,
                $this->kecLabels[$kecCode] ?? '-',
                $group->sum('open'),
                $group->sum('draft'),
                $group->sum('submitted_p'),
                $group->sum('submitted_r'),
                $group->sum('approved'),
                $group->sum('rejected'),
                $group->sum('revoked'),
                $group->sum('completed'),
            ];
        })->sortBy(fn($row) => $row[2])->values();

        return $rows;
    }

    // Kolom A (kode kab) dan C (kode kec) harus string agar leading zero tidak hilang
    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['A', 'C'])) {
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
        ];
    }
}
