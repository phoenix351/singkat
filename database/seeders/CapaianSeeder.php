<?php

namespace Database\Seeders;

use App\Models\Capaian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CapaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $capaians = [
            ['pegawai_id'=>5,'tahun_bulan'=>'202401','predikat'=>1],
            ['pegawai_id'=>5,'tahun_bulan'=>'202402','predikat'=>2],
            ['pegawai_id'=>5,'tahun_bulan'=>'202403','predikat'=>2],
            ['pegawai_id'=>5,'tahun_bulan'=>'202404','predikat'=>1],
        ];
        Capaian::insert($capaians);
    }
}
