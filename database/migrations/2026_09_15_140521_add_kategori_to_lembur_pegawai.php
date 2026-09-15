<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sulutweb_simple';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lembur_pegawai', function (Blueprint $table) {
            $table->string('kategori', 5)->nullable()->after('jam_pulang')->comment('K=Kerja, S=Sabtu, M=Minggu, L=Libur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembur_pegawai', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
