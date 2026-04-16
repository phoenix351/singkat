<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = 'sulutweb_man_management';

    public function up(): void
    {
        Schema::create('keanggotaan_timkerja', function (Blueprint $table) {
            $table->id();

            // Gunakan foreignId agar tipe datanya otomatis Unsigned Big Integer
            $table->foreignId('tim_id')
                ->nullable()
                ->constrained('timkerja') // Nama tabel referensi
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->foreignId('pegawai_id')
                ->nullable()
                ->constrained('pegawai')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->string('keanggotaan', 10)->default('anggota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keanggota_timkerja');
    }
};
