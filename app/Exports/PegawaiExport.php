<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PegawaiExport  implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    protected $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Menentukan koleksi data yang akan diekspor.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if (Pegawai::count() === 0) {
            return collect([array_fill_keys($this->columns, null)]);
        }

        // return Pegawai::select($this->columns)
        //     ->orderByRaw('CASE 
        //                 WHEN unit_kerja = "BPS Prov. Sulawesi Utara" THEN 1 
        //                 ELSE 2 
        //               END')
        //     ->orderBy('unit_kerja', 'asc')
        //     ->orderByRaw('CASE 
        //                 WHEN unit_kerja = "BPS Prov. Sulawesi Utara" AND jabatan = "Kepala BPS Provinsi" THEN 1
        //                 WHEN unit_kerja = "BPS Prov. Sulawesi Utara" AND jabatan = "Kepala Bagian Umum" THEN 2
        //                 WHEN jabatan = "Kepala BPS Kabupaten/Kota" THEN 3
        //                 WHEN jabatan = "Kepala Subbagian Umum" THEN 4
        //                 ELSE 5
        //               END')
        //     // ->orderBy('jabatan', 'asc')
        //     ->get();
        // dd($this->columns);
        $data = Pegawai::select($this->columns)
        ->leftJoin('jabatan','pegawai.jabatan_id','jabatan.id')
        ->orderByRaw('CASE 
                    WHEN unit_kerja = "BPS Prov. Sulawesi Utara" THEN 1 
                    ELSE 2 
                  END')
        ->orderBy('unit_kerja', 'asc')

        // ->orderBy('jabatan', 'asc')
        ->get();
        // dd($data[0]);
        return  $data;
    }

    /**
     * Menentukan heading kolom untuk file Excel.
     *
     * @return array
     */

    public function columnWidths(): array
    {
        $defaultWidths = [
            'id' => 4.11,
            'nip_bps' => 11.67,
            'nip' => 21.56,

            'jabatan' => 27.00,
            'unit_kerja' => 33.89,
            'pangkat_golongan_ruang' => 22.89,
            'angka_kredit_konvensional' => 16.33,
            'angka_kredit_integrasi' => 16.33,
            'predikat_kinerja' => 16.33,
            'tambahan_ijazah' => 16.33,
            'akumulasi_ak' => 16.33,
            'ijazah_terakhir' => 16.33,
            'tmt_pensiun' => 16.33,
            'usia' => 11,
        ];

        $widths = [];

        foreach (['no'] + $this->columns as $index => $column) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $widths[$columnLetter] = $defaultWidths[$column] ?? 10; // Default width if column not found
        }

        return $widths;
    }

    public function headings(): array
    {
        return array_merge(['No'], array_map(function ($column) {
            $column = explode(" ",$column);

            return ucfirst(str_replace('_', ' ', $column[count($column)-1]));
        }, $this->columns));
    }

    public function map($pegawai): array
    {
        static $number = 0;
        $number++;
        // dd($pegawai);
        return array_merge([$number], array_map(function ($column) use ($pegawai) {
            $column = explode(" ",$column);
            return $pegawai->{$column[count($column)-1]};
        }, $this->columns));
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(44);

        // Apply styles to the header row
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Apply border to all data cells
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply styles to all data cells
        foreach (range(2, $highestRow) as $row) {
            foreach (range('A', $highestColumn) as $col) {
                $cellCoordinate = $col . $row;

                $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col) - 1;
                // $columnName = $this->columns[$columnIndex];
                $columnName = $columnIndex === 0 ? 'no' : $this->columns[$columnIndex - 1];

                $alignment = [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ];

                // Customize alignment for specific columns
                if (in_array($columnName, ['nip_bps', 'nip', 'nama', 'jabatan'])) {
                    $alignment['horizontal'] = Alignment::HORIZONTAL_LEFT;
                }

                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'alignment' => $alignment,
                ]);

                // Ensure NIP fields are treated as text
                if (in_array($columnName, ['nip_bps', 'nip'])) {
                    $sheet->getCell($cellCoordinate)->setDataType(DataType::TYPE_STRING);
                }
            }
        }

        return [];
    }
}
