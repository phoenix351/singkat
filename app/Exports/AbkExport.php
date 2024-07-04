<?php

namespace App\Exports;

use App\Models\Abk;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AbkExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $unitKerjaIds;
    protected $showAllJabatan;

    public function __construct(array $unitKerjaIds = null, bool $showAllJabatan = false)
    {
        $this->unitKerjaIds = $unitKerjaIds;
        $this->showAllJabatan = $showAllJabatan;
    }

    public function array(): array
    {
        $data = [];
        $jabatanList = Jabatan::all();
        $unitKerjaList = UnitKerja::whereIn('id', $this->unitKerjaIds)->get();
        $number = 1;

        foreach ($jabatanList as $jabatan) {
            $row = [$number++, $jabatan->nama];
            $hasNonZeroAbk = false;

            foreach ($unitKerjaList as $unitKerja) {
                $abk = Abk::where('jabatan_id', $jabatan->id)
                    ->where('unit_kerja_id', $unitKerja->id)
                    ->first();

                $abkValue = $abk ? $abk->abk : '';
                $eksistingValue = $abk ? $abk->eksisting : '';
                $kebutuhanPegawaiValue = $abk ? $abk->kebutuhan_pegawai : '';

                if ($abkValue !== '' && $abkValue !== 0) {
                    $hasNonZeroAbk = true;
                }

                $row[] = $abkValue;
                $row[] = $eksistingValue;
                $row[] = $kebutuhanPegawaiValue;
            }

            if ($this->showAllJabatan || $hasNonZeroAbk) {
                $data[] = $row;
            } else {
                $number--; // Decrement number if row is not added
            }
        }

        // Add totals row
        $totals = ["", "Jumlah"];
        $abkSums = array_fill(0, count($unitKerjaList), 0);
        $eksistingSums = array_fill(0, count($unitKerjaList), 0);
        $kebutuhanSums = array_fill(0, count($unitKerjaList), 0);

        foreach ($data as $row) {
            for ($i = 0; $i < count($unitKerjaList); $i++) {
                $abkSums[$i] += is_numeric($row[2 + ($i * 3)]) ? $row[2 + ($i * 3)] : 0;
                $eksistingSums[$i] += is_numeric($row[3 + ($i * 3)]) ? $row[3 + ($i * 3)] : 0;
                $kebutuhanSums[$i] += is_numeric($row[4 + ($i * 3)]) ? $row[4 + ($i * 3)] : 0;
            }
        }

        for ($i = 0; $i < count($unitKerjaList); $i++) {
            $totals[] = $abkSums[$i];
            $totals[] = $eksistingSums[$i];
            $totals[] = $kebutuhanSums[$i];
        }

        $data[] = $totals;

        return $data;
    }


    public function headings(): array
    {
        $unitKerjaList = UnitKerja::whereIn('id', $this->unitKerjaIds)->get();
        $headings = ["No", 'Jabatan'];

        foreach ($unitKerjaList as $unitKerja) {
            $headings[] = $unitKerja->nama;
            $headings[] = '';
            $headings[] = '';
        }

        $subHeadings = ['', ""];
        foreach ($unitKerjaList as $unitKerja) {
            $subHeadings[] = 'ABK';
            $subHeadings[] = 'Eksisting';
            $subHeadings[] = 'Kebutuhan Pegawai';
        }

        return [$headings, $subHeadings];
    }

    public function title(): string
    {
        return 'ABK';
    }

    public function styles(Worksheet $sheet)
    {
        $unitKerjaList = UnitKerja::whereIn('id', $this->unitKerjaIds)->get();
        $colors = [
            'FFC7CE', // Light red
            'FFEB9C', // Light yellow
            'C6EFCE', // Light green
            'D9EAD3', // Light blue
            'F4CCCC', // Light pink
            'D0E0E3', // Light gray
        ];

        // Merge cells for the heading "Jabatan"
        $sheet->mergeCells('B1:B2');
        // Set height and width for the heading "Jabatan"
        $sheet->getRowDimension(1)->setRowHeight(30); // Set height
        $sheet->getRowDimension(2)->setRowHeight(30); // Set height
        $sheet->getColumnDimension('B')->setWidth(50); // Set width
        // $sheet->getStyle('A1')->getAlignment()->setWrapText(true); // Wrap text for the heading "Jabatan"

        // Set style for the heading "No"
        $sheet->mergeCells('A1:A2');
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(5);


        // Merge unit kerja headers and set colors
        $columnIndex = 3; // Start from column C
        foreach ($unitKerjaList as $index => $unitKerja) {
            $startColumn = $this->getColumnByIndex($columnIndex);
            $endColumn = $this->getColumnByIndex($columnIndex + 2);
            $sheet->mergeCells("{$startColumn}1:{$endColumn}1");
            $sheet->setCellValue("{$startColumn}1", $unitKerja->nama);

            // Set height for unit kerja headers
            $sheet->getRowDimension(1)->setRowHeight(30); // Set height

            // Set color for unit kerja columns
            $color = $colors[$index % count($colors)];
            $sheet->getStyle("{$startColumn}1:{$endColumn}2")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => $color],
                ],
            ]);

            $columnIndex += 3;
        }

        // Set sub-headings for ABK, Eksisting, Kebutuhan Pegawai
        $subHeadings = ['ABK', 'Eksisting', 'Kebutuhan Pegawai'];
        $columnIndex = 3; // Start from column C
        foreach ($unitKerjaList as $unitKerja) {
            foreach ($subHeadings as $subHeading) {
                $column = $this->getColumnByIndex($columnIndex);
                $sheet->setCellValue("{$column}2", $subHeading);
                // Set width for each sub-heading
                if ($subHeading == 'ABK' || $subHeading == 'Eksisting') {
                    $sheet->getColumnDimension($column)->setWidth(8); // Width 8 for ABK and Eksisting
                } else if ($subHeading == 'Kebutuhan Pegawai') {
                    $sheet->getColumnDimension($column)->setWidth(18); // Width 18 for Kebutuhan Pegawai
                }
                $columnIndex++;
            }
        }

        // Apply border to all cells
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Freeze the first column and first two rows
        $sheet->freezePane('C3');

        // Merge cells for the totals row
        $sheet->mergeCells("A" . ($highestRow + 1) . ":B" . ($highestRow + 1));
        $sheet->getStyle("A" . ($highestRow + 1) . ":B" . ($highestRow + 1))->getFont()->setBold(true);
        $sheet->getStyle("A" . ($highestRow + 1) . ":B" . ($highestRow + 1))->getAlignment()->setHorizontal('center')->setVertical('center');

        // Apply styles to headers
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Center align vertically for heading "Jabatan"
                ],
            ],
            2 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center align horizontally for unit kerja headers
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Center align vertically for unit kerja headers
                ],
            ],
            "C3:AR55" => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center align horizontally for sub-headings
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Center align vertically for sub-headings
                ],
            ],
        ];
    }


    private function getColumnByIndex($index)
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = (int)(($index - $mod) / 26);
        }
        return $letter;
    }
}
