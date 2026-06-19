<?php

namespace App\Exports\Se2026;

use App\Models\Se2026\DataFasih;
use App\Models\Se2026\Ppl;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PplExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithColumnFormatting, WithCustomValueBinder
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $kabkot;
    protected $kec;
    protected $desa;
    protected $sls;
    protected $nama;

    public function __construct($kabkot, $kec, $desa, $sls, $nama)
    {
        $this->kabkot = $kabkot;
        $this->kec = $kec;
        $this->desa = $desa;
        $this->sls = $sls;
        $this->nama = $nama;
    }
    public function collection()
    {
        //
        $kabkot = $this->kabkot ?? null;
        $kec = $this->kec ?? null;
        $desa = $this->desa ?? null;
        $sls = $this->sls ?? null;
        $nama = $this->nama ?? null;

        $query = DataFasih::query();
        if ($sls)
            $query->where('subsls_code', $sls);
        else if ($desa)
            $query->where('subsls_code', 'like', $desa . '%');
        else if ($kec)
            $query->where('subsls_code', 'like', $kec . '%');
        else if ($kabkot)
            $query->where('subsls_code', 'like', $kabkot . '%');

        if ($nama) {
            $email_selected = Ppl::where(function ($q) use ($nama) {
                $q->where('nama', 'like', '%' . $nama . '%')
                    ->orWhere('email', 'like', '%' . $nama . '%');
            })->pluck('email')->toArray();
            $query->whereIn('email', $email_selected);
        }

        $datafasih = $query->select([
            'email',
            'subsls_code',
            'open',
            'draft',
            'submitted_p',
            'approved',
            'rejected',
            'submitted_r',
            'revoked',
            'completed',
        ])
            ->get();
        return $datafasih;
    }

    public function headings(): array
    {
        return [
            "Email",
            "Kode Identitas",
            "OPEN",
            "DRAFT",
            "SUBMITTED BY PENCACAH",
            "APPROVED BY PENGAWAS",
            "REJECTED BY PENGAWAS",
            "SUBMITTED RESPONDENT",
            "REVOKED BY PENGAWAS",
            "COMPLETED BY ADMIN KABUPATEN"
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Cek apakah sel yang sedang diproses berada di kolom B
        if ($cell->getColumn() === 'B') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        // Untuk kolom selain B, kembalikan ke aturan default bawaan library
        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT
        ];
    }
}
