<?php

namespace Database\Seeders;

use App\Models\AngkaKreditHistory;
use App\Models\Pegawai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pegawais = Pegawai::all();
        $histories = [];
        foreach ($pegawais as $pegawai) {
            $histories[] = ['pegawai_id' => $pegawai->id, 'akumulasi_ak' => $pegawai->akumulasi_ak ?? 0, 'created_at' => Date::now(), 'updated_at' => Date::now()];
        }
        AngkaKreditHistory::insert($histories);
    }
}
