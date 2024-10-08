<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Imports\PegawaiImport;
use App\Imports\UserImport;

class PegawaiSeeder extends Seeder
{
    public function run()
    {
        Excel::import(new PegawaiImport, storage_path('app/public/database-pegawai-sulut.xlsx'));
        Excel::import(new UserImport, storage_path('app/public/database-pegawai-sulut.xlsx'));
    }
}
