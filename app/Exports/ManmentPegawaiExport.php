<?php

namespace App\Exports;

use App\Models\ManManagement\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ManmentPegawaiExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, WithCustomValueBinder
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $kabupaten;

    public function __construct($kabupaten = null)
    {
        $this->kabupaten = $kabupaten;
    }

    public function collection()
    {
        //
        $query = Pegawai::query();
        $query->select(['nip_lama', 'nip', 'username', 'email', 'name', 'golongan', 'jabatan', 'provinsi', 'kabupaten']);
        if ($this->kabupaten !== 'null') $query->where('kabupaten', 'like', '%' . $this->kabupaten . '%');
        $pegawai = $query
            ->get();
        return $pegawai;
    }

    public function headings(): array
    {
        return [
            'NIP Lama',
            'NIP',
            'Username',
            'Email',
            'Nama Pegawai',
            'Golongan',
            'Jabatan',
            'Provinsi',
            'Kabupaten/Kota'
        ];
    }

    public function map($row): array
    {
        return [
            (string) $row->nip_lama,
            (string) $row->nip,
            (string) $row->username,
            (string) $row->email,
            (string) $row->name,
            (string) $row->golongan,
            (string) $row->jabatan,
            (string) $row->provinsi,
            (string) $row->kabupaten,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        return true;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
        ];

        // throw new \Exception('Not implemented');
    }
}
