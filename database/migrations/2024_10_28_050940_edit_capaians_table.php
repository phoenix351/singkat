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
        Schema::table('capaians', function (Blueprint $table) {
            $table->integer('bulan_mulai', false)->nullable();
            $table->integer('bulan_akhir', false)->nullable();
            $table->string('path', 255)->nullable();
            $table->dropUnique(['pegawai_id', 'tahun', 'periode']);
            $table->string('jabatan_id')->nullable();
            $table->float('angka_kredit_dasar', 3)->nullable();
        });
        Capaian::where('periode', 'like', '%Semester 1%')->update([
            'bulan_mulai' => 1,
            'bulan_akhir' => 6,
        ]);
        $capaians = Capaian::get();
        foreach ($capaians as $capaian) {
            # code...
            $capaian->jabatan_id = Pegawai::find($capaian->pegawai_id)->jabatan_id;
            $capaian->angka_kredit_dasar = Pegawai::find($capaian->pegawai_id)->akumulasi_ak;
            $capaian->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capaians', function (Blueprint $table) {
            $table->dropColumn('bulan_mulai');
            $table->dropColumn('bulan_akhir');
            $table->dropColumn('path');
            $table->unique(['pegawai_id', 'tahun', 'periode']);
        });
    }
};
