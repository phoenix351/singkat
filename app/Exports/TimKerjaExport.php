<?php

namespace App\Exports;

use App\Models\ManManagement\TimKerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TimKerjaExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $tahun;
    protected $label;

    public function __construct($tahun = null)
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        //
        $query = TimKerja::query();
        $query->select(['tahun', 'label']);
        if ($this->tahun !== 'null') $query->where('tahun', 'like', '%' . $this->tahun . '%');
        $tim = $query->get();
        return $tim;
    }

    public function headings(): array
    {
        return ['tahun', 'label'];
    }
}
