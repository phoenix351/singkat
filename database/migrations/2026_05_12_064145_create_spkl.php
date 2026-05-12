<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    protected $connection = 'sulutweb_simple';
    public function up(): void
    {
        Schema::create('spkl', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_spkl', 150);
            $table->tinyInteger('bulan');
            $table->year('tahun');
            $table->year('tahun_dipa');
            $table->date('tanggal_pengajuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spkl');
    }
};
