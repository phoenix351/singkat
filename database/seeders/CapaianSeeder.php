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
            ['pegawai_id'=>5, 'periode'=>'semester 1', 'tahun'=>'2024','predikat_id'=>1],
            ['pegawai_id'=>5, 'periode'=>'semester 2', 'tahun'=>'2024','predikat_id'=>2],
            ['pegawai_id'=>5, 'periode'=>'tahunan', 'tahun'=>'2023','predikat_id'=>2],
            ['pegawai_id'=>5, 'periode'=>'tahunan', 'tahun'=>'2022','predikat_id'=>1],
            ['pegawai_id'=>5, 'periode'=>'tahunan', 'tahun'=>'2021','predikat_id'=>1],
        ];
        foreach ($capaians as $value) {
            $capaian = new Capaian($value);
            $capaian->save();
        }
    }
}
