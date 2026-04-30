<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = 'sulutweb_simple';
    public function up(): void
    {
        Schema::create('lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')
                ->nullable()
                ->constrained('sulutweb_man_management.pegawai')
                ->onUpdate('restrict')
                ->onDelete('set null');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('nomor_spkl')->nullable();
            $table->string('maksud_lembur');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('sulutweb_man_management.pegawai')
                ->onUpdate('restrict')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembur');
    }
};
