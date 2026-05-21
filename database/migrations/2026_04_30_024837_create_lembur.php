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

        Schema::create('lembur', function (Blueprint $table) {
            $table->id();
            $table->string('maksud_lembur');
            $table->foreignId('tim_id')
                ->nullable()
                ->constrained('sulutweb_man_management.timkerja')
                ->onUpdate('restrict')
                ->onDelete('set null');
            $table->foreignId('spkl_id')
                ->nullable()
                ->constrained('sulutweb_simple.spkl')
                ->onUpdate('restrict')
                ->onDelete('set null');
            $table->timestamps();
        });
        Schema::create('lembur_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembur_id')
                ->nullable()
                ->constrained('sulutweb_simple.lembur')
                ->onUpdate('restrict')
                ->onDelete('cascade');
            $table->foreignId('pegawai_id')
                ->nullable()
                ->constrained('sulutweb_man_management.pegawai')
                ->onUpdate('restrict')
                ->onDelete('set null');
            $table->date('tanggal');
            $table->float('jumlah_jam')->default(1);
            $table->time('jam_berangkat')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->string('status', 30)->comment('1=pending, 2=ditolak, 3=setuju, 4=proses, 5=selesai')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembur_pegawai');
        Schema::dropIfExists('lembur');
        Schema::dropIfExists('spkl');
    }
};
