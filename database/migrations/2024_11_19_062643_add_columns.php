<?php

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
        Schema::table('pegawai', function (Blueprint $table) {
            $table->date('bulan_mulai')->nullable();
            $table->date('bulan_selesai')->nullable();
            $table->dropColumn('predikat_kinerja');
            $table->integer('predikat_id')->nullable();
            $table->float('angka_kredit_dasar')->default(0);
        });

        $pegawais = Pegawai::get();
        foreach ($pegawais as $pegawai) {
            # code...
            $pegawai->angka_kredit_dasar = $pegawai->akumulasi_ak ? $pegawai->akumulasi_ak : 0;
            $pegawai->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('bulan_mulai');
            $table->dropColumn('bulan_selesai');
            $table->dropColumn('predikat_id');
            $table->string('predikat_kinerja')->nullable();
        });
        //
    }
};
