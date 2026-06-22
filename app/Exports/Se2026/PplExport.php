<?php

namespace App\Exports\Se2026;

use App\Models\Se2026\DataFasih;
use App\Models\Se2026\DataFasihPml;
use App\Models\Se2026\Pml;
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
    protected $tab;

    public function __construct($kabkot, $kec, $desa, $sls, $nama, $tab)
    {
        $this->kabkot = $kabkot;
        $this->kec = $kec;
        $this->desa = $desa;
        $this->sls = $sls;
        $this->nama = $nama;
        $this->tab = $tab;
    }
    public function collection()
    {
        //
        $kabkot = $this->kabkot ?? null;
        $kec = $this->kec ?? null;
        $desa = $this->desa ?? null;
        $sls = $this->sls ?? null;
        $nama = $this->nama ?? null;
        $tab = $this->tab ?? null;

        if ($tab == 'ppl') {
            $query = DataFasih::query()
                ->from('sulutweb_se2026.data_fasih as sdf')
                ->leftJoin('sulutweb_se2026.ppl as pt', 'pt.email', '=', 'sdf.email');
        } else {
            $query = DataFasihPml::query()
                ->from('sulutweb_se2026.data_fasih_pml as sdf')
                ->leftJoin('sulutweb_se2026.pml as pt', 'pt.email', '=', 'sdf.email');
        }
        if ($sls)
            $query->where('subsls_code', $sls);
        else if ($desa)
            $query->where('subsls_code', 'like', $desa . '%');
        else if ($kec)
            $query->where('subsls_code', 'like', $kec . '%');
        else if ($kabkot)
            $query->where('subsls_code', 'like', $kabkot . '%');

        if ($nama) {
            $query->where(function ($q) use ($nama) {
                $q->where('pt.nama', 'like', '%' . $nama . '%')
                    ->orWhere('sdf.email', 'like', '%' . $nama . '%');
            });
        }

        $datafasih = $query->select([
            'pt.nama',
            'sdf.email',
            'sdf.subsls_code',
            'sdf.open',
            'sdf.draft',
            'sdf.submitted_p',
            'sdf.approved',
            'sdf.rejected',
            'sdf.submitted_r',
            'sdf.revoked',
            'sdf.completed',
        ])
            ->get()
            ->map(function ($item) {
                $item->nama = $item->nama ?: '-';
                return $item;
            });
        return $datafasih;
    }

    public function headings(): array
    {
        return [
            "Nama",
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
        if ($cell->getColumn() === 'C') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT
        ];
    }
}
