<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitKerjas = [
            "BPS Prov. Sulawesi Utara",
            "BPS Kab. Minahasa",
            "BPS Kab. Minahasa Selatan",
            "BPS Kab. Minahasa Utara",
            "BPS Kab. Bolaang Mongondow",
            "BPS Kab. Bolaang Mongondow Timur",
            "BPS Kab. Bolaang Mongondow Utara",
            "BPS Kab. Siau Tagulandang Biaro",
            "BPS Kota Kotamobagu",
            "BPS Kab.Kepl.Sangihe",
            "BPS Kab.Kepl.Talaud",
            "BPS Kota Bitung",
            "BPS Kota Tomohon",
            "BPS Kota Manado",
        ];

        foreach ($unitKerjas as $unitKerja) {
            \App\Models\UnitKerja::create([
                'nama' => $unitKerja,
            ]);
        }
    }
}
