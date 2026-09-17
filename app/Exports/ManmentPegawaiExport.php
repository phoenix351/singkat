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
    protected $searchField;
    protected $listColumn;
    protected $sortField;
    protected $sortOrder;
    protected $kabupaten;

    public function __construct($param = null, $listColumn = null)
    {
        if ($param instanceof \Illuminate\Http\Request) {
            $this->searchField = $param->input('searchField');
            $this->listColumn = $param->input('listColumn', []);
            $this->sortField = $param->input('sortField');
            $this->sortOrder = $param->input('sortOrder');
            $this->kabupaten = $param->input('kabupaten');
        } elseif (is_array($param)) {
            $this->searchField = $param['searchField'] ?? null;
            $this->listColumn = $param['listColumn'] ?? [];
            $this->sortField = $param['sortField'] ?? null;
            $this->sortOrder = $param['sortOrder'] ?? null;
            $this->kabupaten = $param['kabupaten'] ?? null;
        } else {
            $this->searchField = $param;
            $this->kabupaten = $param;
            $this->listColumn = is_array($listColumn) ? $listColumn : [];
        }

        $req = request();
        if ($req instanceof \Illuminate\Http\Request) {
            if ($this->searchField === null || $this->searchField === '' || $this->searchField === 'null') {
                $this->searchField = $req->input('searchField');
            }
            if (empty($this->listColumn)) {
                $this->listColumn = $req->input('listColumn', []);
            }
            if ($this->sortField === null) {
                $this->sortField = $req->input('sortField');
            }
            if ($this->sortOrder === null) {
                $this->sortOrder = $req->input('sortOrder');
            }
            if ($this->kabupaten === null || $this->kabupaten === '' || $this->kabupaten === 'null') {
                $this->kabupaten = $req->input('kabupaten');
            }
        }
    }

    public function collection()
    {
        $query = Pegawai::query();
        $query->select(['id', 'nip_lama', 'nip', 'username', 'email', 'name', 'golongan', 'jabatan', 'provinsi', 'kabupaten', 'organisasi']);

        if (!empty($this->sortOrder)) {
            $order = $this->sortOrder == 1 ? 'asc' : 'desc';
            if ($this->sortField === 'satker') {
                $query->orderBy('organisasi', $order);
            } elseif (!empty($this->sortField)) {
                $query->orderBy($this->sortField, $order);
            }
        } else {
            $query->orderBy('organisasi', 'asc')
                ->orderBy('name', 'asc');
        }

        $search = $this->searchField;
        if (($search === null || $search === '' || $search === 'null') && ($this->kabupaten !== null && $this->kabupaten !== '' && $this->kabupaten !== 'null')) {
            $search = $this->kabupaten;
        }

        if ($search !== null && $search !== '' && $search !== 'null') {
            $columns = !empty($this->listColumn) && is_array($this->listColumn)
                ? array_values($this->listColumn)
                : ['nip_lama', 'nip', 'username', 'email', 'name', 'golongan', 'jabatan', 'satker', 'kabupaten'];

            $validColumns = [];
            foreach ($columns as $col) {
                if ($col === 'satker' || in_array($col, ['nip_lama', 'nip', 'username', 'email', 'name', 'golongan', 'jabatan', 'provinsi', 'kabupaten', 'organisasi'])) {
                    $validColumns[] = $col;
                }
            }

            if (empty($validColumns)) {
                $validColumns = ['nip_lama', 'nip', 'username', 'email', 'name', 'golongan', 'jabatan', 'satker', 'kabupaten'];
            }

            $query->where(function ($q) use ($search, $validColumns) {
                foreach ($validColumns as $key => $value) {
                    if ($key === 0) {
                        if ($value === 'satker') {
                            $q->where('organisasi', 'like', '%' . $search . '%')
                                ->orWhere('kabupaten', 'like', '%' . $search . '%');
                        } else {
                            $q->where($value, 'like', '%' . $search . '%');
                        }
                    } else {
                        if ($value === 'satker') {
                            $q->orWhere('organisasi', 'like', '%' . $search . '%')
                                ->orWhere('kabupaten', 'like', '%' . $search . '%');
                        } else {
                            $q->orWhere($value, 'like', '%' . $search . '%');
                        }
                    }
                }
            });
        }

        $pegawai = $query->get();
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
