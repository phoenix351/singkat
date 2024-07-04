<?php

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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip_bps');
            $table->string('nip');
            $table->string('nama');
            $table->string('jabatan');
            $table->string('unit_kerja');
            $table->string('pangkat_golongan_ruang');
            $table->string('angka_kredit_konvensional')->nullable();
            $table->string('angka_kredit_integrasi')->nullable();
            $table->string('predikat_kinerja')->nullable();
            $table->string('tambahan_ijazah')->nullable();
            $table->string('akumulasi_ak')->nullable();
            $table->string('ijazah_terakhir')->nullable();
            $table->string('tmt_pensiun')->nullable();
            $table->string('usia')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
