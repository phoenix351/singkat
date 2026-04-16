<?php

namespace Database\Seeders;

use App\Models\ManManagement\Pegawai;
use App\Models\ManManagement\Satker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManMent_SatkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $satker = Pegawai::select(['organisasi', 'kabupaten'])
            ->distinct()
            ->orderBy('organisasi', 'asc')
            ->get()
            ->map(function ($item) {
                $kode_satker = substr($item->organisasi, 0, 4);
                return [
                    'kode_satker' => $kode_satker,
                    'nama_satker' => $item->kabupaten,
                ];
            })
            ->unique('kode_satker')
            ->values()
            ->toArray();
        foreach ($satker as $key => $value) {
            # code...
            Satker::create($value);
        }
    }
}
