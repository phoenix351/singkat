<?php

use App\Models\Capaian;
use App\Models\Pegawai;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $capaians = Capaian::get();
        foreach ($capaians as $capaian) {
            # code...
            $capaian->angka_kredit_dasar = Pegawai::find($capaian->pegawai_id)->akumulasi_ak;
            $capaian->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
