<?php

namespace App\Imports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;


class PegawaiImport implements ToModel, ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private $currentRow = 0;

    public function collection(Collection $collection)
    {
    }
    public function model(array $row)
    {

        $this->currentRow++;

        if ($this->currentRow > 1 && $this->currentRow < 373) {

            // dd($row);

            return new Pegawai([
                'nip_bps'           => $row[1],
                'nip'           => $row[2],
                'nama'          => $row[3],
                'jabatan'       => $row[4],
                "unit_kerja" => $row[5],
                'pangkat_golongan_ruang'      => $row[6],
                "angka_kredit_konvensional" => $row[7],
                "angka_kredit_integrasi" => $row[8],
                "predikat_kinerja" => $row[9],
                "tambahan_ijazah" => $row[10],
                "akumulasi_ak" => $row[11],
                'ijazah_terakhir' => $row[12],
                'tmt_pensiun' => $row[13],
                'usia' => $row[14],
            ]);
        } else {
            return null;
        }
    }
}
