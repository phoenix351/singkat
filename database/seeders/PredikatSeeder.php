<?php

namespace Database\Seeders;

use App\Models\Predikat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PredikatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $predikats = [
            ['nama' => 'Sangat Baik', 'nilai' => 1.5],
            ['nama' => 'Baik', 'nilai' => 1],
            ['nama' => 'Cukup/Butuh Perbaikan', 'nilai' => 0.75],
            ['nama' => 'Kurang', 'nilai' => 0.5],
            ['nama' => 'Sangat Kurang', 'nilai' => 0.25],
        ];

        Predikat::insert($predikats);
    }
}
